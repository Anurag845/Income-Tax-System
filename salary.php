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
			padding-top: 10px;
			padding-bottom: 10px;
			padding-left: 40px;
			padding-right: 40px;
			text-align: left;
		}
		
		table {
			padding: 10px;
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
  			<a onclick="gotoLimit();">Exemption Limits</a>
  			<a onclick="gotoSlabs();">Tax Slabs</a>
  			<a onclick="gotoSalary();">Gross Salary</a>
  			<a onclick="gotoReset();">Reset</a>
  			<a onclick="gotoAddField();">Add Field</a>
  			<a onclick="gotoRemoveField();">Remove Field</a>
		</div>
				
	</div>

	<?php
		include 'db_connect.php';

		$conn = OpenCon();
		
		$salary = "select * from Employee";
		$all_sal = mysqli_query($conn,$salary);
	
		CloseCon($conn);

	?>
	
	<div class="contents">

		<center>

		<h2>Update Gross Salary</h2>

		<form action="submit_salary.php" method="POST">

		<table>

			<tr>
				<th>Id</th>
				<th>Name</th>
				<th>Salary</th>
			</tr>
	
		<?php
			
			$cnt = 0;
			while ($record = mysqli_fetch_array($all_sal)) {
   				echo"<tr>";
   					echo "<td>" . $record['emp_id'] . "</td>";
   					echo "<td>" . $record['emp_name'] . "</td>";
   					echo "<td> <input type='number' name='" . $record['emp_id'] . "' min='0' value='". $record['gross_sal'] ."'> </td>";
   				echo"</tr>";
   				$cnt++;

			}
		
		?>

		</table>

		<input type="submit" value="Submit" style="margin-top:20px;margin-bottom:40px;">
	
		</form>

	</center>
	
	<script>
	
		function gotoHome() {
			window.location.href = "admin_menu.html";
		}
	
		function gotoLimit() {
			window.location.href = "limits.php";
		}
		
		function gotoSlabs() {
			window.location.href = "tax_slabs.php";
		}
		
		function gotoSalary() {
			window.location.href = "salary.php";
		}
		
		function gotoReset() {
			window.location.href = "reset.php";
		}
		
		function gotoAddField() {
			window.location.href = "new_fields.php";
		}
		
		function gotoRemoveField() {
			window.location.href = "remove_fields.php";
		}
	
	</script>

</body>

</html>
