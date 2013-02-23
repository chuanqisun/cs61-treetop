<?php
	  
	//start session
	require_once('startsession.php');
	
	//Insert header
	$page_title='Passenger';
	require_once('header.php');

	//Insert nabigation menu
	require_once('navmenu.php');

	require_once('connectvars.php');
	$error_msg="";
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<fieldset>
		<legend>Passenger List</legend>
		<input type="text" name="flight_number" placeholder="Flight Number" value="<?php if (!empty($_POST['flight_number'])) echo $_POST['flight_number']; ?>" />
		<input type="text" name="date" placeholder="YYYY-MM-DD" value="<?php if (!empty($_POST['date'])) echo $_POST['date']; ?>" />
	</fieldset>
	<input type="submit" value="Show" name="submit" />
</form>
	

<?php

	$dbc=mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	if(!$dbc){
		$error_msg='Error connecting to MySQL Database';
	}else{

		//check if logged in with proper account_type
		if ($_SESSION['account_type']!='employee'){
			$error_msg= 'Sorry, you are not authorized.';
		}else if (!empty($_POST['submit'])){
			//sanitizer
			$_POST['flight_number']=mysqli_real_escape_string($dbc, trim($_POST['flight_number']));
			$_POST['date']=mysqli_real_escape_string($dbc, trim($_POST['date']));

			//display passenger list
			$query="CALL get_passenger_list('".$_POST['flight_number']."','".$_POST['date']."')";
			$data=mysqli_query($dbc, $query);
			if(!$data){
				mysqli_close ($dbc);
				$error_msg='Error retrieving passenger info';
			}else{
				//print all reservations
				echo '<table border="1">';
				echo '<tr><th>Seat Number</th><th>Passenger ID</th><th>Passenger Name</th></tr>';
				while($row = mysqli_fetch_array($data)){
					echo '<tr>';
					echo '<td>';
					echo $row['seat_number'];
					echo '</td>';
					echo '<td>';
					echo $row['id'];
					echo '</td>';
					echo '<td>';
					echo $row['name'];
					echo '</td>';
					echo '</tr>';
				}	
				echo '</table>';
			}
		}
	}
?>

<?php
	//close db
	require_once('closedb.php');

	//insert footer
	require_once('footer.php');

	//error handling
	require_once('error.php');
?>

