<?php		
		$msg = '';
		if (isset($_GET['delete_node']) && is_numeric($_GET['delete_node'])) {
			mysqli_query ($connection, "DELETE FROM node WHERE node_id = '{$_GET['delete']}'");
		}
		
		if (isset($_POST['update_node']) && (is_numeric($_GET['node']))) {		
		    if ((isset($_POST['location'])) && (isset($_POST['type'])) && (isset($_POST['equipment']))) {
				$query = "UPDATE node SET location = '{$_POST['location']}', type = '{$_POST['type']}', equipment = '{$_POST['equipment']}' WHERE node_id = {$_GET['node']} LIMIT 1";
				$result = mysqli_query ($connection, $query); // Run the query.
				$msg = 'Node has been updated';
			}
		}
		
		if (isset($_POST['add_node'])) {		
			if ((isset($_POST['location'])) && (isset($_POST['type'])) && (isset($_POST['equipment']))) {
				if (empty($_POST['location'])) {
					$msg .= '! You must enter a location <br />';
				}
				if (empty($_POST['type'])) {
					$msg .= '! You must enter a type of node <br />';
				}
				if (empty($_POST['equipment'])) {
					$msg .= '! You must enter the equipment used by the node <br />';
				}
				if (!strlen($msg)) {
					$query = "INSERT INTO node (location, type, equipment) VALUES ('{$_POST['location']}', '{$_POST['type']}', '{$_POST['equipment']}' )";
					$result = mysqli_query ($connection, $query); // Run the query.
					$msg = 'Node has been added';
				}
			}
		}
		
		if (isset($_POST['add_bank']) && (is_numeric($_GET['node']))) {
			if (isset($_POST['bank_name']) && (!empty($_POST['bank_name']))) {
				if (!mysqli_num_rows(mysqli_query($connection, "SELECT * FROM node_bank WHERE node_id = {$_GET['node']} && bank_name = '{$_POST['bank_name']}'")))
					mysqli_query ($connection, "INSERT INTO node_bank (node_id, bank_name) VALUES ({$_GET['node']}, '{$_POST['bank_name']}')");
			}
		}
		
		if (isset($_GET['remove_bank']) && (is_numeric($_GET['remove_bank']))) {
			mysqli_query ($connection, "DELETE FROM node_bank WHERE bank_id = {$_GET['remove_bank']} LIMIT 1");
		}
		
		function pageTitle() {
			echo "Node Management";
		}
		
		function display() {
			global $connection, $msg;
			
			if (strlen($msg))
				echo "<h5>{$msg}</h5>";
			if (isset($_GET['add'])) {
			?>
				<form action="index.php?page=view_nodes" method="post">
					<fieldset>
						<legend>Add node information below:</legend>
						<table>
							<tr>
								<td>Location:</td>
								<td><input type="text" name="location" maxlength="25" value="<?php if(isset($_POST["location"])) echo $_POST["location"]; ?>"></td>
							</tr>
							<tr>
								<td>Type:</td>
								<td><input type="text" name="type" maxlength="25" value="<?php if(isset($_POST["type"])) echo $_POST["type"]; ?>"></td>
							</tr>
							<tr>
								<td>Equipment:</td>
								<td><input type="text" name="equipment" size="50" maxlength="150" value="<?php if(isset($_POST["equipment"])) echo $_POST["equipment"]; ?>"></td>
							</tr>
							<tr>
								<td colspan="3"><input type="submit" name="add_node" value="Add Node"></td>
							</tr>
						</table>
					</fieldset>
				</form>
			<?php
			} else {
				if (isset($_GET['node']) && is_numeric($_GET['node'])) {
					$result = mysqli_query($connection, "SELECT * FROM node WHERE node_id = {$_GET['node']} LIMIT 1"); 
					if (mysqli_num_rows($result)) {
						$row = mysqli_fetch_assoc($result);
						?>
						<form action="index.php?page=view_nodes&node=<?php echo $_GET['node'];?>" method="post">
							<fieldset>
								<legend>Edit Node:</legend>
								<table>
									<tr>
										<td>Location:</td>
										<td><input type="text" name="location" maxlength="25" value="<?php echo $row['location']; ?>"></td>
									</tr>
									<tr>
										<td>Type:</td>
										<td><input type="text" name="type" maxlength="25" value="<?php echo $row['type']; ?>"></td>
									</tr>
									<tr>
										<td>Equipment:</td>
										<td><input type="text" name="equipment" size="50" maxlength="150" value="<?php echo $row['equipment']; ?>"></td>
									</tr>
									<tr>
										<td colspan="3"><input type="submit" name="update_node" value="Update"></td>
									</tr>
								</table>
							</fieldset>
						</form>
						
						<form action="index.php?page=view_nodes&node=<?php echo $_GET['node'];?>" method="post">
							<fieldset>
								<legend>Create a New Bank:</legend>
								<table>
									<tr>
										<td>Bank Name:</td>
										<td><input type="text" name="bank_name" maxlength="25" value="">&nbsp;&nbsp;&nbsp;<input type="submit" name="add_bank" value="Add Bank"></td>
									</tr>
								</table>
							</fieldset>
						</form>
					<?php
						$bank_result = mysqli_query($connection, "SELECT bank.bank_id, bank.bank_name, AVG( data.charge_current ) As avg_current, MIN( data.charge_current ) As min_current , MAX( data.charge_current ) As max_current , SUM( data.charge_current ) As sum_current
										FROM node_bank As bank LEFT JOIN bank_data As data ON data.bank_id = bank.bank_id WHERE bank.node_id = {$_GET['node']} GROUP BY bank_id ORDER BY bank.bank_name ASC");
						if (mysqli_num_rows($bank_result)) {
							echo '<table style="border-spacing: 10px;">
									 <tr>
									 <th align=left>Bank&nbsp;&nbsp;</th>
									 <th align=left>Average</th>
									 <th align=left>Min</th>
									 <th align=left>Max</th>
									 <th align=left>Sum</th>
									 <th align=left>&nbsp&nbsp;&nbsp;</th>
									 </tr>';
							while ($row = mysqli_fetch_assoc($bank_result)) {
									echo '<tr>
										<td align="left">' . $row['bank_name'] . '</td>
										<td align="left">' . $row['avg_current'] . '</td>
										<td align="left">' . $row['min_current'] . '</td>
										<td align="left">' . $row['max_current'] . '</td>
										<td align="left">' . $row['sum_current'] . '</td>
										<td align="left"><a onclick="return confirm(\'Are you sure?\')" href="index.php?page=view_nodes&node=' . $_GET['node'] . '&remove_bank=' . $row['bank_id'] . '">Remove</a></td>
									</tr>';  
							}
							echo '</table>';
							mysqli_free_result($bank_result);
						}
					}
				} else {
					$node_result = mysqli_query($connection, "SELECT * FROM node ORDER BY node_id ASC");
					echo '<a href="index.php?page=view_nodes&add=1">[Add Node]</a><br /><br />';
					if (mysqli_num_rows($node_result)) {			
						echo '<table style="border-spacing: 10px;">
								 <tr>
								 <th align=left>Location</th>
								 <th align=left>Type</th>
								 <th align=left>Equipment</th>
								 <th align=left>Edit</th>
								 <th align=left>Delete</th>
								 </tr>';
						while ($row = mysqli_fetch_assoc($node_result)) {					
								echo '<tr>
									<td align="left">' . $row['location'] . '</td>
									<td align="left">' . $row['type'] . '</td>
									<td align="left">' . $row['equipment'] . '</td>
									<td align="left"><a href="index.php?page=view_nodes&node=' . $row['node_id'] . '">Edit</a></td>
									<td align="left"><a onclick="return confirm(\'Are you sure?\')" href="index.php?page=view_nodes&delete_node=' . $row['node_id'] . '">Delete</a></td>
								</tr>'; 
						}
						echo '</table>';
						mysqli_free_result($node_result);
					} else {
						echo 'No nodes currently available.';
					}
				}
			}
		}
?>