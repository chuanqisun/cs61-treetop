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
			$db_selected=mysql_select_db(DB_NAME, $dbc) or die('Can\'t use datebase');
		    
            echo 'connected';

			//This stops SQL Injection 
			foreach ($_POST as $key => $value) { 
				$_POST[$key] = mysql_real_escape_string(trim($value)); 
			} 
			
			//extract data from the form
			$username=$_POST['username'];
			$password=$_POST['password'];

			if (!empty($username) && !empty($password)){

                echo 'user+pass';

				$query="SELECT user_id, username, account_type, passenger_id FROM account WHERE username='$username' AND password='".sha1($password)."';";
				$data=mysql_query($query);

				if (mysql_num_rows($data)==1) {
					$row=mysql_fetch_array($data);
					$_SESSION['user_id'] = $row['user_id']; 
					setcookie('user_id', $row['user_id'], time() + (60*60*24*30)); 

					$_SESSION['username'] = $row['username'];
					setcookie('username', $row['username'], time() + (60*60*24*30));

                    if (!empty($row['passenger_id'])) {
					    $_SESSION['passenger_id'] = $row['passenger_id']; 
					    setcookie('passenger_id', $row['passenger_id'], time() + (60*60*24*30)); 
                    }

					$_SESSION['account_type'] = $row['account_type'];
					setcookie('account_type', $row['account_type'], time() + (60*60*24*30));
				    	
				    echo 'all set';	
					//$home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';
					//header('Location: ' . $home_url);
					
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
