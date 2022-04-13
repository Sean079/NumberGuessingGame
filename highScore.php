<?php
// Start a session
session_start();

$correctNumber = $_SESSION["targetNumber"];
$amountOfTries = $_SESSION["guessesCount"];
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
		<h2>You guessed the right number!</h2>
		<h5>Target number: <?php echo $correctNumber; ?></h5>
		<h5>Amount of guesses: <?php echo $amountOfTries; ?></h5>
	<div>
	<?php
	//Reset variables if user chooses to play again
	$_SESSION["targetNumber"] = rand(1, 100);
	$_SESSION["guessesCount"] = 0;
	
	define('DB_SERVER', 'sql103.epizy.com');
	define('DB_USERNAME', 'epiz_30809405');
	define('DB_PASSWORD', '70k9WRekCBA');
	define('DB_NAME', 'epiz_30809405_3750spring22');
	
	$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
 
	// Check connection
	if($link === false)
	{
		die("ERROR: Could not connect. " . mysqli_connect_error());
	}
	
	$sql = "SELECT userName, userGuess, userTries FROM userHighScore ORDER BY userTries ASC LIMIT 10";
	
	$result = $link->query($sql);
	
	echo "<table>
	<tr>
	<th>Username</th>
	<th>Target Number</th>
	<th>Amount of Guesses</th>
	</tr>";
	
	if($result->num_rows > 0)
	{
		while($row = $result->fetch_assoc())
		{
			echo "<tr>";
			echo "<td>" . $row['userName'] . "</td>";
			echo "<td>" . $row['userGuess'] . "</td>";
			echo "<td>" . $row['userTries'] . "</td>";
			echo "</tr>";
		}
	}
	else
	{
		echo "No results.";
	}
	echo "</table>"
	?>
	<div>
		<br>
		<a href="logout.php" class="btn btn-primary">Log Out</a>
		<br>
		<br>
		<a href="game.php" class="btn btn-primary">Play Again</a>
	</div>
</body>
</html>