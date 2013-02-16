<?php
	//generating navigation bar
	echo '<hr />';
	if(isset($_SESSION['username'])){
		echo '<a href="schedule.php">schedule</a>';
		echo ' | ';
		echo '<a href="reservation.php">reservation</a>';
		echo ' | ';
		echo '<p>welcome'.$username.', <a href="logout.php">logout('.$_SESSION['u_name'].')</a></p>';
	}
	else{
		echo '<a href="login.php">login</a>';
		echo ' | ';
		echo '<a href="signup.php">sign-up</a>';
	}
	echo '<hr />';
?>
