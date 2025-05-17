<?php
	//Start session
	session_start();
	//Unset the variables stored in session
	unset($_SESSION['uname']);
	unset($_SESSION['ERRMSG_ARR']);
	unset($_SESSION['login_fail']);
	//Go to home page
	header("location: index.php");
?>