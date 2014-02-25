<?php
session_start();
$page = (isset($_GET['page']) ? $_GET['page'] : 'default_page');
$actions = array('default_page', 'user_actions', 'view_nodes', 'weather_data', 'import_data', 'show_graph', 'security_log');

require_once("db_config.php");
require_once("functions.php");

$connection = db_connect();

foreach ( $_POST as $key => $val ) {
	// Filters ALL POST Data for SQL Injections. The addcslashes function stops % (wildcards) being used, and escapes them out.
	$_POST[ $key ] =  addcslashes(mysqli_real_escape_string($connection, $val), "%_");
}

if (in_array($page, $actions)) {
	require_once("pages/{$page}.php");
} else {
	$error = 'Page Not Found.';
	require_once("pages/default_page.php");
}

require_once("template/header.php");
if (isset($_GET['error']) && is_numeric($_GET['error'])) {
	echo '<p style="text-align: left; font-size: 12px"><b>You are unable to login.</b> <br /> <br />  If you enter your username / password incorrectly 3 times: you will be locked out for 20 minutes. <br /> Please wait 20 minutes, and try again.</p>';
}
display();

require_once("template/footer.php");
?>