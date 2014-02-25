<?php
if (!isset($page)) die();

$error_param = '';
if (isset($_GET['action'])) {
	switch($_GET['action']) {
		case 1:
			$error_param = ((!login()) ? '?error=1' : '');
			break;
		case 2:
			logout();
			break;
	}
}

header('Location: index.php' . $error_param);
?>

