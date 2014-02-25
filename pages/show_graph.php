<?php
	if (!isset($page)) die();
	function pageTitle() {
		echo "Generate Graph";
	}
	
	function display() {
		global $connection;
		if (isset($_POST['submit']) && isset($_POST['node']) && is_numeric($_POST['node']) && isset($_POST['start_date']) && isset($_POST['end_date']) && isset($_POST['data_type'])) {
			$node_result = mysqli_query($connection, "SELECT * FROM node WHERE node_id = {$_POST['node']} LIMIT 1");
			if (mysqli_num_rows($node_result) > 0) {
				$node = mysqli_fetch_array($node_result, MYSQLI_ASSOC);
				$date_range = " data.time_stamp BETWEEN '".date('Y-m-d', strtotime($_POST['start_date']))."' AND '".date('Y-m-d', strtotime($_POST['end_date']))."'";
				$peek_result = mysqli_query($connection, "SELECT DAY(data.time_stamp) As node_day FROM ".(($_POST['data_type'] == 1) ?'node':'weather')."_data As data WHERE data.node_id = {$node['node_id']} AND ".$date_range." GROUP BY DAY(data.time_stamp)");
				if (mysqli_num_rows($peek_result) > 0) {
					$search_type = (((mysqli_num_rows($peek_result)) > 1)?'DATE(data.time_stamp)':'data.time_stamp');
					echo '
					<script language="javascript" type="text/javascript">
					$(document).ready(function() { 
						var arr_series = new Array();
						var arr_value = new Array();';
					if ($_POST['data_type'] == 1) {
						$node_data_result = mysqli_query($connection, "SELECT {$search_type} AS node_date, bank.bank_id, AVG(charge_current) AS charge_current
															FROM node_data AS data INNER JOIN bank_data AS bank ON bank.node_data_id = data.node_data_id
															WHERE data.node_id = {$node['node_id']} AND {$date_range} GROUP BY bank.bank_id, node_date ORDER BY node_date ASC");
						if (mysqli_num_rows($node_data_result) > 0) {
							$arr_value = array();
							$interval = ceil(mysqli_num_rows($node_data_result)/288);
							while ($node_data_row = mysqli_fetch_array($node_data_result, MYSQLI_ASSOC)) {
								$time = date('Y-m-d H:i:s', strtotime($node_data_row['node_date']));
								$arr_value[$node_data_row['bank_id']][] = array('time_stamp' => $time, 'charge_current' => round(($node_data_row['charge_current'] / $interval), 2));
							}
							$index = 1;
							foreach($arr_value as $arr_data) {
								echo 'var bank'.$index.' = new Array();';
								foreach($arr_data as $data) {
									echo 'bank'.$index.'.push([\''.$data['time_stamp'].'\','.$data['charge_current'].']);';
								}
								echo 'arr_value.push(bank'.$index.');';
								$index++;
							}
							unset($index);
							$bank_result = mysqli_query($connection, "SELECT * FROM node_bank WHERE node_id = {$node['node_id']} ORDER BY bank_name ASC");
							if (mysqli_num_rows($bank_result) > 0) {
								$index = 0;
								while ($bank_row = mysqli_fetch_array($bank_result, MYSQLI_ASSOC)) {
									if ($index < sizeof($arr_value))
										echo 'arr_series.push({label:\''.$bank_row['bank_name'].'\', showMarker: false});';
									$index++;
								}
								unset($index);
							}
						}
					} else {
						$weather_data_result = mysqli_query($connection, "SELECT {$search_type} AS node_date, AVG(data.outside_max) AS outside_max, AVG(data.inside_max) AS inside_max
															FROM weather_data AS data WHERE data.node_id = {$node['node_id']} AND {$date_range} GROUP BY node_date ORDER BY node_date ASC");
						if (mysqli_num_rows($weather_data_result) > 0) {
							echo 'var outside_temp = new Array();
								  var inside_temp = new Array();';
							while($weather_row = mysqli_fetch_array($weather_data_result, MYSQLI_ASSOC)) {
								echo '
									outside_temp.push([\''.$weather_row['node_date'].'\', '.$weather_row['outside_max'].']);
									inside_temp.push([\''.$weather_row['node_date'].'\', '.$weather_row['inside_max'].']);';
							}
							echo 'arr_value.push(outside_temp);
							      arr_value.push(inside_temp);';
						}
						echo 'arr_series.push({label: \'Outside Temperature\', showMarker: false});
							  arr_series.push({label: \'Inside Temperature\', showMarker: false});';
					}
					echo '	chart = $.jqplot(\'data_graph\', arr_value, {
								legend: {show: true, location: \'nw\'},
								title: \''.$node['location'].'\',
								axes:{xaxis:{renderer:$.jqplot.DateAxisRenderer}},
								seriesDefaults: {lineWidth:4},
								series: arr_series,
								cursor:{zoom:true, showTooltip:false} 
							});
						});
					</script>
					<div id="data_graph" style="height: 500px; width: 700px;"></div>
					<button class="button-reset" onclick="chart.resetZoom()">Reset Zoom</button>';
				} else {
					echo 'No data matched the date range ('.date('Y-m-d', strtotime($_POST['start_date'])).' - '.date('Y-m-d', strtotime($_POST['end_date'])).').';
				}
			}
		}
	}
?>