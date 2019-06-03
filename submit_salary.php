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
	
	<div class="contents">

		<?php
		
			$month = date('m');
			if($month <= 3) {
				$no_of_months = 4 - $month;
			}
			else {
				$no_of_months = 12 - $month + 4;
			}

			include 'db_connect.php';

			$conn = OpenCon();
	
			$sql = "select * from Employee";
			$get_names = mysqli_query($conn,$sql);
			$names = array();
			$emp_ids = array();
			$salaries = array();
			$cnt = 0;
			if(!$get_names) {
				echo mysqli_error($conn);
			}
			else {
				while($result = mysqli_fetch_array($get_names)) {
					$names[$cnt] = $result['emp_name'];
					$emp_ids[$cnt] = $result['emp_id'];
					$salaries[$cnt] = $result['gross_sal'];
					$cnt++;
				}
			}
			
			$get_limits = "select tax_limit from Limits";
			$result = mysqli_query($conn,$get_limits);
			
			$categories = array('ann_rent','medi','home_int','nat_pen','phy_hand','edu_int','prof_tax','deduc','investments');
			$limits = array();
			
			if(!$result) {
				echo mysqli_error($conn);
			}
			else {
				$count = 0;
				while($row = mysqli_fetch_array($result)) {
					$limits[$count] = $row['tax_limit'];
					$count++;
				}
				
				$ann_rent = $limits[0];
				$medi = $limits[1];
				$home_int = $limits[2];
				$nat_pen = $limits[3];
				$phy_hand = $limits[4];
				$edu_int = $limits[5];
				$prof_tax = $limits[6];
				$deduc = $limits[7];
				$investments = $limits[8];
			}
			
			$get_slabs = "select * from Tax_slabs";
			$slabs = mysqli_query($conn,$get_slabs);
		
			$lower_bound = array();
			$upper_bound = array();
			$percent = array();
			$cnt = 0;
		
			if(!$slabs) {
				echo mysqli_error($conn);
			}
			else {
				while($result = mysqli_fetch_array($slabs)) {
					$lower_bound[$cnt] = $result['lower_boundary'];
					$upper_bound[$cnt] = $result['upper_boundary'];
					$percent[$cnt] = $result['percent'];
					$cnt += 1;
				}
			}
			
			for($sal_counter = 0; $sal_counter < $cnt; $sal_counter++) {
				
				$emp_id = $emp_ids[$sal_counter];
				if(!empty($_POST[$emp_id]) && $_POST[$emp_id] != "") {
					$new_sal = $_POST[$emp_id];
					$old_sal = $salaries[$sal_counter];
					
					if($new_sal != $old_sal) {
						
						$update = "update Employee set gross_sal = $new_sal where emp_id = '$emp_id'";
						if(mysqli_query($conn,$update)) {
							echo "Salary updated successfully. ";
						}
						else {
							echo mysqli_error($conn);
						}
						
						$annual_taxable = $new_sal;
						$get_dec = "select * from Declarations where emp_id = '$emp_id'";
						$dec = mysqli_query($conn,$get_dec);
						if(!$dec) {
							echo mysqli_error($conn);
						}
						else {
							$invests = 0;
							while($result = mysqli_fetch_array($dec)) {
								$dec_id = $result['dec_id'];
								$desc = $result['dec_type'];
								$status = $result['status'];
								
								if($status == 'Pending') {
									$val = $result['amount_declared'];
								}
								else {
									$val = $result['amount_proved'];
								}
								
								if($dec_id == 'ann_rent') {
									if($val < $ann_rent) {
										$annual_taxable -= $val;
									}
									else {
										$annual_taxable -= $ann_rent;
									}
								}
								else if($dec_id == 'medi') {
									if($val < $medi) {
										$annual_taxable -= $val;
									}
									else {
										$annual_taxable -= $medi;
									}
								}
								else if($dec_id == 'home_int') {
									if($val < $home_int) {
										$annual_taxable -= $val;
									}
									else {
										$annual_taxable -= $home_int;
									}
								}
								else if($dec_id == 'nat_pen') {
									if($val < $nat_pen) {
										$annual_taxable -= $val;
									}
									else {
										$annual_taxable -= $nat_pen;
									}
								}
								else if($dec_id == 'phy_hand') {
									if($val < $phy_hand) {
										$annual_taxable -= $val;
									}
									else {
										$annual_taxable -= $phy_hand;
									}
								}
								else if($dec_id == 'edu_int') {
									if($val < $edu_int) {
										$annual_taxable -= $val;
									}
									else {
										$annual_taxable -= $edu_int;
									}
								}
								else if($dec_id=='cpf' || $dec_id=='ppf' || $dec_id=='nsc' || $dec_id=='ulip' || $dec_id=='ann_ins' || $dec_id=='hsg_loan_prin' || $dec_id=='tuition_fee' || $dec_id=='bank_deposit' || $dec_id=='reg_fee') {
									$invests += $val;
								}
								
							}
							
							if($invests < $investments) {
								$annual_taxable -= $invests;
							}
							else {
								$annual_taxable -= $investments;
							}
					
							$annual_taxable -= $prof_tax;
							$annual_taxable -= $deduc;
					
							$update_annual = "update Taxable_monthly set Annual = '$annual_taxable' where emp_id = '$emp_id'";
							mysqli_query($conn,$update_annual);
							
							$april = $result['April'];
 							$may = $result['May'];
 							$june = $result['June'];
 							$july = $result['July'];
 							$august = $result['August'];
							$september = $result['September'];
							$october = $result['October'];
							$november = $result['November'];
							$december = $result['December'];
							$january = $result['January'];
							$february = $result['February'];
							$march = $result['March'];
							$tax_months = array($april,$may,$june,$july,$august,$september,$october,$november,$december,$january,$february,$march);
					//}
 				
							$cnt = 0;
							$taxable = 0;
							while($cnt < 12-$no_of_months) {
								$taxable += $tax_months[$cnt];
								$cnt += 1;
							}
 				
							$adjust = $annual_taxable - $taxable;
							$adjust_monthly = ceil($adjust/$no_of_months);
 				
							while($cnt < 12) {
								$tax_months[$cnt] = $adjust_monthly;
								$cnt += 1;
							}
							
							$count = 0;
							$adjusted_taxable = 0;
							while($count < 12) {
								$adjusted_taxable += $tax_months[$count];
								$count++;
							}
 				
							$sql = "update Taxable_monthly set April = '$tax_months[0]', May = '$tax_months[1]', June = '$tax_months[2]', July = '$tax_months[3]', August = '$tax_months[4]', September = '$tax_months[5]', October = '$tax_months[6]', November = '$tax_months[7]', December = '$tax_months[8]', January = '$tax_months[9]', February = '$tax_months[10]', March = '$tax_months[11]', Adjusted = '$adjusted_taxable' where emp_id = '$emp_id'";
			
							if(mysqli_query($conn,$sql)) {
								$taxable_flag = true;
						//echo 'Taxable updated successfully. ';
							}
							else {
								$taxable_monthly = false;
								echo mysqli_error($conn);
							}
			
							$cnt = 0;
							$new_tax = 0;
							while($adjusted_taxable > $upper_bound[$cnt]) {
								$new_tax += ($upper_bound[$cnt]-$lower_bound[$cnt])*$percent[$cnt];
								$cnt++;
							}
							$new_tax += ($adjusted_taxable-$lower_bound[$cnt])*$percent[$cnt];
							$new_tax = ceil($new_tax/100);
							
		
							$get_tax = "select * from Tax_monthly where emp_id = '$emp_id'";
							$res_tax = mysqli_query($conn,$get_tax);
		
							if(!$res_tax) {
								echo mysqli_error($conn);
							}
							else {
								$result = $res_tax->fetch_assoc();
								$april = $result['April'];
 								$may = $result['May'];
 								$june = $result['June'];
 								$july = $result['July'];
 								$august = $result['August'];
								$september = $result['September'];
								$october = $result['October'];
								$november = $result['November'];
								$december = $result['December'];
								$january = $result['January'];
								$february = $result['February'];
								$march = $result['March'];
								$tax_months = array($april,$may,$june,$july,$august,$september,$october,$november,$december,$january,$february,$march);
							}
 				
							$cnt = 0;
							$tax_ald = 0;
							while($cnt < 12-$no_of_months) {
								$tax_ald += $tax_months[$cnt];
								$cnt += 1;
							}
 				
							$adjust = $new_tax - $tax_ald;
							$adjust_monthly = ceil($adjust/$no_of_months);
 				
							while($cnt < 12) {
								$tax_months[$cnt] = $adjust_monthly;
								$cnt += 1;
							}
 				
 				
 							$count = 0;
							$adjusted_tax = 0;
							while($count < 12) {
								$adjusted_tax += $tax_months[$count];
								$count++;
							}
							
							if($adjusted_taxable <= 500000) {
								$new_tax = 0;
								$adjusted_tax = 0;
								$count = 0;
								while($count < 12) {
									$tax_months[$count] = 0;
									$count++;
								}
							}
					
							$edu_cess = ceil($adjusted_tax*0.04);
 					
							$sql = "update Tax_monthly set April = '$tax_months[0]', May = '$tax_months[1]', June = '$tax_months[2]', July = '$tax_months[3]', August = '$tax_months[4]', September = '$tax_months[5]', October = '$tax_months[6]', November = '$tax_months[7]', December = '$tax_months[8]', January = '$tax_months[9]', February = '$tax_months[10]', March = '$tax_months[11]', Annual = '$new_tax', Adjusted = '$adjusted_tax', Edu_Cess = '$edu_cess' where emp_id = '$emp_id'";
		
							if(mysqli_query($conn,$sql)) {
								$tax_flag = true;
						//echo 'Tax updated successfully.';
							}
							else {
								echo mysqli_error($conn);
							}
						}
						if($taxable_flag == true and $tax_flag == true) {
							echo 'Taxable updated successfully. Tax updated successfully.';
						}	
					}
					
				}
				else {
					//echo 'Salary field empty';
				}
				
			}
			
			CloseCon($conn);

		?>

		<br>
		<br>
	
		<button onclick="goBack();">Back</button>
	
	</div>

</body>

<script>

	function goBack() {
		window.location.href = "salary.php";
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
	
	function gotoSalary() {
		window.location.href = "salary.php";
	}
	
	function gotoReset() {
		window.location.href = "reset.php";
	}

</script>

</html>
