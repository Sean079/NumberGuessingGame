<?php 
//Config file for connection
require_once "config.php";

// Start a session
session_start();

//Check if user is logged in; if not, go to login page
if(!isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == false)
{
	header("location: login.php");
}

//Define and initialize with empty value
$label = $userenter = "";

//Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST")
{
	//If the user does not enter a guess
	if(empty($_POST["userGuess"]))
	{
		$label = "Please enter a number between 1 and 10";
	}
	//If the user's guess is higher than the target
	elseif($_POST["userGuess"] > $_SESSION["targetNumber"])
	{
		$label = "The target number is lower.";
		$_SESSION["guessesCount"]++;
	}
	//If the user's guess is lower than the target
	elseif($_POST["userGuess"] < $_SESSION["targetNumber"])
	{
		$label = "The target number is higher.";
		$_SESSION["guessesCount"]++;
	}
	//If the user's guess is correct
	elseif($_POST["userGuess"] == $_SESSION["targetNumber"])
	{
		$_SESSION["guessesCount"]++;
		
		//Prepare insert statement
		$sql = "INSERT INTO userHighScore (userName, userGuess, userTries) VALUES (?, ?, ?)";
		if($stmt = mysqli_prepare($link, $sql))
		{
			//Bind variables to the prepared statement
			mysqli_stmt_bind_param($stmt, "sii", $username, $userguess, $usertries);
			
			//Set parameters
			$username = $_SESSION["username"];
			$userguess = $_SESSION["targetNumber"];
			$usertries = $_SESSION["guessesCount"];
			
			//Attempt to execute statement
			if(mysqli_stmt_execute($stmt))
			{	
				//Redirect to high score page
				header("location: highScore.php");
			}
			else
			{
				echo "Oops! Something went wrong. Please try again later. Error: " . $stmt->error;
			}
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
</head>
<body>
	<div class="wrapper">
		<h3>Enter a number between 1 and 100</h3>
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
			<div class="form-group">
				<label for="userGuess">Enter</label>
				<input class="form-control <?php echo (!empty($label)) ? 'is-invalid' : ''; ?>" value="<?php echo $userenter; ?>" type="text" name="userGuess"/>
				<span class="invalid-feedback"><?php echo $label; ?></span>
				<br>
				<input type="submit" class="btn btn-primary" value="Enter">
			</div>
		</form>
	</div>
</body>
</html>