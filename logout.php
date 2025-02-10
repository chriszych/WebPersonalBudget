<?php
	session_start();
	unset($_SESSION['logged_id']);
	unset($_SESSION['logged_firstname']);
	header('Location: index.php');
?>
