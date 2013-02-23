<?php

	//start session
	require_once('startsession.php');
	
	//Insert header
	$page_title='Flight';
	require_once('header.php');

	//Insert nabigation menu
	require_once('navmenu.php');

?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<fieldset>
		<legend>Search Flight</legend>
		<input type="text" name="dep_city" placeholder="From City" value="<?php if (!empty($_POST['dep_city'])) echo $_POST['dep_city']; ?>" />
		<input type="text" name="arr_city" placeholder="To City" value="<?php if (!empty($_POST['arr_city'])) echo $_POST['arr_city']; ?>" />
		<input type="text" name="date" placeholder="YYYY-MM-DD" value="<?php if (!empty($_POST['date'])) echo $_POST['date']; ?>" /></br>
		sort by 
		<input type="radio" name="sort" value="date" <?php if($_POST['sort']==date || empty($_POST['sort'])) echo 'checked="yes"'; ?>>Date
		<input type="radio" name="sort" value="flight_number" <?php if($_POST['sort']==flight_number) echo 'checked="yes"'; ?>>Flight Number
		<input type="radio" name="sort" value="dep_time" <?php if($_POST['sort']==dep_time) echo 'checked="yes"'; ?>>Departure Time
		<input type="radio" name="sort" value="arr_time" <?php if($_POST['sort']==arr_time) echo 'checked="yes"'; ?>>Arrival Time</br>
		order 
		<input type="radio" name="order" value="asc" <?php if($_POST['order']==asc || empty($_POST['order'])) echo 'checked="yes"'; ?>>Ascending
		<input type="radio" name="order" value="desc" <?php if($_POST['order']==desc) echo 'checked="yes"'; ?>>Descending

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

		//print out all availabe flights
		$query="SELECT *" .
			" FROM flight_customer" .
			" WHERE TRUE";

		if(!empty($_POST['dep_city'])){
			$_POST['dep_city']=mysqli_real_escape_string($dbc, trim($_POST['dep_city']));
			$query=$query." AND dep_city='".$_POST['dep_city']. "'" ;
		}
		if(!empty($_POST['arr_city'])){
			$_POST['arr_city']=mysqli_real_escape_string($dbc, trim($_POST['arr_city']));
			$query=$query." AND arr_city='".$_POST['arr_city']. "'" ;
		}
		if(!empty($_POST['date'])){
			$_POST['date']=mysqli_real_escape_string($dbc, trim($_POST['date']));
			$query=$query." AND date='".$_POST['date']. "'" ;
		}

		if(!empty($_POST['sort'])){
			$_POST['sort']=mysqli_real_escape_string($dbc, trim($_POST['sort']));
			switch($_POST['sort']){
				case "date":
					$query=$query." ORDER BY date";
					break;
				case "flight_number":
					$query=$query." ORDER BY flight_number";
					break;
				case "dep_time":
					$query=$query." ORDER BY dep_time";
					break;
				case "arr_time":
					$query=$query." ORDER BY arr_time";
					break; 
			}
		}

		if(!empty($_POST['order'])){
			$_POST['order']=mysqli_real_escape_string($dbc, trim($_POST['order']));
			switch($_POST['order']){
				case "asc":
					$query=$query." ASC";
					break;
				case "desc":
					$query=$query." DESC";
					break;
			}
		}

		if(isset($query)){
			//get all schedule

			$data=mysqli_query($dbc, $query);
			if(!$data){
				mysqli_close ($dbc);
				$error_msg='Error retrieving flight info';
			}else{
				echo '<table border="1">';
				echo '<tr><th>Flight Number</th><th>Date</th><th>Dep. City</th><th>Arr. City</th><th>Dep. Time</th><th>Arr. Time</th><th>Status</th><th>Available Seat</th><th>Action</th></tr>';
				while($row = mysqli_fetch_array($data)){
					echo '<tr>';
					echo '<td>';
					echo $row['flight_number'];
					echo '</td>';
					echo '<td>';
					echo $row['date'];
					echo '</td>';
					echo '<td>';
					echo $row['dep_city'];
					echo '</td>';
					echo '<td>';
					echo $row['arr_city'];
					echo '</td>';
					echo '<td>';
					echo $row['dep_time'];
					echo '</td>';
					echo '<td>';
					echo $row['arr_time'];
					echo '</td>';
					echo '<td>';
					echo $row['status'];
					echo '</td>';
					echo '<td>';
					echo $row['available_seat'];
					echo '</td>';
					echo '<td>';
					if (($row['status']=='unknown' || $row['status']== 'on time' || $row['status']=='delayed') && $row['available_seat']>0){
						echo '<a href="reservation.php?flight_number='.$row['flight_number'].'&date='.$row['date'].'">reserve</a>';
					}
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

