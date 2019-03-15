<!DOCTYPE html>

<head>

    <style>
    
    	html {
    		overflow-y: scroll;
    	}
    
        label {
            padding: 5px;
        }

        input [type="submit"] {
        	background: white;
        	border: 1px solid grey;
        	padding: 4px;
        }
        
        input, select {
        	padding: 4px;
        }
       
        table {
            padding: 0;
            border-collapse: collapse;
        }

        td, th {
            text-align: left;
            padding: 10px;
        }
        
        #dec_form {
        	margin-top: 50px;
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
		
		h2 {
			margin-top: 0;
			text-align: center;
		}
		
		.sel_name * {
			margin-left: 40px;
			margin-right: 40px;
		}
		
		.empdetails, table.empdetails td {
        	margin-top: 10px;
        	border: 1px solid black;
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
		
		$e_type = "select distinct emp_type from Employee";
		$e_types = mysqli_query($conn,$e_type);
	
		$e_name = "select emp_name from Employee";
		$e_names = mysqli_query($conn,$e_name);
		//CloseCon($conn);

	?>
	
	<div class='contents'>

    <h2>
        Declaration Entry
    </h2>

    <center>
            
		<form id="emp_name" action="" method="POST">

			<center>
                <table class="sel_name">
                
                	<tr>
                		<td><label for="staff_name">
                			Select Employee Name
                		</label></td>
                		<td>
                		<select id="staff_name" name="emp_name" required>
							<option value="">Select Name</option>
                    		<?php
								while ($row = mysqli_fetch_array($e_names)) {
    								echo "<option value='" . $row['emp_name'] . "'>" . $row['emp_name'] . "</option>";
								}
							?>
                		</select>
                		</td>
                		<td><input type="submit" value="Enter"></td>
                	</tr>
                </table>
                
			</center>
			
		</form>
		
		
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
		
		<form method="POST" action="process_form.php">
		
		<?php
		
			if(isset($_POST['emp_name'])) {
				$emp_name = $_POST['emp_name'];
				$get_id = "select emp_id from Employee where emp_name = '$emp_name'";
				$id = mysqli_query($conn,$get_id);
				if(!$id) {
					echo mysqli_error($conn);
				}
				else {
					$temp = $id->fetch_assoc();
					$emp_id = $temp['emp_id'];
					$get_details = "select * from Declarations where emp_id = '$emp_id'";
					$result = mysqli_query($conn,$get_details);
					if(!$result) {
						echo mysqli_error($conn);
					}
					else {
						$dec_id = array();
						$amt_dec = array();
						$amt_prov = array();
						
						$cnt = 0;
						while($row = mysqli_fetch_array($result)) {						
							$dec_id[$cnt] = $row['dec_id'];
							$amt_dec[$cnt] = $row['amount_declared'];
							$amt_prov[$cnt] = $row['amount_proved'];
							$cnt++;
						}
						
						echo "<table id='dec_form'>";
            				
            				echo "<tr>";
            					echo "<th>" . 'S.No.' . "</th>";
            					echo "<th>" . 'Particulars' . "</th>";
            					echo "<th>" . 'Amount' . "</th>";
            				echo "</tr>";
							
							$count = 0;
							foreach($dec_id as $type) {
								if($type == 'ann_rent') {
									break;
								}
								$count++;
							}
							if($count < $cnt) {
								if($amt_prov[$count] != 0) {
									$value = $amt_prov[$count];
									$value_type = 'proved';
								}
								else {
									$value = $amt_dec[$count];
									$value_type = 'declared';
								}
							}
							else {
								$value = null;
								$value_type = 'declared';
							}
							
            				echo "<tr>";
                				echo "<td>" . '01' . "</td>";
                    			echo "<td>" . 'Annual Rent (Only of residential unit not owned by employer)' . "</td>";
                    			if($value_type == 'proved') {
                    				echo "<td>" . $value . "<td>";
                    				echo "<td><input type='hidden' name='ann_rent'></td>";
                    			}
                    			else {
                    				echo "<td><input type='number' name='ann_rent' min='0' value='$value'></td>";
                    			}
                			echo "</tr>";
                			
                			$count = 0;
							foreach($dec_id as $type) {
								if($type == 'medi') {
									break;
								}
								$count++;
							}
							if($count < $cnt) {
								if($amt_prov[$count] != 0) {
									$value = $amt_prov[$count];
									$value_type = 'proved';
								}
								else {
									$value = $amt_dec[$count];
									$value_type = 'declared';
								}
							}
							else {
								$value = null;
								$value_type = 'declared';
							}

                    		echo "<tr>";
                        		echo "<td>" . '02' . "</td>";
                        		echo "<td>" . 'Mediclaim (U/s. 80D of I.T. Act)' . "</td>";
                        		if($value_type == 'proved') {
                    				echo "<td>" . $value . "<td>";
                    				echo "<td><input type='hidden' name='medi'></td>";
                    			}
                    			else {
                    				echo "<td><input type='number' name='medi' min='0' value='$value'></td>";
                    			}
                    		echo "</tr>";
                    		
                    		$count = 0;
							foreach($dec_id as $type) {
								if($type == 'home_int') {
									break;
								}
								$count++;
							}
							if($count < $cnt) {
								if($amt_prov[$count] != 0) {
									$value = $amt_prov[$count];
									$value_type = 'proved';
								}
								else {
									$value = $amt_dec[$count];
									$value_type = 'declared';
								}
							}
							else {
								$value = null;
								$value_type = 'declared';
							}

                    		echo "<tr>";
                        		echo "<td>" . '03' . "</td>";
                        		echo "<td>" . 'Interest paid for Home Loan' . "</td>";
                        		if($value_type == 'proved') {
                    				echo "<td>" . $value . "<td>";
                    				echo "<td><input type='hidden' name='home_int'></td>";
                    			}
                    			else {
                    				echo "<td><input type='number' name='home_int' min='0' value='$value'></td>";
                    			}
                    		echo "</tr>";
                    		
                    		$count = 0;
							foreach($dec_id as $type) {
								if($type == 'nat_pen') {
									break;
								}
								$count++;
							}
							if($count < $cnt) {
								if($amt_prov[$count] != 0) {
									$value = $amt_prov[$count];
									$value_type = 'proved';
								}
								else {
									$value = $amt_dec[$count];
									$value_type = 'declared';
								}
							}
							else {
								$value = null;
								$value_type = 'declared';
							}

                    		echo "<tr>";
                        		echo "<td>" . '04' . "</td>";
                        		echo "<td>" . 'National Pension' . "</td>";
                        		if($value_type == 'proved') {
                    				echo "<td>" . $value . "<td>";
                    				echo "<td><input type='hidden' name='nat_pen'></td>";
                    			}
                    			else {
                    				echo "<td><input type='number' name='nat_pen' min='0' value='$value'></td>";
                    			}
                    		echo "</tr>";
                    		
                    		$count = 0;
							foreach($dec_id as $type) {
								if($type == 'phy_hand') {
									break;
								}
								$count++;
							}
							if($count < $cnt) {
								if($amt_prov[$count] != 0) {
									$value = $amt_prov[$count];
									$value_type = 'proved';
								}
								else {
									$value = $amt_dec[$count];
									$value_type = 'declared';
								}
							}
							else {
								$value = null;
								$value_type = 'declared';
							}

                    		echo "<tr>";
                        		echo "<td>" . '05' . "</td>";
                        		echo "<td>" . 'Physically Handicap Above 70%' . "</td>";
                        		if($value_type == 'proved') {
                    				echo "<td>" . $value . "<td>";
                    				echo "<td><input type='hidden' name='phy_hand'></td>";
                    			}
                    			else {
                    				echo "<td><input type='number' name='phy_hand' min='0' value='$value'></td>";
                    			}                        
                    		echo "</tr>";
                    		
                    		$count = 0;
							foreach($dec_id as $type) {
								if($type == 'edu_int') {
									break;
								}
								$count++;
							}
							if($count < $cnt) {
								if($amt_prov[$count] != 0) {
									$value = $amt_prov[$count];
									$value_type = 'proved';
								}
								else {
									$value = $amt_dec[$count];
									$value_type = 'declared';
								}
							}
							else {
								$value = null;
								$value_type = 'declared';
							}

                    		echo "<tr>";
                        		echo "<td>" . '06' . "</td>";
                        		echo "<td>" . 'Education Loan Int.' . "</td>";
                        		if($value_type == 'proved') {
                    				echo "<td>" . $value . "<td>";
                    				echo "<td><input type='hidden' name='edu_int'></td>";
                    			}
                    			else {
                    				echo "<td><input type='number' name='edu_int' min='0' value='$value'></td>";
                    			}
                    		echo "</tr>";

                    		echo "<tr>";
                        		echo "<td>" . '07' . "</td>";
                        		echo "<td>" . 'Investments U/S. 80C of I.T. Act' . "</td>";
                        		echo "<td></td>";
                    		echo "</tr>";
                    		
                    		$count = 0;
							foreach($dec_id as $type) {
								if($type == 'cpf') {
									break;
								}
								$count++;
							}
							if($count < $cnt) {
								if($amt_prov[$count] != 0) {
									$value = $amt_prov[$count];
									$value_type = 'proved';
								}
								else {
									$value = $amt_dec[$count];
									$value_type = 'declared';
								}
							}
							else {
								$value = null;
								$value_type = 'declared';
							}

                    		echo "<tr>";
                        		echo "<td></td>";
                        		echo "<td>" . 'a. CPF' . "</td>";
                        		if($value_type == 'proved') {
                    				echo "<td>" . $value . "<td>";
                    				echo "<td><input type='hidden' name='cpf'></td>";
                    			}
                    			else {
                    				echo "<td><input type='number' name='cpf' min='0' value='$value'></td>";
                    			}
                    		echo "</tr>";
                    		
                    		$count = 0;
							foreach($dec_id as $type) {
								if($type == 'ppf') {
									break;
								}
								$count++;
							}
							if($count < $cnt) {
								if($amt_prov[$count] != 0) {
									$value = $amt_prov[$count];
									$value_type = 'proved';
								}
								else {
									$value = $amt_dec[$count];
									$value_type = 'declared';
								}
							}
							else {
								$value = null;
								$value_type = 'declared';
							}

                    		echo "<tr>";
                        		echo "<td></td>";
                        		echo "<td>" . 'b. PPF' . "</td>";
                        		if($value_type == 'proved') {
                    				echo "<td>" . $value . "<td>";
                    				echo "<td><input type='hidden' name='ppf'></td>";
                    			}
                    			else {
                    				echo "<td><input type='number' name='ppf' min='0' value='$value'></td>";
                    			}
                    		echo "</tr>";
                    		
                    		$count = 0;
							foreach($dec_id as $type) {
								if($type == 'nsc') {
									break;
								}
								$count++;
							}
							if($count < $cnt) {
								if($amt_prov[$count] != 0) {
									$value = $amt_prov[$count];
									$value_type = 'proved';
								}
								else {
									$value = $amt_dec[$count];
									$value_type = 'declared';
								}
							}
							else {
								$value = null;
								$value_type = 'declared';
							}

                    		echo "<tr>";
                        		echo "<td></td>";
                        		echo "<td>" . 'c. NSC' . "</td>";
                        		if($value_type == 'proved') {
                    				echo "<td>" . $value . "<td>";
                    				echo "<td><input type='hidden' name='nsc'></td>";
                    			}
                    			else {
                    				echo "<td><input type='number' name='nsc' min='0' value='$value'></td>";
                    			}
                    		echo "</tr>";
                    		
                    		$count = 0;
							foreach($dec_id as $type) {
								if($type == 'ulip') {
									break;
								}
								$count++;
							}
							if($count < $cnt) {
								if($amt_prov[$count] != 0) {
									$value = $amt_prov[$count];
									$value_type = 'proved';
								}
								else {
									$value = $amt_dec[$count];
									$value_type = 'declared';
								}
							}
							else {
								$value = null;
								$value_type = 'declared';
							}

                    		echo "<tr>";
                        		echo "<td></td>";
                        		echo "<td>" . 'd. ULIP/Mutual Fund/etc' . "</td>";
                        		if($value_type == 'proved') {
                    				echo "<td>" . $value . "<td>";
                    				echo "<td><input type='hidden' name='ulip'></td>";
                    			}
                    			else {
                    				echo "<td><input type='number' name='ulip' min='0' value='$value'></td>";
                    			}        
                    		echo "</tr>";
                    		
                    		$count = 0;
							foreach($dec_id as $type) {
								if($type == 'ann_ins') {
									break;
								}
								$count++;
							}
							if($count < $cnt) {
								if($amt_prov[$count] != 0) {
									$value = $amt_prov[$count];
									$value_type = 'proved';
								}
								else {
									$value = $amt_dec[$count];
									$value_type = 'declared';
								}
							}
							else {
								$value = null;
								$value_type = 'declared';
							}

                    		echo "<tr>";
                        		echo "<td></td>";
                        		echo "<td>" . 'e. Annual Insurance Premium' . "</td>";
                        		if($value_type == 'proved') {
                    				echo "<td>" . $value . "<td>";
                    				echo "<td><input type='hidden' name='ann_ins'></td>";
                    			}
                    			else {
                    				echo "<td><input type='number' name='ann_ins' min='0' value='$value'></td>";
                    			}
                    		echo "</tr>";
                    		
                    		$count = 0;
							foreach($dec_id as $type) {
								if($type == 'hsg_loan_prin') {
									break;
								}
								$count++;
							}
							if($count < $cnt) {
								if($amt_prov[$count] != 0) {
									$value = $amt_prov[$count];
									$value_type = 'proved';
								}
								else {
									$value = $amt_dec[$count];
									$value_type = 'declared';
								}
							}
							else {
								$value = null;
								$value_type = 'declared';
							}

                    		echo "<tr>";
                        		echo "<td></td>";
                        		echo "<td>" . 'f. Housing Loan Principal Amount' . "</td>";
                        		if($value_type == 'proved') {
                    				echo "<td>" . $value . "<td>";
                    				echo "<td><input type='hidden' name='hsg_loan_prin'></td>";
                    			}
                    			else {
                    				echo "<td><input type='number' name='hsg_loan_prin' min='0' value='$value'></td>";
                    			}
                    		echo "</tr>";
                    		
                    		$count = 0;
							foreach($dec_id as $type) {
								if($type == 'tuition_fee') {
									break;
								}
								$count++;
							}
							if($count < $cnt) {
								if($amt_prov[$count] != 0) {
									$value = $amt_prov[$count];
									$value_type = 'proved';
								}
								else {
									$value = $amt_dec[$count];
									$value_type = 'declared';
								}
							}
							else {
								$value = null;
								$value_type = 'declared';
							}

                    		echo "<tr>";
                        		echo "<td></td>";
                        		echo "<td>" . 'g. Tuition fee of children' . "</td>";
                        		if($value_type == 'proved') {
                    				echo "<td>" . $value . "<td>";
                    				echo "<td><input type='hidden' name='tuition_fee'></td>";
                    			}
                    			else {
                    				echo "<td><input type='number' name='tuition_fee' min='0' value='$value'></td>";
                    			}
                    		echo "</tr>";
                    		
                    		$count = 0;
							foreach($dec_id as $type) {
								if($type == 'bank_deposit') {
									break;
								}
								$count++;
							}
							if($count < $cnt) {
								if($amt_prov[$count] != 0) {
									$value = $amt_prov[$count];
									$value_type = 'proved';
								}
								else {
									$value = $amt_dec[$count];
									$value_type = 'declared';
								}
							}
							else {
								$value = null;
								$value_type = 'declared';
							}

                    		echo "<tr>";
                        		echo "<td></td>";
                        		echo "<td>" . 'h. Bank Deposit' . "</td>";	
                        		if($value_type == 'proved') {
                    				echo "<td>" . $value . "<td>";
                    				echo "<td><input type='hidden' name='bank_deposit'></td>";
                    			}
                    			else {
                    				echo "<td><input type='number' name='bank_deposit' min='0' value='$value'></td>";
                    			}
                    		echo "</tr>";							
                    		
                    		$count = 0;
							foreach($dec_id as $type) {
								if($type == 'reg_fee') {
									break;
								}
								$count++;
							}
							if($count < $cnt) {
								if($amt_prov[$count] != 0) {
									$value = $amt_prov[$count];
									$value_type = 'proved';
								}
								else {
									$value = $amt_dec[$count];
									$value_type = 'declared';
								}
							}
							else {
								$value = null;
								$value_type = 'declared';
							}

                    		echo "<tr>";
                        		echo "<td></td>";
                        		echo "<td>" . 'i. Registration Fee' . "</td>";
                        		if($value_type == 'proved') {
                    				echo "<td>" . $value . "<td>";
                    				echo "<td><input type='hidden' name='reg_fee'></td>";
                    			}
                    			else {
                    				echo "<td><input type='number' name='reg_fee' min='0' value='$value'></td>";
                    			}
                    		echo "</tr>";
                    		
                    		
                    		$get_ptax = "select tax_limit from Limits where entry = 'Profession Tax'";
							$res = mysqli_query($conn,$get_ptax);
							if(!$res) {
								echo mysqli_error($conn);
							}
							else {
								$temp = $res->fetch_assoc();
								$prof_tax = $temp['tax_limit'];
							}
							
							echo "<tr>";
                        		echo "<td>08</td>";
                        		echo "<td>" . 'Profession Tax' . "</td>";	
                        		echo "<td>" . $prof_tax . "</td>";
                    		echo "</tr>";
                    		
                    		$get_deduc = "select tax_limit from Limits where entry = 'Deduction'";
							$res = mysqli_query($conn,$get_deduc);
							if(!$res) {
								echo mysqli_error($conn);
							}
							else {
								$temp = $res->fetch_assoc();
								$deduction = $temp['tax_limit'];
							}
							
							echo "<tr>";
                        		echo "<td>09</td>";
                        		echo "<td>" . 'Standard Deduction' . "</td>";	
                        		echo "<td>" . $deduction . "</td>";
                    		echo "</tr>";
                    		
                    		echo "<tr>";
                    			echo "<td colspan='3' style='text-align:center'><input type='submit' value='Submit'></td>";
                    		echo "<tr>";
                    		
                			echo "<tr>";
            					echo "<td><input type='hidden' name='emp_name' value='$emp_name'></td>";
            				echo "</tr>";

                		echo "</table>";
					}
				}            
            }
            
            ?>
            
        </form>
    </center>
    
    </div>

    <script>

		/*onsubmit="return validate()
		  removed from dec_form because empty can be submitted*/
	
        function validate() {
            var a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,text;
            var count = 0;
            text = "Invalid";
            a = document.getElementsByName("ann_rent")[0].value;
            b = document.getElementsByName("medi")[0].value;
            c = document.getElementsByName("home_int")[0].value;
            d = document.getElementsByName("nat_pen")[0].value;
            e = document.getElementsByName("phy_hand")[0].value;
            f = document.getElementsByName("edu_int")[0].value;
            g = document.getElementsByName("cpf")[0].value;
            h = document.getElementsByName("ppf")[0].value;
            i = document.getElementsByName("nsc")[0].value;
            j = document.getElementsByName("ann_ins")[0].value;
            k = document.getElementsByName("ulip")[0].value;
            l = document.getElementsByName("hsg_loan_prin")[0].value;
            m = document.getElementsByName("tuition_fee")[0].value;
            n = document.getElementsByName("bank_deposit")[0].value;
            o = document.getElementsByName("reg_fee")[0].value;

            if(a=="" && b=="" && c=="" && d=="" && e=="" && f=="" && g=="" && h=="" && i=="" && j=="" && k=="" && l=="" && m=="" && n=="" && o=="") {
                alert("All fields are empty");
                return false;
            }

        }
        
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
