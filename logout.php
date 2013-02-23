<?php
	
	session_start();
	
	if(isset($_SESSION['user_id'])){
		//delete session variables
		$_SESSION=array();
	}

	//delete session cookie
	if(isset($_SESSION[session_name()])) {
		setcookie(session_name(), '', time()-3600);
	}
	
	//destroy session
	session_destroy();

	//delete user cookie by making them expire
	setcookie('user_id', '', time()-3600);
	setcookie('username', '', time()-3600);
	setcookie('passenger_id', '', time()-3600);
	setcookie('account_type', '', time()-3600);	

	$home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';
	header('Location: ' . $home_url);
?>

