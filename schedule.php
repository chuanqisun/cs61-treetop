<?php
	  
	//start session
	require_once('startsession.php');
	
	//Insert header
	$page_title='Schedule';
	require_once('header.php');

	//Insert nabigation menu
	require_once('navmenu.php');
	$error_msg="";
?>

<form method="get" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<fieldset>
		<legend>Plan Your Travel</legend>
		<input type="text" name="dep_city" placeholder="From City" value="<?php if (!empty($_GET['dep_city'])) echo $_GET['dep_city']; ?>" />
		<input type="text" name="arr_city" placeholder="To City" value="<?php if (!empty($_GET['arr_city'])) echo $_GET['arr_city']; ?>" />
	</fieldset>
	<input type="submit" value="Search" name="submit" />
</form>
 
<?php



	//connect to database
	require_once('connectvars.php');
	$dbc=mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME); 	
	if(!$dbc){
		$error_msg='Error connecting to MySQL Database';
	}else{

		if(!empty($_GET['dep_city']) && !empty($_GET['arr_city'])){
			$_GET['arr_city']=mysqli_real_escape_string($dbc, trim($_GET['arr_city']));
			$_GET['dep_city']=mysqli_real_escape_string($dbc, trim($_GET['dep_city']));
			$query="SELECT *" .
			" FROM flight_schedule_human" .
			" WHERE dep_city='".$_GET['dep_city']. "' AND" .
			" arr_city='".$_GET['arr_city']."'";


		}else if(!empty($_GET['dep_city'])){
			$_GET['dep_city']=mysqli_real_escape_string($dbc, trim($_GET['dep_city']));
			$query="SELECT *" .
			" FROM flight_schedule_human" .
			" WHERE dep_city='".$_GET['dep_city']. "'" ;

		}else if(!empty($_GET['arr_city'])){
			$_GET['arr_city']=mysqli_real_escape_string($dbc, trim($_GET['arr_city']));
			$query="SELECT *" .
			" FROM flight_schedule_human" .
			" WHERE arr_city='".$_GET['arr_city']. "'" ;

		}else{
			$query="SELECT *" .
			" FROM flight_schedule_human" ;

		}


		
		//get all schedule

		$data=mysqli_query($dbc, $query);
		if(!$data){
			mysqli_close ($dbc);
			$error_msg='Error retrieving schedules';
		}else{
			echo '<table border="1">';
			echo '<tr><th>Flight Number</th><th>Dep. Airport</th><th>Dep. City</th><th>Arr. Airport</th><th>Arr. City</th><th>Day of Operation</th><th>Dep. Time</th><th>Arr. Time</th><th>Aircraft</th</tr>';
			while($row = mysqli_fetch_array($data)){
				echo '<tr>';
				echo '<td>';
				echo $row['flight_number'];
				echo '</td>';
				echo '<td>';
				echo $row['dep_airport'];
				echo '</td>';
				echo '<td>';
				echo $row['dep_city'];
				echo '</td>';
				echo '<td>';
				echo $row['arr_airport'];
				echo '</td>';
				echo '<td>';
				echo $row['arr_city'];
				echo '</td>';
				echo '<td>';
				echo $row['weekday'];
				echo '</td>';
				echo '<td>';
				echo $row['dep_time'];
				echo '</td>';
				echo '<td>';
				echo $row['arr_time'];
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

