<?php
	  
	//start session
	require_once('startsession.php');
	
	//Insert header
	$page_title='Reservation';
	require_once('header.php');

	$error_msg="";

	//Insert nabigation menu
	require_once('navmenu.php');

	require_once('connectvars.php');

?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<fieldset>
		<legend>Manage Your Researvation</legend>
		<input type="text" name="flight_number" placeholder="Flight Number" value="<?php if (!empty($_POST['flight_number'])) {echo $_POST['flight_number'];}else if(!empty($_GET['flight_number'])) {echo $_GET['flight_number'];} ?>"/>
		<input type="text" name="date" placeholder="YYYY-MM-DD" value="<?php if (!empty($_POST['date'])) {echo $_POST['date'];}else if(!empty($_GET['date'])) {echo $_GET['date'];} ?>"/><br>
		<input type="radio" name="action" value="make" <?php if($_POST['action']==make || empty($_POST['action'])) echo 'checked="yes"'; ?>>Reserve
		<input type="radio" name="action" value="cancel" <?php if($_POST['action']==cancel) echo 'checked="yes"'; ?>>Cancel<br>
	</fieldset>
	<input type="submit" value="Execute" name="submit" />
</form>


 
<?php

	$dbc=mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	if(!$dbc){
		$error_msg='Error connecting to MySQL Database';
	}else{
		//deal with new reservation
		if(!isset($_SESSION['passenger_id'])){
			echo "Sorry Please login to manage reservations.";
		}else if($_POST['action']=='make' && !empty($_POST['flight_number']) && !empty($_POST['date'])){
			//sanitizer
			$_POST['flight_number']=mysqli_real_escape_string($dbc, trim($_POST['flight_number']));
			$_POST['date']=mysqli_real_escape_string($dbc, trim($_POST['date']));

			$query="SELECT make_reservation('".$_SESSION['passenger_id']."','".$_POST['flight_number']."','".$_POST['date']."') as result";
			$data=mysqli_query($dbc, $query);
			if(!$data){
				mysqli_close ($dbc);
				$error_msg='Error making reservation';
			}else{
				$row=mysqli_fetch_array($data);
				switch ($row['result']) {
					case '0':
						$error_msg=  "reservation made successfully!";
						break;
					case '1':
						$error_msg=  "sorry, the flight is full.";
						break;
					case '2':
						$error_msg=  "sorry, the flight is no reservable.";
						break;
					case '3':
						$error_msg=  "sorry, you have already reserved this flight.";
						break;
				}
			}
		}
	}

?>

<?php
	//deal with reservation cancelation
	if(!isset($_SESSION['passenger_id'])){
		$error_msg= 'Sorry Please login to manage reservations.';
	}else if($_POST['action']=='cancel' && !empty($_POST['flight_number']) && !empty($_POST['date'])){
		//sanitizer
		$_POST['flight_number']=mysqli_real_escape_string($dbc, trim($_POST['flight_number']));
		$_POST['date']=mysqli_real_escape_string($dbc, trim($_POST['date']));

		$query="SELECT cancel_reservation('".$_SESSION['passenger_id']."','".$_POST['flight_number']."','".$_POST['date']."') as result";
		$data=mysqli_query($dbc, $query);
		if(!$data){
			mysqli_close ($dbc);
			$error_msg='Error canceling reservation';
		}else{
			$row=mysqli_fetch_array($data);
			switch ($row['result']) {
				case '0':
					$error_msg=  "reservation canceled successfully!";
					break;
				case '1':
					$error_msg=  "sorry, you are not on this flight.";
					break;
				case '2':
					$error_msg=  "sorry, the flight is no cancelable.";
					break;
			}
		}
	}
?>
	

<?php	
	//check if logged in
	if (!isset($_SESSION['passenger_id'])){
		$error_msg= 'Sorry Please login to manage reservations.';
	}else{
		//display all reservations
		$query="SELECT * FROM reservation_customer WHERE passenger_id='".$_SESSION['passenger_id']."'";
		$data=mysqli_query($dbc, $query);
		if(!$data){
			mysqli_close ($dbc);
			$error_msg='Error retrieving reservation info';
		}else{
			//print all reservations
			echo '<table border="1">';
			echo '<tr><th>Flight Number</th><th>Flight Date</th><th>Dep. City</th><th>Arr. City</th><th>Status</th><th>Departure Time</th><th>Seat Number</th</tr><th>Aircraft</th</tr>';
			while($row = mysqli_fetch_array($data)){
				echo '<tr>';
				echo '<td>';
				echo $row['flight_number'];
				echo '</td>';
				echo '<td>';
				echo $row['flight_date'];
				echo '</td>';
				echo '<td>';
				echo $row['dep_city'];
				echo '</td>';
				echo '<td>';
				echo $row['arr_city'];
				echo '</td>';
				echo '<td>';
				echo $row['status'];
				echo '</td>';
				echo '<td>';
				echo $row['dep_time'];
				echo '</td>';
				echo '<td>';
				echo $row['seat_number'];
				echo '</td>';
				echo '<td>';
				echo $row['aircraft'];
				echo '</td>';
				echo '</tr>';
			}	
			echo '</table>';
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

