<?php
	  
	//start session
	require_once('startsession.php');
	
	//Insert header
	$page_title='Home';
	require_once('header.php');

	//Insert nabigation menu
	require_once('navmenu.php');

	require_once('connectvars.php');

	$error_msg="";
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<fieldset>
		<legend>Top 5</legend>
		From
		<input type="text" name="begin_date" placeholder="YYYY-MM-DD" value="<?php if (!empty($_POST['begin_date'])) echo $_POST['begin_date']; ?>" />
		To
		<input type="text" name="end_date" placeholder="YYYY-MM-DD" value="<?php if (!empty($_POST['end_date'])) echo $_POST['end_date']; ?>" />
	</fieldset>
	<input type="submit" value="Show" name="submit" />
</form>
	

<?php

	$dbc=mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	if(!$dbc){
		$error_msg='Error connecting to MySQL Database';
	}else{

		//check if logged in
		if ($_SESSION['account_type']!='employee'){
			$error_msg= 'Sorry, you are not authorized.';
		}else if(!empty($_POST['submit'])){
			//sanitizer
			$_POST['begin_date']=mysqli_real_escape_string($dbc, trim($_POST['begin_date']));
			$_POST['end_date']=mysqli_real_escape_string($dbc, trim($_POST['end_date']));

			//display all reservations
			$query="CALL get_popular_flight('".$_POST['begin_date']."','".$_POST['end_date']."')";
			$data=mysqli_query($dbc, $query);
			if(!$data){
				mysqli_close ($dbc);
				$error_msg='Error retrieving stats';
			}else{
				//print all reservations
				echo '<table border="1">';
				echo '<tr><th>Flight Number</th><th>From</th><th>To</th><th>Total Traffic</th></tr>';
				while($row = mysqli_fetch_array($data)){
					echo '<tr>';
					echo '<td>';
					echo $row['flight_number'];
					echo '</td>';
					echo '<td>';
					echo $row['dep_city'];
					echo '</td>';
					echo '<td>';
					echo $row['arr_city'];
					echo '</td>';
					echo '<td>';
					echo $row['total_passenger'];
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

