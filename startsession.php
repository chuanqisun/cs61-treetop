<?php
	session_start();

	//auto re-login by cookie
	if(!isset($_SESSION['user_id'])){
		if(isset($_COOKIE['user_id'])){
			$_SESSION['user_id']=$_COOKIE['user_id'];
			$_SESSION['username']=$_COOKIE['username'];
			$_SESSION['account_type']=$_COOKIE['account_type'];
        }
        if(isset($_COOKIE['passenger_id']){
		    $_SESSION['passenger_id']=$_COOKIE['passenger_id']; 
        }
	}
?>
