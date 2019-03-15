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
  			<a onclick="gotoLimit();">Exemption Limits</a>
  			<a onclick="gotoValidate();">Declaration Validation</a>
  			<a onclick="gotoTaxable();">Taxable Amount</a>
  			<a onclick="gotoSlabs();">Tax Slabs</a>
  			<a onclick="gotoTax();">Income Tax</a>
		</div>
				
	</div>
	
	<div class="contents">

		<?php

			include 'db_connect.php';

			$conn = OpenCon();
	
			$ann_rent = $_POST["ann_rent"];
			$medi = $_POST["medi"];
			$home_int = $_POST["home_int"];
			$nat_pen = $_POST["nat_pen"];
			$phy_hand = $_POST["phy_hand"];
			$edu_int = $_POST["edu_int"];
			$prof_tax = $_POST["prof_tax"];
			$deduc = $_POST["deduc"];
			$investments = $_POST["investments"];
	
			mysqli_query($conn,'Delete from Limits');
	
			$sql = "insert into Limits(entry,tax_limit) values ('Annual Rent','$ann_rent'),('Mediclaim',$medi),('Home Interest',$home_int),('National Pension',$nat_pen),('Physically Handicap',$phy_hand),('Education Interest',$edu_int),('Profession Tax',$prof_tax),('Deduction',$deduc),('Investments',$investments)";
	
			if(mysqli_query($conn,$sql)) {
    			echo "Limits updated successfully. ";
			} 
			else {
    			echo mysqli_error($conn);
			}
			
			$month = date('m');
			if($month < 3) {
				$no_of_months = 3 - $month;
			}
			else {
				$no_of_months = 12 - $month + 3;
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
			
			
			$no_of_emp = "select * from Taxable_monthly";
			$res = mysqli_query($conn,$no_of_emp);
			
			if(!$res) {
				echo mysqli_error($conn);
			}
			else {
				$emp_cnt = mysqli_num_rows($res);
				while($result = mysqli_fetch_array($res)) {
					$emp_id = $result['emp_id'];
					
					$get_gross = "select gross_sal from Employee where emp_id = '$emp_id'";
					$gross = mysqli_query($conn,$get_gross);
					$temp = $gross->fetch_assoc();
					$gross_sal = $temp['gross_sal'];
					
					$annual_taxable = $gross_sal;
					$invests = 0;
					
					$get_dec = "select * from Declarations where emp_id='$emp_id'";
					$decs = mysqli_query($conn,$get_dec);
					while($dec_details = mysqli_fetch_array($decs)) {
						$dec_id = $dec_details['dec_id'];
						$status = $dec_details['status'];
						if($status == 'Pending') {
							$ent = $dec_details['amount_declared'];
						}
						else if($status == 'Proved') {
							$ent = $dec_details['amount_proved'];
						}
						
						if($dec_id == 'ann_rent') {
							if($ent < $ann_rent) {
								$annual_taxable -= $ent;
							}
							else {
								$annual_taxable -= $ann_rent;
							}
						}
						else if($dec_id == 'medi') {
							if($ent < $medi) {
								$annual_taxable -= $ent;
							}
							else {
								$annual_taxable -= $medi;
							}
						}
						else if($dec_id == 'home_int') {
							if($ent < $home_int) {
								$annual_taxable -= $ent;
							}
							else {
								$annual_taxable -= $home_int;
							}
						}
						else if($dec_id == 'nat_pen') {
							if($ent < $nat_pen) {
								$annual_taxable -= $ent;
							}
							else {
								$annual_taxable -= $nat_pen;
							}
						}
						else if($dec_id == 'phy_hand') {
							if($ent < $phy_hand) {
								$annual_taxable -= $ent;
							}
							else {
								$annual_taxable -= $phy_hand;
							}
						}
						else if($dec_id == 'edu_int') {
							if($ent < $edu_int) {
								$annual_taxable -= $ent;
							}
							else {
								$annual_taxable -= $edu_int;
							}
						}
						else if($dec_id=='cpf' || $dec_id=='ppf' || $dec_id=='nsc' || $dec_id=='ulip' || $dec_id=='ann_ins' || $dec_id=='hsg_loan_prin' || $dec_id=='tuition_fee' || $dec_id=='bank_deposit' || $dec_id=='reg_fee') {
							$invests += $ent;
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
 				
					/*$sql = "select * from Taxable_monthly where emp_id = '$emp_id'";
					$get_details = mysqli_query($conn,$sql);
					if(!$get_details) {
						echo mysqli_error($conn);
					}
					else {
						$result = $get_details->fetch_assoc();*/
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
					$adjust_monthly = $adjust/$no_of_months;
 				
					while($cnt < 12) {
						$tax_months[$cnt] = $adjust_monthly;
						$cnt += 1;
					}
 				
					$sql = "update Taxable_monthly set April = '$tax_months[0]', May = '$tax_months[1]', June = '$tax_months[2]', July = '$tax_months[3]', August = '$tax_months[4]', September = '$tax_months[5]', October = '$tax_months[6]', November = '$tax_months[7]', December = '$tax_months[8]', January = '$tax_months[9]', February = '$tax_months[10]', March = '$tax_months[11]' where emp_id = '$emp_id'";
			
					if(mysqli_query($conn,$sql)) {
						$taxable_flag = true;
						//echo 'Taxable updated successfully. ';
					}
					else {
						echo mysqli_error($conn);
					}
			
					$cnt = 0;
					$new_tax = 0;
					while($annual_taxable > $upper_bound[$cnt]) {
						$new_tax += ($upper_bound[$cnt]-$lower_bound[$cnt])*$percent[$cnt];
						$cnt++;
					}
					$new_tax += ($annual_taxable-$lower_bound[$cnt])*$percent[$cnt];
					$new_tax = $new_tax/100;
		
					$get_tax = "select * from Tax_monthly where emp_id = '$emp_id'";
					$res_tax = mysqli_query($conn,$get_tax);
		
					if(!$res) {
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
					$adjust_monthly = $adjust/$no_of_months;
 				
					while($cnt < 12) {
						$tax_months[$cnt] = $adjust_monthly;
						$cnt += 1;
					}
 				
 					//echo $new_tax;
 					
					$sql = "update Tax_monthly set April = '$tax_months[0]', May = '$tax_months[1]', June = '$tax_months[2]', July = '$tax_months[3]', August = '$tax_months[4]', September = '$tax_months[5]', October = '$tax_months[6]', November = '$tax_months[7]', December = '$tax_months[8]', January = '$tax_months[9]', February = '$tax_months[10]', March = '$tax_months[11]', Annual = '$new_tax' where emp_id = '$emp_id'";
		
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
			
			CloseCon($conn);

		?>

		<br>
		<br>
	
		<button onclick="goBack();">Back</button>
	
	</div>

</body>

<script>

	function goBack() {
		window.location.href = "limits.php";
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

</html>
