<?php
	if (!isset($page)) die();
	require_once('pagination.class.php');
	
	function pageTitle() {
		echo 'Security Log';
	}
	
	function display() {
		global $connection;
		$pagination = new pagination('index.php?page=security_log');
		$pagination->setMax(25);
		$pagination->setData("SELECT ip_address, COUNT(ip_address) As login_count, report_datetime, time_to_sec(timediff(now(), report_datetime)) As seconds FROM access_log GROUP BY ip_address ORDER BY report_datetime DESC");
		if ($pagination->count_all > 0) {
			echo '	<table border="0" width="600px" style="text-align: left;">
					<tr>
						<th>IP Address</th>
						<th>Count (Attempts)</th>
						<th>Date Time</th>
						<th>Last Attempt (in seconds)</th>
					</tr>';
			$pagination->show_all();
			
			echo '</table>';
		} else {
			echo "No security data available to display";
		}
	}
?>