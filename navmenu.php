<?php
	//generating navigation bar
	echo '<hr />';
    if(isset($_SESSION['user_id']))
	    if(isset($_SESSION['passenger_id'])){
	    	echo '<a href="schedule.php">schedule</a>';
	    	echo ' | ';
	    	echo '<a href="reservation.php">reservation</a>';
	    }
        if($_SESSION['accout_type'] == 'employee'){
            if(isset($_SESSION['passegner_id'])){
                echo ' | ';
            }
            echo '<a href="passenger">passenger info</a>';
            echo ' | ';
            echo '<a href="stat">statistics</a>';
        }
		echo ' | ';
		echo 'welcome '.$_SESSION['username'].', <a href="logout.php">logout</a>';
	}
	echo '<hr />';
?>
