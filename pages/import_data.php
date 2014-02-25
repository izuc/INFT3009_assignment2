<?php
		require_once("csv_reader.class.php");
		if (!isset($page) || !isLoggedIn()) die();
				
		function pageTitle() {
			echo 'Import CSV Data';
		}

		function display() {
			global $connection;
			if (isset($_POST['submit']) && isset($_POST['node'])) {
				if (isset($_FILES['file_upload'])) {
					$file_upload = $_FILES['file_upload'];
					if (strrchr($file_upload['name'], ".") == '.csv') {
						$csv = new File_CSV_DataSource;
						$csv->load($file_upload['tmp_name']);
						$result = $csv->connect();
						if ($_POST['data_type'] == 1) {
							foreach ($result as $row) {
								if (((sizeof($row) -1) % 2) == 0) {
									mysqli_query($connection, "INSERT INTO node_data(node_id, time_stamp) VALUES({$_POST['node']},'{$row[0]}')");
									unset($row[0]);
									$node_id = mysqli_insert_id($connection);
									if ($node_id > 0) {					
										$index = 0;
										$bank_data = array_chunk($row, 2);
										$bank_query = mysqli_query($connection, "SELECT * FROM node_bank WHERE node_id = {$_POST['node']} ORDER BY bank_id ASC");
										while($bank_result = mysqli_fetch_array($bank_query, MYSQLI_ASSOC)) {
											if ($index <= (sizeof($bank_data)-1)) {
												mysqli_query($connection, "INSERT INTO bank_data(node_data_id, bank_id, battery_voltage, charge_current) VALUES({$node_id}, {$bank_result['bank_id']}, {$bank_data[$index][0]}, {$bank_data[$index][1]})");
											}
											$index++;
										}
									}
								}
							}
						} else {
							$previous_date = '';
							foreach ($result as $row) {
								if (strlen($row[0]) > 0) $previous_date = $row[0]; else $row[0] = $previous_date;
								for($i = 0; $i < (sizeof($row)-1); $i++) {
									if ($row[$i] == '-')
										$row[$i] = str_replace('-', 0.0, $row[$i]);
								}
								mysqli_query($connection, "INSERT INTO weather_data(node_id, time_stamp, inside_min, inside_max, outside_min, outside_max, humidity_min, humidity_max, rainfall, pressure_min, pressure_max, windchill_min, windchill_max, windspeed_min, windspeed_max, gust, forecast, wind_direction) 
										VALUES({$_POST['node']},'".date('Y-m-d H:i:s', strtotime($row[0] . ' '. $row[1]))."',{$row[2]}, {$row[3]}, {$row[4]}, {$row[5]}, {$row[6]}, {$row[7]}, {$row[8]}, {$row[9]}, {$row[10]}, {$row[11]}, {$row[12]}, {$row[13]}, {$row[14]}, {$row[15]}, '{$row[16]}', '{$row[17]}')");
							}
						}
						echo $file_upload['name'] . ' imported';
					} else {
						echo 'File Upload Error';
					}
				}
			}			
			
			$node_result = mysqli_query($connection, "SELECT * FROM node ORDER BY location ASC");
			if (mysqli_num_rows($node_result)) {
				echo '
				<form enctype="multipart/form-data" action="index.php?page=import_data" method="POST">
					<table>
						<tr>
							<td><input name="file_upload" type="file" size="30" /></td>
							<td>
								<select name="node" style="width: 150px;">';
								while ($row = mysqli_fetch_assoc($node_result)) {
									echo '<option value="' . $row['node_id'] . '">' . $row['location'] . '</option>';
								}
								echo '</select>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="radio" name="data_type" value="1" checked="checked" />Node Data <br />
								<input type="radio" name="data_type" value="2" />Weather Data
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input name="submit" type="submit" value="Upload File" />
							</td>
						</tr>
					</table>
				</form>';
			} else {
				echo 'No nodes currently available.';
			}
		}
?>