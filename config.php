<?php
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
?>