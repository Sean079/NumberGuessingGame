<?php
//Config file for connection
require_once "config.php";

// Start a session
session_start();

//Create two values; one for generating a random number, the other for 
//keeping track of how many guesses it took the user
$_SESSION["targetNumber"] = rand(1, 100);
$_SESSION["guessesCount"] = 0;

header("location: game.php");
?>