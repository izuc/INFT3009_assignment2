<?php
	function db_connect() {
		$connection = new mysqli(DB_HOST_NAME, DB_USER_NAME, DB_PASSWORD, DB_NAME);
		if (mysqli_connect_errno()) {
		   printf("Connect failed: %s\n", mysqli_connect_error());
		   exit();
		}
		return $connection;
	}
	
	function isLoggedIn() { // Relatively Safe.
		return (isset($_SESSION['user']) && isset($_SESSION['agent']) && ($_SESSION['agent']  == md5($_SERVER['HTTP_USER_AGENT'])) && ($_SESSION['ip_address']  == md5($_SERVER['REMOTE_ADDR'])));
	}
	
	function reportLoginFailure() {
		global $connection;
		// Adds the unsuccessful attempt into the table; which will be used to determine whether they get locked out (for an acceptable duration) =P TAKE THAT BEN!
		mysqli_query($connection, "INSERT INTO access_log(ip_address, report_datetime, attempted_username, attempted_password) VALUES ('{$_SERVER['REMOTE_ADDR']}', NOW(), '{$_POST['username']}', '{$_POST['password']}');");
	}
	
	function login() {
		global $connection;
		if (isset($_POST['username']) && isset($_POST['password'])) {
			$log_query = mysqli_query($connection, "SELECT COUNT(ip_address) As login_count, time_to_sec(timediff(now(), report_datetime)) As seconds FROM access_log WHERE ip_address = '{$_SERVER['REMOTE_ADDR']}' GROUP BY ip_address HAVING ((seconds < 1200) AND login_count >= 3);");
			if (mysqli_num_rows($log_query) == 0) {
				if($result = mysqli_query($connection, "SELECT * FROM user_account WHERE user_name = '{$_POST['username']}' LIMIT 1")) {
					$row = mysqli_fetch_array($result, MYSQLI_ASSOC);			
					if ((md5($_POST['password']) == $row['user_password'])) {
						$_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);
						$_SESSION['ip_address'] = md5($_SERVER['REMOTE_ADDR']); // Helps avoid session high-jacking; the more user data to test against (the better =D).
						$_SESSION['user'] = $row['user_name'];
						return true;
					} else {
						reportLoginFailure();
					}
				} else {
					reportLoginFailure();
				}
			} else {
				reportLoginFailure();
			}
		}
		return false;
	}
	
	function logout() {
		session_unset();  
		session_destroy();
	}
