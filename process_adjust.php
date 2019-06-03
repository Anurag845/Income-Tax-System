<!DOCTYPE html>

<html>

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
		</div>
				
	</div>

<div class="contents">

<?php
	include 'db_connect.php';

	$conn = OpenCon();
	
	$month = date('m');
	$emp_id = $_POST['emp_id'];
		
	if($month <= 3) {
		$no_of_months = 4 - $month;
	}
	else {
		$no_of_months = 12 - $month + 4;
	}
		
	$sql = "select Annual from Taxable_monthly where emp_id = '$emp_id'";
	$get_taxable = mysqli_query($conn,$sql);
	if(!$get_taxable) {
		echo mysqli_error($conn);
	}
	else {
		$temp = $get_taxable->fetch_assoc();
		$annual_taxable = $temp['Annual'];
	}
	
	$dec_ids = array('ann_rent','medi','home_int','nat_pen','phy_hand','edu_int');
	$entry_name = array('Annual Rent','Mediclaim','Home Interest','National Pension','Physically Handicap','Education Interest');
	
	for($dec_counter = 0; $dec_counter < 6; $dec_counter++) {
	
		$decid = $dec_ids[$dec_counter];
		$decname = $entry_name[$dec_counter];
		if(!empty($_POST[$decid]) && $_POST[$decid] != "") {
	
			$prov_value = $_POST[$decid];

			$sql = "update Declarations set amount_proved = '$prov_value', status = 'Proved' where emp_id = '$emp_id' and dec_id = '$decid'";
			mysqli_query($conn,$sql);
			
			$get_declared = "select amount_declared from Declarations where emp_id = '$emp_id' and dec_id = '$decid'";
			$dec = mysqli_query($conn,$get_declared);
			if(!$dec) {
				echo mysqli_error($conn);
			}
			else {
				$temp = $dec->fetch_assoc();
				$dec_value = $temp['amount_declared'];
			}
			if($dec_value != $prov_value) {
				$get_limit = "select tax_limit from Limits where entry='$decname'";
				$result = mysqli_query($conn,$get_limit);
				if(!$result) {
					echo mysqli_error($conn);
				}
				else {
					$temp = $result->fetch_assoc();
					$limit = $temp['tax_limit'];
				}
				if($dec_value < $limit) {
					$annual_taxable = $annual_taxable + $dec_value;
				}
				else {
					$annual_taxable = $annual_taxable + $limit;
				}
				if($prov_value < $limit) {
					$annual_taxable = $annual_taxable - $prov_value;
				}
				else {
					$annual_taxable = $annual_taxable - $limit;
				}
				
				$update_annual = "update Taxable_monthly set Annual = '$annual_taxable' where emp_id = '$emp_id'";
				mysqli_query($conn,$update_annual);
 				
				$sql = "select * from Taxable_monthly where emp_id = '$emp_id'";
				$get_details = mysqli_query($conn,$sql);
				if(!$get_details) {
					echo mysqli_error($conn);
				}
				else {
					$result = $get_details->fetch_assoc();
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
 				
				mysqli_query($conn,$sql);
			}
		}
	
	}
	
	$invest_ids = array('cpf','ppf','nsc','ulip','ann_ins','hsg_loan_prin','tuition_fee','bank_deposit','reg_fee');
	$invest_desc = array('CPF','PPF','NSC','ULIP','Annual Insurance','Housing Loan Principal','Children Tuition Fee','Bank Deposit','Registration Fee');
	
	$old_inv = 0;
	$new_inv = 0;
	
	for($inv_counter = 0; $inv_counter < 9; $inv_counter++) {
	
		$invest_id = $invest_ids[$inv_counter];
		$desc = $invest_desc[$inv_counter];
		if(!empty($_POST[$invest_id]) && $_POST[$invest_id] != "") {
			$p_value = $_POST[$invest_id];
			$get_value = "select amount_declared from Declarations where dec_id = '$invest_id' and emp_id = '$emp_id'";
			$res = mysqli_query($conn,$get_value);
			if(!$res) {
				echo mysqli_error($conn);
			}
			else {
				$temp = $res->fetch_assoc();
				$value = $temp['amount_declared'];
			}
			
			$new_inv += $p_value;
			$sql = "update Declarations set amount_proved = '$p_value', status = 'Proved' where emp_id = '$emp_id' and dec_id = '$invest_id'";
			mysqli_query($conn,$sql);
		}
		else {
			$get_value = "select amount_declared,amount_proved,status from Declarations where emp_id = '$emp_id' and dec_id = '$invest_id'";
			$res = mysqli_query($conn,$get_value);
			$num_rows = mysqli_num_rows($res);
			if($num_rows > 0) {
				$temp = $res->fetch_assoc();
				$status = $temp['status'];
				if($status == 'Pending') {
					$value = $temp['amount_declared'];
				}
				else if($status == 'Proved') {
					$value = $temp['amount_proved'];
				}
			}
			else {
				$value = 0;
			}
			$new_inv += $value;
		}
		
		$old_inv += $value;
		
	}
	
	$get_limit = "select tax_limit from Limits where entry='Investments'";
	$res = mysqli_query($conn,$get_limit);
	$temp = $res->fetch_assoc();
	$limit = $temp['tax_limit'];
	
	if($old_inv > $limit) {
		$annual_taxable = $annual_taxable + $limit;
	}
	else {
		$annual_taxable = $annual_taxable + $old_inv;
	}
	
	if($new_inv > $limit) {
		$annual_taxable = $annual_taxable - $limit;
	}
	else {
		$annual_taxable = $annual_taxable - $new_inv;
	}
	
	//echo 'Annual Taxable' . $annual_taxable;
	
			$update_annual = "update Taxable_monthly set Annual = '$annual_taxable' where emp_id = '$emp_id'";
			mysqli_query($conn,$update_annual);
 				
			$sql = "select * from Taxable_monthly where emp_id = '$emp_id'";
			$get_details = mysqli_query($conn,$sql);
			if(!$get_details) {
				echo mysqli_error($conn);
			}
			else {
				$result = $get_details->fetch_assoc();
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
				echo 'Validated successfully. ';
			}
			else {
				echo mysqli_error($conn);
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
			
		$cnt = 0;
		$new_tax = 0;
		while($adjusted_taxable > $upper_bound[$cnt]) {
			$new_tax += ($upper_bound[$cnt]-$lower_bound[$cnt])*$percent[$cnt];
			$cnt++;
		}
		$new_tax += ($adjusted_taxable-$lower_bound[$cnt])*$percent[$cnt];
		$new_tax = ceil($new_tax/100);
		
		if($adjusted_taxable <= 500000) {
			if($new_tax < 12500) {
				$new_tax = 0;
			}
			else {
				$new_tax -= 12500;
			}
		}
		
		$get_tax = "select * from Tax_monthly where emp_id = '$emp_id'";
		$res = mysqli_query($conn,$get_tax);
		
		if(!$res) {
			echo mysqli_error($conn);
		}
		else {
			$result = $res->fetch_assoc();
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
		
		$cnt = 0;
		$adjusted_tax = 0;
		while($cnt < 12) {
			$adjusted_tax = $adjusted_tax + $tax_months[$cnt];
			$cnt += 1;
		}
		
		$edu_cess = ceil($adjusted_tax*0.04);
 				
		$sql = "update Tax_monthly set April = '$tax_months[0]', May = '$tax_months[1]', June = '$tax_months[2]', July = '$tax_months[3]', August = '$tax_months[4]', September = '$tax_months[5]', October = '$tax_months[6]', November = '$tax_months[7]', December = '$tax_months[8]', January = '$tax_months[9]', February = '$tax_months[10]', March = '$tax_months[11]', Annual = '$new_tax', Adjusted = '$adjusted_tax', Edu_Cess= '$edu_cess' where emp_id = '$emp_id'";
		
		if(mysqli_query($conn,$sql)) {
			echo 'Tax calculated successfully.';
		}
		else {
			echo mysqli_error($conn);
		}
	
?>

<br>
<br>
<button onclick="getBack();">Back</button>

</div>

</body>

<script>

	function getBack() {
		window.location.href = "validate.php";
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
