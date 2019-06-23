<?php
	include 'db_connect.php';
	$conn = OpenCon();
	
	$username = $_POST['Username'];
	$password = $_POST['Password'];
	
	$cred = "select * from Users where username = '$username' and password = '$password'";
	$res = mysqli_query($conn,$cred);
	
	if(mysqli_num_rows($res) == 0) {
		echo "Invalid credentials.";
	}
	else if(mysqli_num_rows($res) == 1) {
		if($username == "admin") {
			echo "<input id='user_cat' type='hidden' value='admin'>";
		}
		else if($username == "user") {
			echo "<input id='user_cat' type='hidden' value='user'>";
		}
	}
?>

<html>

<head>
	<script>
		var user_cat = document.getElementById("user_cat").value;
		if(user_cat == "admin") {
			window.location.href = "admin_menu.html";
		}
		else {
			window.location.href = "user_menu.html";
		}
	</script>
</head>

</html>
