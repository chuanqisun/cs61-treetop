<?php

	require_once('connectvars.php');

	//start session
	session_start();
	
	//Clear error message
	$error_msg="";

	//if not logged in
	if (!isset($_SESSION['user_id'])){
		if (isset($_POST['submit'])){

			//connect to database
			$dbc=mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die('Error connecting to MySQL database');
			$db_selected=mysql_select_db(DB_NAME, $dbc);
			if (!$db_selected) {
			    die ('Can\'t use foo : ' . mysql_error());
			}
			//extract data from the form
			$username=$_POST['username'];
			$password=$_POST['password'];

			//This stops SQL Injection in GET vars 
			foreach ($_GET as $key => $value) { 
				$_GET[$key] = mysql_real_escape_string($value); 
			} 

			if (!empty($username) && !empty($password)){
				echo '<p class=error">'.$username.'</p>';
				echo '<p class=error">'.$password.'</p>';
				echo '<p class=error">'.sha1($password).'</p>';
				$query="SELECT user_id, username FROM account WHERE username='$username' AND password='".sha1($password)."';";
				$data=mysql_query($query);
				echo '<p class=error">'.$query.'</p>';
				echo '<p class=error">'.$data.'</p>';

				if (mysql_num_rows($data)==1) {
					$row=mysql_fetch_array($data);
					$_SESSION['user_id'] = $row['user_id']; 
					$_SESSION['username'] = $row['username'];

					setcookie('user_id', $row['user_id'], time() + (60*60*24*30)); 
					setcookie('username', $row['username'], time() + (60*60*24*30));

					$home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';
					header('Location: ' . $home_url);
					
				}
				else{
				$error_msg= 'invalid username or password.';
				}
			}
			else{
				$error_msg='must enter username and passowrd.';
			}
		}
	}


	//if login failed
	if (empty($_SESSION['user_id'])) {
		echo '<p class=error">'.$error_msg.'</p>';
	}
	//if login success
	else{
		echo'<p class="login">You are logged in as ' . $_SESSION['username'] . '.</p>';
	}
?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<fieldset>
		<legend>Log In</legend>
		<label>Username:</label>
		<input type="text" name="username" />
		<label>Password:</label>
		<input type="password" name="password" />
	</fieldset>
	<input type="submit" value="Log In" name="submit" />
</form>
