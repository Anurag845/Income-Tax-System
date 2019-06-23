<?php
 
function OpenCon() {
	$dbhost = "127.0.0.1";
	$dbuser = "root";
	$dbpass = "dell";
	$db = "Income_Tax";
 
 	$conn = new mysqli($dbhost, $dbuser, $dbpass,$db) or die("Connect failed: %s\n". $conn -> error);
 	
 	return $conn;
}
 
function CloseCon($conn) {
	$conn -> close();
}
   
?>
