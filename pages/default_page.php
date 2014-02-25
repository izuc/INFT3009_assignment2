<?php
		if (!isset($page)) die();

		function pageTitle() {
			echo "Welcome to Central Power";
		}

		function display() {
			global $error, $connection;
			if (isset($error)) {
				echo $error;
				return;
			} 
			$node_result = mysqli_query($connection, "SELECT * FROM node ORDER BY location ASC");?>	
				<div id="tabs">
					<ul>
						<li><a href="#tabs-1">Introduction</a></li>
						<li><a href="#tabs-2">Generate Graph</a></li>
					</ul>
					<div id="tabs-1">
						<strong>Welcome to Central Power</strong><br />
						In today's society it is important to consider alternate power sources and reudce our
						reliance on non-renewable power sources. Some environmentally friendly power sources 
						to consider are	solar and wind power. While there are households that have implimented 
						an environmentally friendly power source to supplement a portion or all of their power 
						suppy many have postponed their investment due to a lack of information.<br /><br />
						
						This website is based around the case study of a residential implimentation of renewable 
						energy. Live statistics are gathered, stored and graphed giving you and your local 
						community the information they need to make a decision on implementing a renewable 
						energy source at your residence.<br /><br />
						
						The links on the left allow you to view information on the case study and manage aspects 
						of the implimentation.
					</div>
					<div id="tabs-2">
						<form action="index.php?page=show_graph" name="graph_form" method="POST">
							<table>
								<tr>
									<td width="150">
										<input type="radio" name="range" value="today" checked="checked" onmouseup="$('#start_date').val('<?php echo date('m/d/Y'); ?>'),$('#end_date').val('<?php echo date('m/d/Y'); ?>')" />Today<br />
										<input type="radio" name="range" value="2" onmouseup="$('#start_date').val('<?php echo  date('m') . "/" . (((int)date('d')) - ((int)date('w'))) . "/" . date('Y'); ?>'),$('#end_date').val('<?php echo date('m') . "/" . (((int)date('d')) + (6-((int)date('')))) . "/" . date('Y'); ?>')" />This Week<br />
										<input type="radio" name="range" value="3" onmouseup="$('#start_date').val('<?php echo date('m') . "/" . date('d') . "/" . date('Y'); ?>'),$('#end_date').val('<?php echo date('m/t/Y'); ?>')" />This Month<br />
										<input type="radio" name="range" value="4" onmouseup="$('#start_date').val('01/01/<?php echo date('Y'); ?>'),$('#end_date').val('12/31/<?php echo date('Y'); ?>')" />This Year<br />
										<input type="radio" name="range" value="5" onmouseup="$('#start_date').val(''), $('#end_date').val('')"/>Custom Range
									</td>
									<td align="left" valign="top">
										<table>
											<tr>
												<td colspan="2">
													<select name="node" style="width: 205px;">
														<?php
														while ($row = mysqli_fetch_assoc($node_result)) {
															echo '<option value="' . $row['node_id'] . '">' . $row['location'] . '</option>';
														}?>
													</select>
													<br /><br />
												</td>							
											</tr>
											<tr>
												<td>Start Date:</td>
												<td>
													<input name="start_date" id="start_date" class="date_picker" value="<?php echo date('m/d/Y'); ?>" size="12" onfocus="this.blur()"  readonly="readonly" />
												</td>
											</tr>
											<tr>
												<td>End Date:</td>
												<td>
													<input name="end_date" id="end_date" class="date_picker" value="<?php echo date('m/d/Y'); ?>" size="12" onfocus="this.blur()" readonly="readonly" />
												</td>
											</tr>
										</table>
									</td>
									<td valign="bottom">
										<input type="radio" name="data_type" value="1" checked="checked" />Node Data <br />
										<input type="radio" name="data_type" value="2" />Weather Data
									</td>
								</tr>
								<tr>
									<td colspan="3" align="center">
										<input type="submit" name="submit" value="Generate Graph" />
									</td>
								</tr>
							</table>
						</form>
					</div>
				</div>
				<script type="text/javascript">
					$(function() {
						$("#tabs").tabs();
						$(".date_picker").datepicker();
					});
				</script>
				<?php
		}
?>