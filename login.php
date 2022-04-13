<?php
//Config file for connection
require_once "config.php";

// Start a session
session_start();

//Check if user is already logged in; if yes, direct to intermission page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true)
{
	header("location: intermission.php");
}

//Define and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";

//Function to take salt, password entered, and hashed password and determine
//if the entered password is correct
function verifyPassword($password, $user_salt, $hashed_password)
{
	return(hash("sha256", $password.$user_salt) == $hashed_password);
}

//Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST")
{
	//Check if username is empty
	if(empty(trim($_POST["username"])))
	{
		$username_err = "Please enter username.";
	}
	else
	{
		$username = trim($_POST["username"]);
	}
	
	//Check if password is empty
	if(empty(trim($_POST["password"])))
	{
		$password_err = "Please enter your password.";
	}
	else
	{
		$password = trim($_POST["password"]);
	}
	
	//Validate credentials
	if(empty($username_err) && empty($password_err))
	{
		//Prepare select statement
		$sql = "SELECT user_id, userName, userSalt, userPassword FROM userTable WHERE userName = ?";
		
		if($stmt = mysqli_prepare($link, $sql))
		{
			//Bind variables to the prepared statement as parameters
			mysqli_stmt_bind_param($stmt, "s", $param_username);
			
			//Set parameters
			$param_username = $username;
			
			//Attempt to execute select statement
			if(mysqli_stmt_execute($stmt))
			{
				//Store result
				mysqli_stmt_store_result($stmt);
				
				//Check if username exists; if yes, verify password
				if(mysqli_stmt_num_rows($stmt) == 1)
				{
					//Bind result variables
					mysqli_stmt_bind_result($stmt, $id, $username, $user_salt, $hashed_password);
					
					if(mysqli_stmt_fetch($stmt))
					{
						//Check if password matches
						if(verifyPassword($password, $user_salt, $hashed_password))
						{
							//Password correct, start new session
							session_start();
							
							//Store data in session variables
							$_SESSION["loggedin"] = true;
							$_SESSION["id"] = $id;
							$_SESSION["username"] = $username;
							
							//Generate variables for game
							$_SESSION["targetNumber"] = rand(1, 100);
							$_SESSION["guessesCount"] = 0;
							
							//Redirect to intermission page
							header("location: game.php");
						}
						else
						{
							//Password not valid, display error
							$login_err = "Invalid username or password.";
						}
					}
				}
				else
				{
					//Username doesn't exist, display error
					$login_err = "Invalid username or password.";
				}
			}
			else
			{
				echo "Oops! Something went wrong. Please try again later. Error: " . $stmt->error;;
			}
			
			mysqli_stmt_close($stmt);
		}
	}
	mysqli_close($link);
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
</head>
	<body>
		<div class="wrapper">
			<h2>Login</h2>
			
			<?php
			if(!empty($login_err))
			{
				echo '<div class="alert alert-danger">' . $login_err . '</div>';
			}
			?>
		
			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
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
					<input type="submit" class="btn btn-primary" value="Login">
				</div>
				
				<p> Don't have an account? <a href="signup.php">Sign up now</a></p>
			</form>
		</div>
	</body>
</html>