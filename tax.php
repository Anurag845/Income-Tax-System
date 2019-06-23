<!DOCTYPE html>

<head>

	<style>
	
		html {
			overflow-y: scroll;
		}
	
		body {
			margin: 0;
			padding: 0;
			font-size: 16;
		}
		
		.invalid {
			display: none;
		}
		
		td, th {
			margin: 20px;
			padding: 8px;
			text-align: left;
			border: 1px solid black;
		}
		
		table {
			border-collapse: collapse;
			border: 1px solid black;
		}
		
		form {
			padding: 10px;
			margin-top: 10px;
			margin-bottom: 100px;
		}
		
		.heading {
        	top: 0;
        	left: 0;
        	right: 0;
        	height: 70px;
        	width: 100%;
			background: black;
			color: white;
			padding: 0;
			position: fixed;
		}
		
		h1 {
			margin: 0;
			text-align: center;
			line-height: 70px;
		}
		
		h2 {
			margin-top: 0;
		}
		
		.topnav {
			border-top: 1px solid silver;
			border-bottom: 1px solid black;
  			background-color: #333;
  			overflow: hidden;
  			padding: 0;
		}

		.topnav a {
			float: left;
  			color: #f2f2f2;
  			text-align: center;
  			padding: 10px 12px;
  			text-decoration: none;
		}


		.topnav a:hover {
  			background-color: #ddd;
  			color: black;
		}
		
		.contents {
			margin-top: 140px;
		}
		
	</style>

</head>

<html>

<body>

	<div class='heading'>
		
		<h1>Income Tax System</h1>
		
		<div class="topnav">
			<a onclick="gotoHome();">Home</a>
  			<a onclick="gotoForm();">Declaration Form</a>
  			<a onclick="gotoValidate();">Declaration Validation</a>
  			<a onclick="gotoTaxable();">Taxable Amount</a>
  			<a onclick="gotoTax();">Income Tax</a>
		</div>
				
	</div>
	
	<?php
		include 'db_connect.php';

		$conn = OpenCon();
	
		$all_tax = "select * from Tax_monthly";
		$taxable = mysqli_query($conn,$all_tax);

	?>
	
	<div class="contents">

		<center>

		<h2>Monthly Income Tax</h2>
		
		<table>
	
			<?php
				echo"<tr>";
   					echo "<th>" . 'Employee Id' . "</th>";
   					echo "<th>" . 'April' . "</th>";
   					echo "<th>" . 'May' . "</th>";
   					echo "<th>" . 'June' . "</th>";
   					echo "<th>" . 'July' . "</th>";
   					echo "<th>" . 'August' . "</th>";
   					echo "<th>" . 'September' . "</th>";
   					echo "<th>" . 'October' . "</th>";
   					echo "<th>" . 'November' . "</th>";
   					echo "<th>" . 'December' . "</th>";
   					echo "<th>" . 'January' . "</th>";
   					echo "<th>" . 'February' . "</th>";
   					echo "<th>" . 'March' . "</th>";
   					echo "<th>" . 'Annual' . "</td>";
					echo "<th>" . 'Adjusted' . "</td>";
					echo "<th>" . 'Education Cess' . "</td>";
   				echo"</tr>";
				while($rows = mysqli_fetch_array($taxable)) {
					echo"<tr>";
   					echo "<td>" . $rows['emp_id'] . "</td>";
   					echo "<td>" . $rows['April'] . "</td>";
   					echo "<td>" . $rows['May'] . "</td>";
   					echo "<td>" . $rows['June'] . "</td>";
   					echo "<td>" . $rows['July'] . "</td>";
   					echo "<td>" . $rows['August'] . "</td>";
   					echo "<td>" . $rows['September'] . "</td>";
   					echo "<td>" . $rows['October'] . "</td>";
   					echo "<td>" . $rows['November'] . "</td>";
   					echo "<td>" . $rows['December'] . "</td>";
   					echo "<td>" . $rows['January'] . "</td>";
   					echo "<td>" . $rows['February'] . "</td>";
   					echo "<td>" . $rows['March'] . "</td>";
   					echo "<td>" . $rows['Annual'] . "</td>";
   					echo "<td>" . $rows['Adjusted'] . "</td>";
   					echo "<td>" . $rows['Edu_Cess'] . "</td>";
   					echo"</tr>";
				}
			?>

		</table>

	</center>
	
	<script>
	
		function gotoHome() {
			window.location.href = "user_menu.html";
		}
		
		function gotoForm() {
			window.location.href = "form.php";
		}

		function gotoValidate() {
			window.location.href = "validate.php";
		}

		function gotoTaxable() {
			window.location.href = "taxable.php";
		}
		
		function gotoTax() {
			window.location.href = "tax.php";
		}

	</script>

</body>

</html>
