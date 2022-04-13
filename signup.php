<?php
//Config file for connection
require_once "config.php";

session_start();

//Define variables, initialize with empty values
$username = $password = "";
$username_err = $password_err = "";

//Processing form data when create account form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST")
{
	if(empty(trim($_POST["username"])))
	{
		$username_err = "Please enter a username";
	}
	else
	{
		//Prepare select statement
		$sql = "SELECT user_id FROM userTable WHERE userName = ?";

		if($stmt = mysqli_prepare($link, $sql))
		{
			//Bind variables to the prepared statement
			mysqli_stmt_bind_param($stmt, "s", $param_username);
			
			//Set username to add to statement
			$param_username = trim($_POST["username"]);
			
			//Attempts SQL statement
			if(mysqli_stmt_execute($stmt))
			{
				//Store result
				mysqli_stmt_store_result($stmt);
				
				//If an existing username is found, show error
				if(mysqli_stmt_num_rows($stmt) == 1)
				{
					$username_err = "This username is already taken.";
				}
				else
				{
					$username = trim($_POST["username"]);
				}
			}
			else
			{
				echo "Oops! Something went wrong. Please try again later. Error: " . $stmt->error;
			}
			
			mysqli_stmt_close($stmt);
		}
	}
	if(empty(trim($_POST["password"])))
	{
		$password_err = "Please enter a password.";
	}
	elseif(strlen(trim($_POST["password"])) < 6)
	{
		$password_err = "Password must have at least 6 characters.";
	}
	else
	{
		$password = trim($_POST["password"]);
	}
	
	//Check for input errors before inserting into table
	if(empty($username_err) && empty($password_err))
	{
		//Prepare insert statement
		$sql = "INSERT INTO userTable (userName, userSalt, userPassword) VALUES (?, ?, ?)";
		
		if($stmt = mysqli_prepare($link, $sql))
		{
			//Bind variables to the prepared statement
			mysqli_stmt_bind_param($stmt, "sss", $param_username, $param_usersalt, $param_userpassword);
			
			//Set parameters
			$param_username = $username;
			$param_usersalt = bin2hex(random_bytes(4));
			$param_userpassword = hash("sha256", $password.$param_usersalt);
			
			//Attempt to execute statement
			if(mysqli_stmt_execute($stmt))
			{
				//Redirect to login page
				header("location: login.php");
			}
			else
			{
				echo "Oops! Something went wrong. Please try again later. Error: " . $stmt->error;
			}
			
			//Close statement
			mysqli_stmt_close($stmt);
			
		}
		
	}
	
}
?>
<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<style>
		body{ font: 14px sans-serif; }
		.wrapper{ width: 360px; padding: 20px;}
	</style>
<head>
	<body>
		<div class="wrapper">
			<h2>Sign Up</h2>
			<form action="" method="post">
				<div class="form-group">
					<label for="username">Username</label>
					<input class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>" type="text" name="username"/>
                    <span class="invalid-feedback"><?php echo $username_err; ?></span>
				</div>

				<div class="form-group">
					<label for="password">Password</label>
					<input class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>" type="password" name="password"/>
                    <span class="invalid-feedback"><?php echo $password_err; ?></span>
				</div>
				
				<div class="form_group">
					<input type="submit" class="btn btn-primary" value="Submit">
				</div>
				
				<p> Already have an account? <a href="login.php">Login here</a></p>
			</form>
		</div>
	</body>
</html>