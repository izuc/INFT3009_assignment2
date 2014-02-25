		<div id="navigation">
			<a href="index.php">Home Page</a> <br />
			<a href="index.php?page=weather_data">Weather Data</a>
			<br /><br />
			<?php 
			if (isLoggedIn()) { 
				echo '<b>Welcome:</b> '.ucfirst($_SESSION['user']).' =) <br />';
				echo '<a href="index.php?page=view_nodes">Node Management</a><br />
					  <a href="index.php?page=import_data">Import Data</a><br />
					  <a href="index.php?page=security_log">Security Log</a><br />
						[<a href="index.php?page=user_actions&action=2">Logout</a>]';
			} else {
				echo '<table>
						<form method="post" action="index.php?page=user_actions&action=1">
						<tr>
							<td>Username:</td>
							<td><input type="text" name="username" maxlength="8" size="10"></td>
						<tr>
						<tr>
							<td>Password:</td>
							<td><input type="password" name="password" maxlength="16" size="10"></td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" value="Login"></td>
						</tr>
						</form>
					</table>';
			} ?>
		</div>
