<?php
	
	//mysql global variables
	require_once('connectvars.php');

	//start session
	require_once('startsession.php');
	
	//Clear error message
	$error_msg="";

	//Insert header
	$page_title='Login';
	require_once('header.php');

	//Insert nabigation menu
	require_once('navmenu.php');
?>

<?php

	//if not logged in
	if (!isset($_SESSION['u_id'])){
		if (isset($_POST['submit'])){

			//connect to database
			$dbc=mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			if(!$dbc){
				$error_msg='Error connecting to MySQL Database';
			}else{
				//sanitizer
				$_POST['username']=mysqli_real_escape_string($dbc, $_POST['username']);
				$_POST['password']=mysqli_real_escape_string($dbc, $_POST['password']);

	
				//extract data from the form
				$username=$_POST['username'];
				$password=$_POST['password'];

				if (!empty($username) && !empty($password)){
					$query="SELECT user_id, username, account_type, passenger_id FROM account WHERE username='$username' AND password=SHA('$password')";
					$data=mysqli_query($dbc, $query);
					if(!$data){
						mysqli_close ($dbc);
						$error_msg='Error login';
					}else{

						if (mysqli_num_rows($data)==1) {
							$row=mysqli_fetch_array($data);
							$_SESSION['user_id'] = $row['user_id']; 
							$_SESSION['username'] = $row['username'];
							$_SESSION['account_type'] = $row['account_type'];

							if (!empty($row['passenger_id'])) {
								$_SESSION['passenger_id'] = $row['passenger_id'];
							}

							setcookie('user_id', $row['user_id'], time() + (60*60*24*1)); //24 hours
							setcookie('username', $row['username'], time() + (60*60*24*1));
							if (!empty($row['passenger_id'])) {
								setcookie('passenger_id', $row['passenger_id'], time() + (60*60*24*1));
							}
							setcookie('account_type', $row['account_type'], time() + (60*60*24*1));

							//redirect to home page
							$home_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/index.php';
							header('Location: ' . $home_url);
					
						}
						else{
						$error_msg= 'invalid username or password.';
						}
					}
				}
				else{
					$error_msg='must enter username and passowrd.';
				}
			}
		}
	}

?>

<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	<fieldset>
		<legend>Log In</legend>
		<input type="text" name="username" placeholder="username" /></br>
		<input type="password" name="password" placeholder="password"/>
	</fieldset>
	<input type="submit" value="Log In" name="submit" />
</form>

<?php
	//close db
	require_once('closedb.php');

	//insert footer
	require_once('footer.php');

	//error handling
	require_once('error.php');
?>


