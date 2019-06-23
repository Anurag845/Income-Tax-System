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
		
		#head2,#deduc {
			margin-top: 40px;
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
		
		$limits = "select * from Limits";
		$all_limits = mysqli_query($conn,$limits);
		
		$deduc = "select * from Standard_Deduc";
		$all_deducs = mysqli_query($conn,$deduc);

	?>
	
	<div class="contents">

		<center>

		<h2>Update Limits</h2>

		<form action="submit_limits.php" method="POST">

		<table>

			<tr>
				<th>S.No</th>
				<th>Entry</th>
				<th>Limit</th>
			</tr>
	
		<?php
			$s_no = 1;
			$cnt = 0;
			while ($record = mysqli_fetch_array($all_limits)) {
				echo"<tr>";
   					echo "<td>" . $s_no . "</td>";
   					echo "<td>" . $record['entry'] . "</td>";
   					echo "<td> <input type='number' name='". $record['dec_id'] ."' min='0' value='". $record['tax_limit'] ."'> </td>";
   				echo"</tr>";
				if($record['sub_field'] == "yes") {
					$get_subs = "select sub_field from Dec_sub_fields where field_id = '$record[dec_id]'";
					$subs = mysqli_query($conn,$get_subs);
					if(!$subs) {
						echo mysqli_error($conn);
					}
					else {
						$c = 1;
						while($row = mysqli_fetch_array($subs)) {
							echo "<tr>";
   								echo "<td></td>";
   								echo "<td>" . $c . ') ' . $row['sub_field'] . "</td>";
   								echo "<td>";
   							echo"</tr>";
   							$c++;
						}
					}
				}
   				
   				$cnt++;
   				$s_no++;
			}

			//CloseCon($conn);
		?>

		</table id="deduc">
		
		<h2 id="head2">Update Deduction Values</h2>

		<table>

			<tr>
				<th>S.No</th>
				<th>Deduction</th>
				<th>Value</th>
			</tr>
	
		<?php
			$s_no = 1;
			$cnt = 0;
			while ($record = mysqli_fetch_array($all_deducs)) {
				echo"<tr>";
   					echo "<td>" . $s_no . "</td>";
   					echo "<td>" . $record['field'] . "</td>";
   					$name = str_replace(' ', '', $record['field']);
   					echo "<td> <input type='number' name='$name' min='0' value='". $record['value'] ."'> </td>";
   				echo"</tr>";
				   				
   				$cnt++;
   				$s_no++;
			}

			//CloseCon($conn);
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

		function gotoTaxable() {
			window.location.href = "taxable.php";
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
