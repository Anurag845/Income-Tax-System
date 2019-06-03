<!DOCTYPE html>

<html>

<head>

	<style>
	
		body {
			margin: 0;
			padding: 0;
			font-size: 16;
		}
	
		td, th {
            text-align: left;
            padding: 8px;
        }
        
        .empdetails, table.empdetails td {
        	margin-top: 20px;
        	border: 1px solid black;
        }
        
        .decdetails, table.decdetails td {
        	margin-top: 60px;
        	border: 1px solid black;
        }
        
        .selname {
        	margin-top: 40px;
        }
        
        table {
        	border-collapse: collapse;
        }
        
        input, select {
        	padding: 4px;
        }
        
        .sel_name * {
			margin-left: 40px;
			margin-right: 40px;
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
			text-align: center;
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
			margin-bottom: 100px;
		}

		html {
    		overflow-y: scroll;
    	}

        input [type="submit"] {
        	background: white;
        	border: 1px solid grey;
        	padding: 4px;
        }
        
        .hidden_input {
        	display: none;
        }
        
	</style>

</head>

<body>

	<div class='heading'>
		
		<h1>Income Tax System</h1>
		
		<div class="topnav">
			<a onclick="gotoHome();">Home</a>
  			<a onclick="gotoForm();">Declaration Form</a>
  			<a onclick="gotoValidate();">Declaration Validation</a>
  			<a onclick="gotoTaxable();">Taxable Amount</a>
  			<a onclick="gotoTax();">Income Tax</a>
  			<a onclick="gotoLimit();">Exemption Limits</a>
  			<a onclick="gotoSlabs();">Tax Slabs</a>
  			<a onclick="gotoSalary();">Gross Salary</a>
  			<a onclick="gotoReset();">Reset</a>
		</div>
				
	</div>
	

	<?php
		include 'db_connect.php';

		$conn = OpenCon();
	
		$e_name = "select emp_name from Employee";
		$e_names = mysqli_query($conn,$e_name);

	?>
	
	<div class="contents">

	<center>
	
		<h2>
			Validate Amount Declared
		</h2>
	
		<table class="selname">
		
			<form method="POST" action="">
			
			<tr>
				<td><label for="staff_name">Select Employee Name</label></td>
				<td>
					<select id="staff_name" name="emp_name" required>
					<option value="">Select Name</option>
                    <?php
						while ($row = mysqli_fetch_array($e_names)) {
    						echo "<option value='" . $row['emp_name'] ."'>" . $row['emp_name'] ."</option>";
						}
					?>
                	</select>
                </td>
                <td>
                	<input type="submit" value="Check">
                </td>
			</tr>
			
			</form>
			
		</table>
		
		<table class="empdetails">
		
			<?php
			
				if(isset($_POST['emp_name'])) {
					$name = $_POST['emp_name'];
					$get_empid = "select emp_id,gross_sal from Employee where emp_name='$name'";
					$result = mysqli_query($conn,$get_empid);
					if(!$result) {
						echo mysqli_error($conn);
					}
					else {
						$temp = $result->fetch_assoc();
						$emp_id = $temp['emp_id'];
						$gross_sal = $temp['gross_sal'];

						echo "<tr>";
						echo "<td>" . 'Employee Id' . "</td>";
						echo "<td>" . 'Employee Name' . "</td>";
						echo "<td>" . 'Gross Salary' . "</td>";
						echo "</tr>";
						echo "<tr>";
						echo "<td>" . $emp_id . "</td>";
						echo "<td>" . $name . "</td>";
						echo "<td>" . $gross_sal . "</td>";
						echo "</tr>";
						echo "<br/>";
						echo "<br/>";
					}
				}
			
			?>
			
		</table>
		
		<form method="POST" action="process_adjust.php">
		
		<table class="decdetails">
			
			<?php
				
				if(isset($_POST['emp_name'])) {
					$name = $_POST['emp_name'];
					$get_empid = "select emp_id from Employee where emp_name='$name'";
					$result = mysqli_query($conn,$get_empid);
					if(!$result) {
						echo mysqli_error($conn);
					}
					else {
						$temp = $result->fetch_assoc();
						$emp_id = $temp['emp_id'];
						$get_details = "select dec_type,amount_declared,amount_proved,status,dec_id from Declarations where emp_id='$emp_id'";
						$details = mysqli_query($conn,$get_details);
						if(!$details) {
							echo mysqli_error($conn);
						}
						else {
							$cnt = 1;
							echo "<tr>";
							echo "<td rowspan='2'>" . 'Sr No' . "</td>";
							echo "<td rowspan='2'>" . 'Declaration Type' . "</td>";
							echo "<td rowspan='2'>" . 'Amount Declared' . "</td>";
							echo "<td rowspan='2'>" . 'Amount in Proof' . "</td>";
							echo "</tr>";
							echo "<tr>";
							echo "</tr>";
							
							while($row = mysqli_fetch_array($details))
							{
								$input_name = $row['dec_id'];
								echo "<tr>";
								echo "<td>" . $cnt . "</td>";
								echo "<td>" . $row['dec_type'] . "</td>";
								echo "<td>" . $row['amount_declared'] . "</td>";
								if($row['status'] == 'Pending') {
									echo "<td> <input type='number' name='" . $input_name . "'> </td>";									
								}
								else {
									echo "<td>" . $row['amount_proved'] . "</td>";
								}
								echo "</tr>";
								$cnt++;
							}
																					
							echo "<td colspan='4' style='text-align:center;padding: 30px;border-left-style:hidden;border-right-style:hidden;border-bottom-style:hidden;'> <input type='submit' value='Submit'> </td>";
						}
					}
				}
				else {
					
				}
			?>
			
		</table>
		
		<input type='hidden' name='emp_id' value='<?php echo "$emp_id";?>'>
		
		</form>
	
	</center>
	
	</div>
	
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
		
		function gotoSalary() {
			window.location.href = "salary.php";
		}
		
		function gotoReset() {
			window.location.href = "reset.php";
		}
	
	</script>

</body>

</html>
