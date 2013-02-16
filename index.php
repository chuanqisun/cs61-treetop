<?php
	  
	//start session
	require_once('startsession.php');
	
	//Insert header
	$page_title='Schedule';
	require_once('header.php');

	//Insert nabigation menu
	require_once('navmenu.php');
?>

	<!--display genes here-->
<?php

	//check if logged in
	if (isset($_SESSION['u_id'])){

		require_once('connectvars.php');
		$dbc=mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die('Error connecting to MySQL Database');



		$query="SELECT g_name, g_expression_count, g_update_time FROM genes ORDER BY g_expression_count DESC LIMIT 10";
		$data=mysqli_query($dbc, $query) or die ('Error fetching genes');
		echo '<p>Most Popular</p>';
		echo '<table boarder="0">';
		echo '<tr><th>Gene</th><th>Variation</th><th>Last Mutation</th>';
		while($row = mysqli_fetch_array($data)){
			echo '<tr>';
			echo '<td>';
			echo '(<a href="evolve.php?g_name=' . $row['g_name'] . '">evolve</a>';
			echo '|';
			echo '<a href="explore.php?type=g_name&name=' . $row['g_name'] . '&submit=Explore">explore</a>)';
			echo $row['g_name'];
			echo '</td>';
			echo '<td>';
			echo $row['g_expression_count'];
			echo '</td>';
			echo '<td>';
			echo $row['g_update_time'];
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
	}else{
		echo '<p>Welcome to Treetop Airlines. Please login.</p>';
	}
?>

<?php

	//insert footer
	require_once('footer.php');
?>
