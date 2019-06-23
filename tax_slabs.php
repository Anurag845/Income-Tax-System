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
			padding: 10px;
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
	
	<div class="contents">

		<center>

		<h2>Update Tax Slabs</h2>

		<form action="submit_slabs.php" method="POST" onsubmit="return validate()">

		<table>

			<tr>
				<th>S.No</th>
				<th>Lower Bound</th>
				<th>Upper Bound</th>
				<th>Percentage</th>
			</tr>
	
		<?php
		
			include 'db_connect.php';
			$conn = OpenCon();
			
			$get_slabs = "select * from Tax_slabs";
			$slabs = mysqli_query($conn,$get_slabs);
			
			$s_no = 1;
			$cnt = 0;
			$percent_name = array("slab_1","slab_2","slab_3","slab_4","slab_5","slab_6","slab_7");
			$lb_name = array("lbslab_1","lbslab_2","lbslab_3","lbslab_4","lbslab_5","lbslab_6","lbslab_7");
			$ub_name = array("ubslab_1","ubslab_2","ubslab_3","ubslab_4","ubslab_5","ubslab_6","ubslab_7");
			while ($result = mysqli_fetch_array($slabs)) {
   				echo"<tr>";
   					echo "<td>" . $s_no . "</td>";
   					echo "<td> <input type='number' name='" . $lb_name{$cnt} . "' min='0' value='". $result['lower_boundary'] ."'> </td>";
   					echo "<td> <input type='number' name='" . $ub_name{$cnt} . "' min='0' value='". $result['upper_boundary'] ."'> </td>";
   					echo "<td> <input type='number' name='" . $percent_name{$cnt} . "' min='0' value='". $result['percent'] ."'> </td>";
   				echo"</tr>";
   				$cnt++;
   				$s_no++;
			}
			
			while($s_no < 8) {
				echo"<tr>";
   					echo "<td>" . $s_no . "</td>";
   					echo "<td> <input type='number' name='" . $lb_name{$cnt} . "' min='0' value='". $result['lower_boundary'] ."'> </td>";
   					echo "<td> <input type='number' name='" . $ub_name{$cnt} . "' min='0' value='". $result['upper_boundary'] ."'> </td>";
   					echo "<td> <input type='number' name='" . $percent_name{$cnt} . "' min='0' value='". $result['percent'] ."'> </td>";
   				echo"</tr>";
   				$s_no++;
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
	
		function validate() {
			var s1_p = document.getElementById('slab_1').value;
			var s2_p = document.getElementById('slab_2').value;
			var s3_p = document.getElementById('slab_3').value;
			var s4_p = document.getElementById('slab_4').value;
			var s5_p = document.getElementById('slab_5').value;
			var s6_p = document.getElementById('slab_6').value;
			var s7_p = document.getElementById('slab_7').value;
			
			var s1_lb = document.getElementById('lbslab_1').value;
			var s2_lb = document.getElementById('lbslab_2').value;
			var s3_lb = document.getElementById('lbslab_3').value;
			var s4_lb = document.getElementById('lbslab_4').value;
			var s5_lb = document.getElementById('lbslab_5').value;
			var s6_lb = document.getElementById('lbslab_6').value;
			var s7_lb = document.getElementById('lbslab_7').value;
			
			var s1_ub = document.getElementById('ubslab_1').value;
			var s2_ub = document.getElementById('ubslab_2').value;
			var s3_ub = document.getElementById('ubslab_3').value;
			var s4_ub = document.getElementById('ubslab_4').value;
			var s5_ub = document.getElementById('ubslab_5').value;
			var s6_ub = document.getElementById('ubslab_6').value;
			var s7_ub = document.getElementById('ubslab_7').value;
			
			if(s1_p=="" || s2_p=="" || s3_p=="" || s4_p=="" || s5_p=="" || s1_lb=="" || s2_lb=="" || s3_lb=="" || s4_lb=="" || s5_lb=="" || s1_ub=="" || s2_ub=="" || s3_ub=="" || s4_ub=="" || s5_ub=="") {
				alert("5 entries not complete");
				return false;
			}
			else if(s6_p!="") {
				if(s6_lb=="" || s6_ub=="") {
					alert("6th entry incomplete");
					return false;
				}
			}
			else if(s7_p!="") {
				if(s7_lb=="" || s7_ub=="") {
					alert("7th entry incomplete");
					return false;
				}
			}
			return true;
		}
	
	
	</script>

</body>

</html>
