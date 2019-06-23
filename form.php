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
            padding-top: 10px;
            padding-bottom: 10px;
            padding-left: 20px;
            padding-right: 20px;
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
        
        .dec_type {
        	margin-top: 60px;
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
  			<a onclick="gotoValidate();">Declaration Validation</a>
  			<a onclick="gotoTaxable();">Taxable Amount</a>
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
		
		
		<?php
		
			echo "<table class='empdetails'>";	
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
			echo "</table>";
			
			if(isset($_POST['emp_name'])) {
				echo "<form method='POST' action=''>";
				echo "<table class='dec_type'>";
					echo "<tr>";
						echo "<td><input type='submit' name='dec_option' value='New'></td>";
						echo "<td><input type='submit' name='dec_option' value='Update'></td>";
						echo "<td><input type='submit' name='dec_option' value='Delete'></td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td><input type='hidden' name='emp_name' value='$_POST[emp_name]'></td>";
					echo "</tr>";
				echo "</table>";
				echo "</form>";
			}
		?>
			
		
		<form method="POST" action="process_form.php">
		
		<?php
		
			if(isset($_POST['dec_option'])) {
				$emp_name = $_POST['emp_name'];
				$get_id = "select emp_id from Employee where emp_name = '$emp_name'";
				$id = mysqli_query($conn,$get_id);
				if(!$id) {
					echo mysqli_error($conn);
				}
				else {
					$temp = $id->fetch_assoc();
					$emp_id = $temp['emp_id'];
				}
				
				$option = $_POST['dec_option'];
				if($option == 'New' || $option == 'Delete') {
					$del = "delete from Declarations where emp_id = '$emp_id'";
					mysqli_query($conn,$del);
					$del = "delete from Taxable_monthly where emp_id = '$emp_id'";
					mysqli_query($conn,$del);
					$del = "delete from Tax_monthly where emp_id = '$emp_id'";
					mysqli_query($conn,$del);
				}
				if($option == 'New' || $option == 'Update') {
				
					if(isset($_POST['dec_option'])) {
						
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
            				
            					$get_fields = "select entry,sub_field,dec_id from Limits";
            					$fields = mysqli_query($conn,$get_fields);
            					if(!$fields) {
            						echo mysqli_error($conn);
            					}
            					else {
            						$sr_num = array();
            						$descriptions = array();
            						$cat_id = array();
            						$sub_present = array();
            						$cnt_var = 0;
            						while($row = mysqli_fetch_array($fields)) {
            							$descriptions[$cnt_var] = $row['entry'];
            							$sr_num[$cnt_var] = $cnt_var+1;
            							$cat_id[$cnt_var] = $row['dec_id'];
            							$sub_present[$cnt_var] = $row['sub_field'];
            							$cnt_var++;
            						}
            					}
            				
            					for($dec_counter = 0; $dec_counter < $cnt_var; $dec_counter++) {
            					
            						$count = 0;
									foreach($dec_id as $type) {
										if($type == $cat_id[$dec_counter]) {
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
							
									if($sub_present[$dec_counter] == "no") {
            							echo "<tr>";
                							echo "<td>" . $sr_num[$dec_counter] . "</td>";
                    						echo "<td>" . $descriptions[$dec_counter] . "</td>";
                    						if($value_type == 'proved') {
                    							echo "<td>" . $value . "<td>";
                    							echo "<td><input type='hidden' name='$cat_id[$dec_counter]'></td>";
                    						}
                    						else {
                    							echo "<td><input type='number' name='$cat_id[$dec_counter]' min='0' value='$value'></td>";
                    						}
                						echo "</tr>";
                					}
                					else {
                						echo "<tr>";
                        					echo "<td>" . $sr_num[$dec_counter] . "</td>";
                        					echo "<td>" . $descriptions[$dec_counter] . "</td>";
                        					echo "<td><input type='hidden' name='$cat_id[$dec_counter]' value=0></td>";
                        					echo "<td></td>";
                    					echo "</tr>";
                    					
                    					$get_sub_fields = "select * from Dec_sub_fields";
            							$sub_fields = mysqli_query($conn,$get_sub_fields);
            							if(!$sub_fields) {
            								echo mysqli_error($conn);
            							}
            							else {
            								$subs = array();
            								$sub_ids = array();
            								$c = 0;
            								while($row = mysqli_fetch_array($sub_fields)) {
            									$subs[$c] = $row['sub_field'];
            									$sub_ids[$c] = $row['sub_id'];
            									$c++;
            								}
            							}
                    		
                    		
               							for($sub_counter = 0; $sub_counter < $c; $sub_counter++) {
               				
               								$count = 0;
											foreach($dec_id as $type) {
												if($type == $sub_ids[$sub_counter]) {
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
                        						echo "<td>" . $subs[$sub_counter] . "</td>";
                        						if($value_type == 'proved') {
                    								echo "<td>" . $value . "<td>";
                    								echo "<td><input type='hidden' name='$sub_ids[$sub_counter]'></td>";
                    							}
                    							else {
                    								echo "<td><input type='number' name='$sub_ids[$sub_counter]' min='0' value='$value'></td>";
                    							}
                    						echo "</tr>";
               				
               							}
                					}
            				
            					}
            					
            					$get_fixed = "select * from Standard_Deduc";
                    			$fixed = mysqli_query($conn,$get_fixed);
                    			
                    			if(!$fixed) {
                    				echo mysqli_error($conn);
                    			}
                    			else {
                    				$cnt_var++;
                    				while($row = mysqli_fetch_array($fixed)) {
                    					$fixed_name = $row['field'];
                    					$fixed_value = $row['value'];
                    					echo "<tr>";
                        					echo "<td>" . $cnt_var . "</td>";
                        					echo "<td>" . $fixed_name . "</td>";	
                        					echo "<td>" . $fixed_value . "</td>";
                    					echo "</tr>";
                    					$cnt_var++;
                    				}
                    			}
                    		
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
			}
            
        ?>
            
        </form>
    </center>
    
    </div>

    <script>
		
		function validate() {
			var name = document.getElementById("staff_name").value;
			if(name == "") {
				alert("Employee name not selected.");
				return false;
			}
			else {
				return true;
			}
		}
		
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
