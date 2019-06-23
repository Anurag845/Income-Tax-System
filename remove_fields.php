<!DOCTYPE html>

<html>

<head>

<style>

	html {
    	overflow-y: scroll;
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
	
	body {
		margin: 0;
		padding: 0;
		font-size: 16;
	}
	
	h1 {
		margin: 0;
		text-align: center;
		line-height: 70px;
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
		margin-top: 150px;
  		text-align: center;
	}

	button {
		background: white;
		border: 1px solid grey;
	}
	
	td {
		padding: 10px 30px;
	}

	form {
		margin-bottom: 80px;
	}
	
	table {
		margin-bottom: 40px;
	}
	
</style>


</head>

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
	
	<div class="contents">
	
		<h2>Remove entry</h2>
	
		<center>
		
			<form action="process_remove.php" method="POST">
			<?php
		
		 		include 'db_connect.php';
		 		$conn = OpenCon();
		 		
		 		echo "<table>";
		 			echo "<tr>";
		 				echo "<th>Sr No</th>";
		 				echo "<th>Field Name</th>";
		 				echo "<th>Value</th>";
		 				echo "<th>Delete Entry</th>";
		 			echo "<tr>";
		 
		 		$sql = "select * from Limits";
		 		$details = mysqli_query($conn,$sql);
		 		if(!$details) {
		 			echo mysqli_error($conn);
		 		}
		 		else {
		 			$s_no = 1;
		 			while($row = mysqli_fetch_array($details)) {
		 				if($row["sub_field"] == "yes") {
		 					echo "<tr>";
		 						echo "<td>" . $s_no . "</td>";
		 						echo "<td>" . $row["entry"] . "</td>";
		 						echo "<td></td>";
		 					echo "</tr>";
		 					$dec_id = $row["dec_id"];
		 					$get_sub = "select sub_field from Dec_sub_fields where field_id = '$dec_id'";
		 					$sub = mysqli_query($conn,$get_sub);
		 					if(!$sub) {
		 						echo mysqli_error($conn);
		 					}
		 					else {
		 						while($sub_row = mysqli_fetch_array($sub)) {
		 							echo "<tr>";
		 								echo "<td></td>";
		 								echo "<td>" . $sub_row['sub_field'] . "</td>";
		 								echo "<td></td>";
		 							echo "</tr>";
		 						}
		 					}
		 				}
		 				else {
		 					$entry = $row["entry"];
		 					$dec_id = $row["dec_id"];
		 					echo "<tr>";
		 						echo "<td>" . $s_no . "</td>";
		 						echo "<td>" . $entry . "</td>";
		 						echo "<td>" . $row["tax_limit"] . "</td>";
		 						echo "<td><input type='checkbox' name='$dec_id'></td>";
		 					echo "</tr>";
		 				}
		 				$s_no++;
		 			}
		 		}
		 		
		 		$get_deduc = "select * from Standard_Deduc";
		 		$deduc = mysqli_query($conn,$get_deduc);
		 		if(!$deduc) {
		 			echo mysqli_error($conn);
		 		}
		 		else {
		 			while($row = mysqli_fetch_array($deduc)) {
		 				$std_id = $row["ded_id"];
		 				echo "<tr>";
		 					echo "<td>" . $s_no . "</td>";
		 					echo "<td>" . $row["field"] . "</td>";
		 					echo "<td>" . $row["value"] . "</td>";
		 					echo "<td><input type='checkbox' name='$std_id'></td>";
		 				echo "</tr>";
		 				$s_no++;
		 			}
		 		}
		 		
		 		echo "</table>";
		
			?>
			<input type="submit" value="Submit">
			
			</form>
		</center>
	
	</div>

</body>

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

</html>
