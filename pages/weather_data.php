<?php
	if (!isset($page)) die();
	require_once('pagination.class.php');
	
	function pageTitle() {
		echo 'Weather Data';
	}
	
	function display() {
		global $connection;
		$node = ((isset($_POST['node']) && is_numeric($_POST['node'])) ? $_POST['node'] : ((isset($_GET['node']) && is_numeric($_GET['node'])) ? $_GET['node'] : 0));
	?>
		<form action="index.php?page=weather_data" name="graph_form" method="POST">
			<select name="node" style="width: 205px;">
				<?php
				$node_result = mysqli_query($connection, "SELECT * FROM node ORDER BY location ASC");
				while ($row = mysqli_fetch_assoc($node_result)) {
					echo '<option '.(($node == $row['node_id'])? 'selected="selected"':'').' value="' . $row['node_id'] . '">' . $row['location'] . '</option>';
				}?>
			</select>
			<input type="submit" name="view_data" value="View Data" />
		</form><br />
	<?php
		if ($node > 0) {
			$pagination = new pagination('index.php?page=weather_data&node='.$node);
			$pagination->setMax(25);
			$pagination->setData('SELECT * FROM weather_data WHERE node_id = '.$node);
			if ($pagination->totalpages > 0) {
				echo '<table border="0" width="100%">';
				echo '	
					<tr>
						<th align="left">Time</th>
						<th align="left">Inside Max</th>
						<th align="left">Outside Max</th>
						<th align="left">Humidity</th>
					</tr>
				
				';
				while ($row = mysqli_fetch_array($pagination->sql, MYSQL_ASSOC))   {
					echo '<tr>';
					echo '<td>'.$row['time_stamp'].'</td>';
					echo '<td>'.$row['inside_max'].'</td>';
					echo '<td>'.$row['outside_max'].'</td>';
					echo '<td>'.$row['humidity_max'].'</td>';
					echo '</tr>';
				}
				echo '</table><br />';
				$pagination->displayLinks(20); 
			} else {
				echo 'No weather data available for this node';
			}
		}
	}
?>