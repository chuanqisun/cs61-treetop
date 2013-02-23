<?php

	//generating navigation bar according to the type of user and whether an associated passenger_id exists

	//show this to everyone
	echo '<hr />';
	echo '<a href="schedule.php">schedule</a>';
	if(isset($_SESSION['username'])){
		//to logged in passengers
		if($_SESSION['passenger_id']){
			echo ' | ';
			echo '<a href="flight.php">flight</a>';
			echo ' | ';
			echo '<a href="reservation.php">reservation</a>';
		}
		//to logged in employees
		if($_SESSION['account_type']=='employee'){
			echo ' | ';
			echo '<a href="passenger.php">passenger</a>';
			echo ' | ';
			echo '<a href="stats.php">statistics</a>';
		}
		//to everyone logged in
		echo ' | ';
		echo '<a href="logout.php">logout('.$_SESSION['username'].')</a>';
	}
	//to everyone not logged in
	else{
		echo ' | ';
		echo '<a href="login.php">login</a>';
	}
	echo '<hr />';
?>

