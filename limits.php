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
  			<a onclick="gotoForm();">Declaration Form</a>
  			<a onclick="gotoLimit();">Exemption Limits</a>
  			<a onclick="gotoValidate();">Declaration Validation</a>
  			<a onclick="gotoTaxable();">Taxable Amount</a>
  			<a onclick="gotoSlabs();">Tax Slabs</a>
  			<a onclick="gotoTax();">Income Tax</a>
		</div>
				
	</div>

	<?php
		include 'db_connect.php';

		$conn = OpenCon();
		
		$limits = "select * from Limits";
		$all_limits = mysqli_query($conn,$limits);
	
		CloseCon($conn);

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
			$input_names = array("ann_rent","medi","home_int","nat_pen","phy_hand","edu_int","prof_tax","deduc","investments");
			while ($record = mysqli_fetch_array($all_limits)) {
   				echo"<tr>";
   					echo "<td>" . $s_no . "</td>";
   					echo "<td>" . $record['entry'] . "</td>";
   					echo "<td> <input type='number' name='" . $input_names{$cnt} . "' min='0' value='". $record['tax_limit'] ."'> </td>";
   				//echo "<th><a href='test.php'>Update</a> ";
   				echo"</tr>";
   				$cnt++;
   				$s_no++;
			}
		
			$investments = array("CPF","PPF","NSC","ULIP","Annual Insurance","Housing Loan Principal","Children Tuition Fee","Bank Deposit","Registration Fee");
			$s_no = 1;
		
			foreach ($investments as $value) {
   				echo "<tr>";
   				echo "<td>";
   				echo "<td>" . $s_no . ') ' . $value . "</td>";
   				echo "<td>";
   			//echo "<td> <input type='number' name='" . $input_names{$cnt} . "' min='0' value='". $record['tax_limit'] ."'> </td>";
   			//echo "<th><a href='test.php'>Update</a> ";
   				echo"</tr>";
   			
   				$s_no++;
			}
		
		?>

		</table>

		<input type="submit" value="Submit" style="margin-top:20px;margin-bottom:40px;">
	
		</form>

	</center>
	
	<script>
	
		function gotoHome() {
			window.location.href = "menu.html";
		}
		
		function gotoForm() {
			window.location.href = "form.php";
		}
	
		function gotoLimit() {
			window.location.href = "limits.php";
		}
	
		function gotoValidate() {
			window.location.href = "validate.php";
		}

		function gotoTaxable() {
			window.location.href = "taxable.php";
		}
		
		function gotoSlabs() {
			window.location.href = "tax_slabs.php";
		}

		function gotoTax() {
			window.location.href = "tax.php";
		}
	
	</script>

</body>

</html>
