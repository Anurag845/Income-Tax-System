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

</style>

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

	$emp_name = $_POST['emp_name'];
	//$date = $_POST['dec_date'];

	$get_empid = "select emp_id,gross_sal from Employee where emp_name='$emp_name'";
	$result = mysqli_query($conn,$get_empid);
	if(!$result) {
		echo mysqli_error($conn);
	}
	else {
		$temp = $result->fetch_assoc();
		$emp_id = $temp['emp_id'];
		$gross_sal = $temp['gross_sal'];
			
		$month = date('m');
		
		if($month < 3) {
			$no_of_months = 3 - $month;
		}
		else {
			$no_of_months = 12 - $month + 3;
		}
	}
	
	$entry_exists = "select * from Taxable_monthly where emp_id = '$emp_id'";
	$details = mysqli_query($conn,$entry_exists);
	
	if(!$details) {
		echo mysqli_error($conn);
	}
	else {
		$no_entry = mysqli_num_rows($details);
		if($no_entry == 0) {
			$flag = 'first_time';
		}
		else if($no_entry == 1){
			$flag = 'updation';
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
		
	}
	
	if($flag == 'first_time') {
	
		$annual_taxable = $gross_sal;
		
		$get_ptax = "select tax_limit from Limits where entry = 'Profession Tax'";
		$res = mysqli_query($conn,$get_ptax);
		if(!$res) {
			echo mysqli_error($conn);
		}
		else {
			$temp = $res->fetch_assoc();
			$prof_tax = $temp['tax_limit'];
		}
		
		$get_deduc = "select tax_limit from Limits where entry = 'Deduction'";
		$res = mysqli_query($conn,$get_deduc);
		if(!$res) {
			echo mysqli_error($conn);
		}
		else {
			$temp = $res->fetch_assoc();
			$deduction = $temp['tax_limit'];
		}
							
		$annual_taxable = $annual_taxable - ($prof_tax + $deduction);				

		if(isset($_POST['ann_rent'])) {
			$ann_rent = $_POST['ann_rent'];
			$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'Annual Rent',$ann_rent,0,'Pending','ann_rent') on duplicate key update amount_declared = '$ann_rent'";
			mysqli_query($conn,$sql);
			$get_limit = "select tax_limit from Limits where entry='Annual Rent'";
			$result = mysqli_query($conn,$get_limit);
			if(!$result) {
				echo mysqli_error($conn);
			}
			else {
				$temp = $result->fetch_assoc();
				$limit = $temp['tax_limit'];
				if($ann_rent < $limit) {
					$annual_taxable = $annual_taxable - $ann_rent;
				}
				else {
					$annual_taxable = $annual_taxable - $limit;
				}
			}
		}
	
		if(isset($_POST['medi'])) {
			$medi = $_POST['medi'];
			$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'Mediclaim',$medi,0,'Pending','medi') on duplicate key update amount_declared = '$medi'";
			mysqli_query($conn,$sql);
			$get_limit = "select tax_limit from Limits where entry='Mediclaim'";
			$result = mysqli_query($conn,$get_limit);
			if(!$result) {
				echo mysqli_error($conn);
			}
			else {
				$temp = $result->fetch_assoc();
				$limit = $temp['tax_limit'];
				if($medi < $limit) {
					$annual_taxable = $annual_taxable - $medi;
				}
				else {
					$annual_taxable = $annual_taxable - $limit;
				}
			}
		}
		if(isset($_POST['home_int'])) {
			$home_int = $_POST['home_int'];
			$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'Home Interest',$home_int,0,'Pending','home_int') on duplicate key update amount_declared = '$home_int'";
			mysqli_query($conn,$sql);
			$get_limit = "select tax_limit from Limits where entry='Home Interest'";
			$result = mysqli_query($conn,$get_limit);
			if(!$result) {
				echo mysqli_error($conn);
			}
			else {
				$temp = $result->fetch_assoc();
				$limit = $temp['tax_limit'];
				if($home_int < $limit) {
					$annual_taxable = $annual_taxable - $home_int;
				}
				else {
					$annual_taxable = $annual_taxable - $limit;
				}
			}
		}
		if(isset($_POST['nat_pen'])) {
			$nat_pen = $_POST['nat_pen'];
			$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'National Pension',$nat_pen,0,'Pending','nat_pen') on duplicate key update amount_declared = '$nat_pen'";
			mysqli_query($conn,$sql);
			$get_limit = "select tax_limit from Limits where entry='National Pension'";
			$result = mysqli_query($conn,$get_limit);
			if(!$result) {
				echo mysqli_error($conn);
			}
			else {
				$temp = $result->fetch_assoc();
				$limit = $temp['tax_limit'];
				if($nat_pen < $limit) {
					$annual_taxable = $annual_taxable - $nat_pen;
				}
				else {
					$annual_taxable = $annual_taxable - $limit;
				}
			}
		}
		if(isset($_POST['phy_hand'])) {
			$phy_hand = $_POST['phy_hand'];
			$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'Physically Handicap',$phy_hand,0,'Pending','phy_hand') on duplicate key update amount_declared = '$phy_hand'";
			mysqli_query($conn,$sql);
			$get_limit = "select tax_limit from Limits where entry='Physically Handicap'";
			$result = mysqli_query($conn,$get_limit);
			if(!$result) {
				echo mysqli_error($conn);
			}
			else {
				$temp = $result->fetch_assoc();
				$limit = $temp['tax_limit'];
				if($phy_hand < $limit) {
					$annual_taxable = $annual_taxable - $phy_hand;
				}
				else {
					$annual_taxable = $annual_taxable - $limit;
				}
			}
		}
		if(isset($_POST['edu_int'])) {
			$edu_int = $_POST['edu_int'];
			$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'Education Interest',$edu_int,0,'Pending','edu_int') on duplicate key update amount_declared = '$edu_int'";
			mysqli_query($conn,$sql);
			$get_limit = "select tax_limit from Limits where entry='Education Interest'";
			$result = mysqli_query($conn,$get_limit);
			if(!$result) {
				echo mysqli_error($conn);
			}
			else {
				$temp = $result->fetch_assoc();
				$limit = $temp['tax_limit'];
				if($edu_int < $limit) {
					$annual_taxable = $annual_taxable - $edu_int;
				}
				else {
					$annual_taxable = $annual_taxable - $limit;
				}
			}
		}
	
		if(isset($_POST['cpf'])) {
			$cpf = $_POST['cpf'];
			$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'CPF',$cpf,0,'Pending','cpf') on duplicate key update amount_declared = '$cpf'";
			mysqli_query($conn,$sql);
		}
		else {
			$cpf = 0;
		}
	
		if(isset($_POST['ppf'])) {
			$ppf = $_POST['ppf'];
			$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'PPF',$ppf,0,'Pending','ppf') on duplicate key update amount_declared = '$ppf'";
			mysqli_query($conn,$sql);
		}
		else {
			$ppf = 0;
		}
	
		if(isset($_POST['nsc'])) {
			$nsc = $_POST['nsc'];
			$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'NSC',$nsc,0,'Pending','nsc') on duplicate key update amount_declared = $nsc";
			mysqli_query($conn,$sql);
		}
		else {
			$nsc = 0;
		}
	
		if(isset($_POST['ulip'])) {
			$ulip = $_POST['ulip'];
			$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'ULIP',$ulip,0,'Pending','ulip') on duplicate key update amount_declared = '$ulip'";
			mysqli_query($conn,$sql);
		}
		else {
			$ulip = 0;
		}
	
		if(isset($_POST['ann_ins'])) {
			$ann_ins = $_POST['ann_ins'];
			$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'Annual Insurance',$ann_ins,0,'Pending','ann_ins') on duplicate key update amount_declared = '$ann_ins'";
			mysqli_query($conn,$sql);
		}
		else {
			$ann_ins = 0;
		}
	
		if(isset($_POST['hsg_loan_prin'])) {
			$hsg_loan_prin = $_POST['hsg_loan_prin'];
			$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'Housing Loan Principal',$hsg_loan_prin,0,'Pending','hsg_loan_prin') on duplicate key update amount_declared = '$hsg_loan_prin'";
			mysqli_query($conn,$sql);
		}
		else {
			$hsg_loan_prin = 0;
		}
	
		if(isset($_POST['tuition_fee'])) {
			$tuition_fee = $_POST['tuition_fee'];
			$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'Children Tuition Fee',$tuition_fee,0,'Pending','tuition_fee') on duplicate key update amount_declared = '$tuition_fee'";
			mysqli_query($conn,$sql);
		}
		else {
			$tuition_fee = 0;
		}
	
		if(isset($_POST['bank_deposit'])) {
			$bank_deposit = $_POST['bank_deposit'];
			$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'Bank Deposit',$bank_deposit,0,'Pending','bank_deposit') on duplicate key update amount_declared = '$bank_deposit'";
			mysqli_query($conn,$sql);
		}
		else {
			$bank_deposit = 0;
		}
	
		if(isset($_POST['reg_fee'])) {
			$reg_fee = $_POST['reg_fee'];
			$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'Registration Fee',$reg_fee,0,'Pending','reg_fee') on duplicate key update amount_declared = '$reg_fee'";
			mysqli_query($conn,$sql);
		}
		else {
			$reg_fee = 0;
		}
	
		$investments = $cpf + $ppf + $nsc + $ulip + $ann_ins + $hsg_loan_prin + $tuition_fee + $bank_deposit + $reg_fee;
		$get_limit = "select tax_limit from Limits where entry='Investments'";
		$result = mysqli_query($conn,$get_limit);
		if(!$result) {
			echo mysqli_error($conn);
		}
		else {
			$temp = $result->fetch_assoc();
			$limit = $temp['tax_limit'];
			if($investments < $limit) {
				$annual_taxable = $annual_taxable - $investments;
			}
			else {
				$annual_taxable = $annual_taxable - $limit;
			}
		}
	
		$taxablepermonth = $annual_taxable/12;
	
		$sql = "insert into Taxable_monthly values ($emp_id,$taxablepermonth,$taxablepermonth,$taxablepermonth,$taxablepermonth,$taxablepermonth,$taxablepermonth,$taxablepermonth,$taxablepermonth,$taxablepermonth,$taxablepermonth,$taxablepermonth,$taxablepermonth,$annual_taxable)";
		
		if(mysqli_query($conn,$sql)) {
			echo "Declaration Form submitted successfully.";
		}
		else {
			echo mysqli_error($conn);
		}
		
		$cnt = 0;
		$tax = 0;
		while($annual_taxable > $upper_bound[$cnt]) {
			$tax += ($upper_bound[$cnt]-$lower_bound[$cnt])*$percent[$cnt];
			$cnt++;
		}
		$tax += ($annual_taxable-$lower_bound[$cnt])*$percent[$cnt];
		$tax = $tax/100;
		//echo 'Tax is ' . $tax;
		
		$taxpermonth = $tax/12;
		
		$sql = "insert into Tax_monthly values ($emp_id,$taxpermonth,$taxpermonth,$taxpermonth,$taxpermonth,$taxpermonth,$taxpermonth,$taxpermonth,$taxpermonth,$taxpermonth,$taxpermonth,$taxpermonth,$taxpermonth,$tax)";
		
		if(mysqli_query($conn,$sql)) {
			echo ' Tax calculated successfully.';
		}
		else {
			echo mysqli_error($conn);
		}
	
	}
	
	else if($flag == 'updation') {
		$sql = "select Annual from Taxable_monthly where emp_id = '$emp_id'";
		$get_taxable = mysqli_query($conn,$sql);
		if(!$get_taxable) {
			echo mysqli_error($conn);
		}
		else {
			$temp = $get_taxable->fetch_assoc();
			$annual_taxable = $temp['Annual'];
		}
		
		
		if(!empty($_POST['ann_rent']) || $_POST['ann_rent'] === '0') {
	
			$new_ann_rent = $_POST['ann_rent'];
			//echo $prov_ann_rent;
			
			$get_declared = "select amount_declared from Declarations where emp_id = '$emp_id' and dec_id = 'ann_rent'";
			$dec = mysqli_query($conn,$get_declared);
			if(!$dec) {
				echo mysqli_error($conn);
			}
			else {
				if(mysqli_num_rows($dec) == 0 && $new_ann_rent != 0) {
					$dec_ann_rent = 0;
					$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'Annual Rent',$new_ann_rent,0,'Pending','ann_rent')";
					mysqli_query($conn,$sql);
				}
				else if(mysqli_num_rows($dec) == 1 && $new_ann_rent === '0') {
					$temp = $dec->fetch_assoc();
					$dec_ann_rent = $temp['amount_declared'];
					$sql = "delete from Declarations where emp_id = '$emp_id' and dec_id = 'ann_rent'";
					mysqli_query($conn,$sql);
				}
				else {
					$temp = $dec->fetch_assoc();
					$dec_ann_rent = $temp['amount_declared'];
					$sql = "update Declarations set amount_declared = '$new_ann_rent' where emp_id = '$emp_id' and dec_id = 'ann_rent'";
					mysqli_query($conn,$sql);
				}
				
			}
			if($dec_ann_rent != $new_ann_rent) {
				$get_limit = "select tax_limit from Limits where entry='Annual Rent'";
				$result = mysqli_query($conn,$get_limit);
				if(!$result) {
					echo mysqli_error($conn);
				}
				else {
					$temp = $result->fetch_assoc();
					$limit = $temp['tax_limit'];
				}
				if($dec_ann_rent < $limit) {
					$annual_taxable = $annual_taxable + $dec_ann_rent;
				}
				else {
					$annual_taxable = $annual_taxable + $limit;
				}
				if($new_ann_rent < $limit) {
					$annual_taxable = $annual_taxable - $new_ann_rent;
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
				$adjust_monthly = $adjust/$no_of_months;
 				
				while($cnt < 12) {
					$tax_months[$cnt] = $adjust_monthly;
					$cnt += 1;
				}
 				
				$sql = "update Taxable_monthly set April = '$tax_months[0]', May = '$tax_months[1]', June = '$tax_months[2]', July = '$tax_months[3]', August = '$tax_months[4]', September = '$tax_months[5]', October = '$tax_months[6]', November = '$tax_months[7]', December = '$tax_months[8]', January = '$tax_months[9]', February = '$tax_months[10]', March = '$tax_months[11]' where emp_id = '$emp_id'";
 				
				mysqli_query($conn,$sql);
			}
		}
		else {
			//echo 'Not set';
		}
		
		if(!empty($_POST['medi']) || $_POST['medi'] === '0') {
	
			$new_medi = $_POST['medi'];

			$get_declared = "select amount_declared from Declarations where emp_id = '$emp_id' and dec_id = 'medi'";
			$dec = mysqli_query($conn,$get_declared);
			if(!$dec) {
				echo mysqli_error($conn);
			}
			else {
				if(mysqli_num_rows($dec) == 0 && $new_medi != 0) {
					$dec_medi = 0;
					$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'Mediclaim',$new_medi,0,'Pending','medi')";
					mysqli_query($conn,$sql);
				}
				else if(mysqli_num_rows($dec) == 1 && $new_medi === '0') {
					$temp = $dec->fetch_assoc();
					$dec_medi = $temp['amount_declared'];
					$sql = "delete from Declarations where emp_id = '$emp_id' and dec_id = 'medi'";
					mysqli_query($conn,$sql);
				}
				else {
					$temp = $dec->fetch_assoc();
					$dec_medi = $temp['amount_declared'];
					$sql = "update Declarations set amount_declared = '$new_medi' where emp_id = '$emp_id' and dec_id = 'medi'";
					mysqli_query($conn,$sql);
				}
				
			}
			if($dec_medi != $new_medi) {
				$get_limit = "select tax_limit from Limits where entry='Mediclaim'";
				$result = mysqli_query($conn,$get_limit);
				if(!$result) {
					echo mysqli_error($conn);
				}
				else {
					$temp = $result->fetch_assoc();
					$limit = $temp['tax_limit'];
				}
				if($dec_medi < $limit) {
					$annual_taxable = $annual_taxable + $dec_medi;
				}
				else {
					$annual_taxable = $annual_taxable + $limit;
				}
				if($new_medi < $limit) {
					$annual_taxable = $annual_taxable - $new_medi;
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
				$adjust_monthly = $adjust/$no_of_months;
 				
				while($cnt < 12) {
					$tax_months[$cnt] = $adjust_monthly;
					$cnt += 1;
				}
 				
				$sql = "update Taxable_monthly set April = '$tax_months[0]', May = '$tax_months[1]', June = '$tax_months[2]', July = '$tax_months[3]', August = '$tax_months[4]', September = '$tax_months[5]', October = '$tax_months[6]', November = '$tax_months[7]', December = '$tax_months[8]', January = '$tax_months[9]', February = '$tax_months[10]', March = '$tax_months[11]' where emp_id = '$emp_id'";
 				
				mysqli_query($conn,$sql);
			}
		}
		else {
			//echo 'Not set';
		}
		
		if(!empty($_POST['home_int']) || $_POST['home_int'] === '0') {
	
			$new_home_int = $_POST['home_int'];

			$get_declared = "select amount_declared from Declarations where emp_id = '$emp_id' and dec_id = 'home_int'";
			$dec = mysqli_query($conn,$get_declared);
			if(!$dec) {
				echo mysqli_error($conn);
			}
			else {
				if(mysqli_num_rows($dec) == 0 && $new_home_int != 0) {
					$dec_home_int = 0;
					$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'Home Interest',$new_home_int,0,'Pending','home_int')";
					mysqli_query($conn,$sql);
				}
				else if(mysqli_num_rows($dec) == 1 && $new_home_int === '0') {
					$temp = $dec->fetch_assoc();
					$dec_home_int = $temp['amount_declared'];
					$sql = "delete from Declarations where emp_id = '$emp_id' and dec_id = 'home_int'";
					mysqli_query($conn,$sql);
				}
				else {
					$temp = $dec->fetch_assoc();
					$dec_home_int = $temp['amount_declared'];
					$sql = "update Declarations set amount_declared = '$new_home_int' where emp_id = '$emp_id' and dec_id = 'home_int'";
					mysqli_query($conn,$sql);
				}
				
			}
			if($dec_home_int != $new_home_int) {
				$get_limit = "select tax_limit from Limits where entry='Home Interest'";
				$result = mysqli_query($conn,$get_limit);
				if(!$result) {
					echo mysqli_error($conn);
				}
				else {
					$temp = $result->fetch_assoc();
					$limit = $temp['tax_limit'];
				}
				if($dec_home_int < $limit) {
					$annual_taxable = $annual_taxable + $dec_home_int;
				}
				else {
					$annual_taxable = $annual_taxable + $limit;
				}
				if($new_home_int < $limit) {
					$annual_taxable = $annual_taxable - $new_home_int;
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
				$adjust_monthly = $adjust/$no_of_months;
 				
				while($cnt < 12) {
					$tax_months[$cnt] = $adjust_monthly;
					$cnt += 1;
				}
 				
				$sql = "update Taxable_monthly set April = '$tax_months[0]', May = '$tax_months[1]', June = '$tax_months[2]', July = '$tax_months[3]', August = '$tax_months[4]', September = '$tax_months[5]', October = '$tax_months[6]', November = '$tax_months[7]', December = '$tax_months[8]', January = '$tax_months[9]', February = '$tax_months[10]', March = '$tax_months[11]' where emp_id = '$emp_id'";
 				
				mysqli_query($conn,$sql);
			}
		}
		else {
			//echo 'Not set';
		}
		
		if(!empty($_POST['nat_pen']) || $_POST['nat_pen'] === '0') {
	
			$new_nat_pen = $_POST['nat_pen'];

			$get_declared = "select amount_declared from Declarations where emp_id = '$emp_id' and dec_id = 'nat_pen'";
			$dec = mysqli_query($conn,$get_declared);
			if(!$dec) {
				echo mysqli_error($conn);
			}
			else {
				if(mysqli_num_rows($dec) == 0 && $new_nat_pen != 0) {
					$dec_nat_pen = 0;
					$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'National Pension',$new_nat_pen,0,'Pending','nat_pen')";
					mysqli_query($conn,$sql);
				}
				else if(mysqli_num_rows($dec) == 1 && $new_nat_pen === '0') {
					$temp = $dec->fetch_assoc();
					$dec_nat_pen = $temp['amount_declared'];
					$sql = "delete from Declarations where emp_id = '$emp_id' and dec_id = 'nat_pen'";
					mysqli_query($conn,$sql);
				}
				else {
					$temp = $dec->fetch_assoc();
					$dec_nat_pen = $temp['amount_declared'];
					$sql = "update Declarations set amount_declared = '$new_nat_pen' where emp_id = '$emp_id' and dec_id = 'nat_pen'";
					mysqli_query($conn,$sql);
				}
				
			}
			if($dec_nat_pen != $new_nat_pen) {
				$get_limit = "select tax_limit from Limits where entry='National Pension'";
				$result = mysqli_query($conn,$get_limit);
				if(!$result) {
					echo mysqli_error($conn);
				}
				else {
					$temp = $result->fetch_assoc();
					$limit = $temp['tax_limit'];
				}
				if($dec_nat_pen < $limit) {
					$annual_taxable = $annual_taxable + $dec_nat_pen;
				}
				else {
					$annual_taxable = $annual_taxable + $limit;
				}
				if($new_nat_pen < $limit) {
					$annual_taxable = $annual_taxable - $new_nat_pen;
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
				$adjust_monthly = $adjust/$no_of_months;
 				
				while($cnt < 12) {
					$tax_months[$cnt] = $adjust_monthly;
					$cnt += 1;
				}
 				
				$sql = "update Taxable_monthly set April = '$tax_months[0]', May = '$tax_months[1]', June = '$tax_months[2]', July = '$tax_months[3]', August = '$tax_months[4]', September = '$tax_months[5]', October = '$tax_months[6]', November = '$tax_months[7]', December = '$tax_months[8]', January = '$tax_months[9]', February = '$tax_months[10]', March = '$tax_months[11]' where emp_id = '$emp_id'";
 				
				mysqli_query($conn,$sql);
			}
		}
		else {
			//echo 'Not set';
		}
		
		if(!empty($_POST['phy_hand']) || $_POST['phy_hand'] === '0') {
	
			$new_phy_hand = $_POST['phy_hand'];

			$get_declared = "select amount_declared from Declarations where emp_id = '$emp_id' and dec_id = 'phy_hand'";
			$dec = mysqli_query($conn,$get_declared);
			if(!$dec) {
				echo mysqli_error($conn);
			}
			else {
				if(mysqli_num_rows($dec) == 0 && $new_phy_hand != 0) {
					$dec_phy_hand = 0;
					$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'Physically Handicap',$new_phy_hand,0,'Pending','phy_hand')";
					mysqli_query($conn,$sql);
				}
				else if(mysqli_num_rows($dec) == 1 && $new_phy_hand === '0') {
					$temp = $dec->fetch_assoc();
					$dec_phy_hand = $temp['amount_declared'];
					$sql = "delete from Declarations where emp_id = '$emp_id' and dec_id = 'phy_hand'";
					mysqli_query($conn,$sql);
				}
				else {
					$temp = $dec->fetch_assoc();
					$dec_phy_hand = $temp['amount_declared'];
					$sql = "update Declarations set amount_declared = '$new_phy_hand' where emp_id = '$emp_id' and dec_id = 'phy_hand'";
					mysqli_query($conn,$sql);
				}
				
			}
			if($dec_phy_hand != $new_phy_hand) {
				$get_limit = "select tax_limit from Limits where entry='Physically Handicap'";
				$result = mysqli_query($conn,$get_limit);
				if(!$result) {
					echo mysqli_error($conn);
				}
				else {
					$temp = $result->fetch_assoc();
					$limit = $temp['tax_limit'];
				}
				if($dec_phy_hand < $limit) {
					$annual_taxable = $annual_taxable + $dec_phy_hand;
				}
				else {
					$annual_taxable = $annual_taxable + $limit;
				}
				if($new_phy_hand < $limit) {
					$annual_taxable = $annual_taxable - $new_phy_hand;
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
				$adjust_monthly = $adjust/$no_of_months;
 				
				while($cnt < 12) {
					$tax_months[$cnt] = $adjust_monthly;
					$cnt += 1;
				}
 				
				$sql = "update Taxable_monthly set April = '$tax_months[0]', May = '$tax_months[1]', June = '$tax_months[2]', July = '$tax_months[3]', August = '$tax_months[4]', September = '$tax_months[5]', October = '$tax_months[6]', November = '$tax_months[7]', December = '$tax_months[8]', January = '$tax_months[9]', February = '$tax_months[10]', March = '$tax_months[11]' where emp_id = '$emp_id'";
 				
				mysqli_query($conn,$sql);
			}
		}
		else {
			//echo 'Not set';
		}
		
		if(!empty($_POST['edu_int']) || $_POST['edu_int'] === '0') {
	
			$new_edu_int = $_POST['edu_int'];

			$get_declared = "select amount_declared from Declarations where emp_id = '$emp_id' and dec_id = 'edu_int'";
			$dec = mysqli_query($conn,$get_declared);
			if(!$dec) {
				echo mysqli_error($conn);
			}
			else {
				if(mysqli_num_rows($dec) == 0 && $new_edu_int != 0) {
					$dec_edu_int = 0;
					$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'Education Interest',$new_edu_int,0,'Pending','edu_int')";
					mysqli_query($conn,$sql);
				}
				else if(mysqli_num_rows($dec) == 1 && $new_edu_int === '0') {
					$temp = $dec->fetch_assoc();
					$dec_edu_int = $temp['amount_declared'];
					$sql = "delete from Declarations where emp_id = '$emp_id' and dec_id = 'edu_int'";
					mysqli_query($conn,$sql);
				}
				else {
					$temp = $dec->fetch_assoc();
					$dec_edu_int = $temp['amount_declared'];
					$sql = "update Declarations set amount_declared = '$new_edu_int' where emp_id = '$emp_id' and dec_id = 'edu_int'";
					mysqli_query($conn,$sql);
				}
				
			}
			if($dec_edu_int != $new_edu_int) {
				$get_limit = "select tax_limit from Limits where entry='Education Interest'";
				$result = mysqli_query($conn,$get_limit);
				if(!$result) {
					echo mysqli_error($conn);
				}
				else {
					$temp = $result->fetch_assoc();
					$limit = $temp['tax_limit'];
				}
				if($dec_edu_int < $limit) {
					$annual_taxable = $annual_taxable + $dec_edu_int;
				}
				else {
					$annual_taxable = $annual_taxable + $limit;
				}
				if($new_edu_int < $limit) {
					$annual_taxable = $annual_taxable - $new_edu_int;
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
				$adjust_monthly = $adjust/$no_of_months;
 				
				while($cnt < 12) {
					$tax_months[$cnt] = $adjust_monthly;
					$cnt += 1;
				}
 				
				$sql = "update Taxable_monthly set April = '$tax_months[0]', May = '$tax_months[1]', June = '$tax_months[2]', July = '$tax_months[3]', August = '$tax_months[4]', September = '$tax_months[5]', October = '$tax_months[6]', November = '$tax_months[7]', December = '$tax_months[8]', January = '$tax_months[9]', February = '$tax_months[10]', March = '$tax_months[11]' where emp_id = '$emp_id'";
 				
				mysqli_query($conn,$sql);
			}
		}
		else {
			//echo 'Not set';
		}
		
		
		if(!empty($_POST['cpf'])  || $_POST['cpf'] === '0') {
			$new_cpf = $_POST['cpf'];
			$get_cpf = "select amount_declared from Declarations where dec_id = 'cpf' and emp_id = '$emp_id'";
			$res = mysqli_query($conn,$get_cpf);
			if(!$res) {
				echo mysqli_error($conn);
			}
			else {
				if(mysqli_num_rows($res) == 0 && $new_cpf != 0) {
					$cpf = 0;
					$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'CPF',$new_cpf,0,'Pending','cpf')";
					mysqli_query($conn,$sql);
				}
				else if(mysqli_num_rows($res) == 1 && $new_cpf === '0') {
					$temp = $res->fetch_assoc();
					$cpf = $temp['amount_declared'];
					$sql = "delete from Declarations where emp_id = '$emp_id' and dec_id = 'cpf'";
					mysqli_query($conn,$sql);
				}
				else {
					$temp = $res->fetch_assoc();
					$cpf = $temp['amount_declared'];
					$sql = "update Declarations set amount_declared = '$new_cpf' where emp_id = '$emp_id' and dec_id = 'cpf'";
					mysqli_query($conn,$sql);
				}
			}
		}
		else {
			$new_cpf = null;
			$get_status = "select * from Declarations where dec_id = 'cpf' and emp_id = '$emp_id'";
			$res = mysqli_query($conn,$get_status);
			if(!$res) {
				mysqli_error($conn);
			}
			else {
				$row_count = mysqli_num_rows($res);
				if($row_count == 1) {
					$temp = $res->fetch_assoc();
					$status = $temp['status'];
					if($status == 'Pending') {
						$cpf = $temp['amount_declared'];
					}
					else {
						$cpf = $temp['amount_proved'];
					}
				}
				else {
					$cpf = 0;
				}
			}
		}
		
		if(!empty($_POST['ppf']) || $_POST['ppf'] === '0') {
			$new_ppf = $_POST['ppf'];
			$get_ppf = "select amount_declared from Declarations where dec_id = 'ppf' and emp_id = '$emp_id'";
			$res = mysqli_query($conn,$get_ppf);
			if(!$res) {
				echo mysqli_error($conn);
			}
			else {
				if(mysqli_num_rows($res) == 0 && $new_ppf != 0) {
					$ppf = 0;
					$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'PPF',$new_ppf,0,'Pending','ppf')";
					mysqli_query($conn,$sql);
				}
				else if(mysqli_num_rows($res) == 1 && $new_ppf === '0') {
					$temp = $res->fetch_assoc();
					$ppf = $temp['amount_declared'];
					$sql = "delete from Declarations where emp_id = '$emp_id' and dec_id = 'ppf'";
					mysqli_query($conn,$sql);
				}
				else {
					$temp = $res->fetch_assoc();
					$ppf = $temp['amount_declared'];
					$sql = "update Declarations set amount_declared = '$new_ppf' where emp_id = '$emp_id' and dec_id = 'ppf'";
					mysqli_query($conn,$sql);
				}
			}
		}
		else {
			$new_ppf = null;
			$get_status = "select * from Declarations where dec_id = 'ppf' and emp_id = '$emp_id'";
			$res = mysqli_query($conn,$get_status);
			if(!$res) {
				mysqli_error($conn);
			}
			else {
				$row_count = mysqli_num_rows($res);
				if($row_count == 1) {
					$temp = $res->fetch_assoc();
					$status = $temp['status'];
					if($status == 'Pending') {
						$ppf = $temp['amount_declared'];
					}
					else {
						$ppf = $temp['amount_proved'];
					}
				}
				else {
					$ppf = 0;
				}
			}
		}
		
		if(!empty($_POST['nsc']) || $_POST['nsc'] === '0') {
			$new_nsc = $_POST['nsc'];
			$get_nsc = "select amount_declared from Declarations where dec_id = 'nsc' and emp_id = '$emp_id'";
			$res = mysqli_query($conn,$get_nsc);
			if(!$res) {
				echo mysqli_error($conn);
			}
			else {
				if(mysqli_num_rows($res) == 0 && $new_nsc != 0) {
					$nsc = 0;
					$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'NSC',$new_nsc,0,'Pending','nsc')";
					mysqli_query($conn,$sql);
				}
				else if(mysqli_num_rows($res) == 1 && $new_nsc === '0') {
					$temp = $res->fetch_assoc();
					$nsc = $temp['amount_declared'];
					$sql = "delete from Declarations where emp_id = '$emp_id' and dec_id = 'nsc'";
					mysqli_query($conn,$sql);
				}
				else {
					$temp = $res->fetch_assoc();
					$nsc = $temp['amount_declared'];
					$sql = "update Declarations set amount_declared = '$new_nsc' where emp_id = '$emp_id' and dec_id = 'nsc'";
					mysqli_query($conn,$sql);
				}
			}
		}
		else {
			$new_nsc = null;
			$get_status = "select * from Declarations where dec_id = 'nsc' and emp_id = '$emp_id'";
			$res = mysqli_query($conn,$get_status);
			if(!$res) {
				mysqli_error($conn);
			}
			else {
				$row_count = mysqli_num_rows($res);
				if($row_count == 1) {
					$temp = $res->fetch_assoc();
					$status = $temp['status'];
					if($status == 'Pending') {
						$nsc = $temp['amount_declared'];
					}
					else {
						$nsc = $temp['amount_proved'];
					}
				}
				else {
					$nsc = 0;
				}
			}
		}
		
		
		if(!empty($_POST['ulip']) || $_POST['ulip'] === '0') {
			$new_ulip = $_POST['ulip'];
			$get_ulip = "select amount_declared from Declarations where dec_id = 'ulip' and emp_id = '$emp_id'";
			$res = mysqli_query($conn,$get_ulip);
			if(!$res) {
				echo mysqli_error($conn);
			}
			else {
				if(mysqli_num_rows($res) == 0 && $new_ulip != 0) {
					$ulip = 0;
					$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'NSC',$new_ulip,0,'Pending','ulip')";
					mysqli_query($conn,$sql);
				}
				else if(mysqli_num_rows($res) == 1 && $new_ulip === '0') {
					$temp = $res->fetch_assoc();
					$ulip = $temp['amount_declared'];
					$sql = "delete from Declarations where emp_id = '$emp_id' and dec_id = 'ulip'";
					mysqli_query($conn,$sql);
				}
				else {
					$temp = $res->fetch_assoc();
					$ulip = $temp['amount_declared'];
					$sql = "update Declarations set amount_declared = '$new_ulip' where emp_id = '$emp_id' and dec_id = 'ulip'";
					mysqli_query($conn,$sql);
				}
			}
		}
		else {
			$new_ulip = null;
			$get_status = "select * from Declarations where dec_id = 'ulip' and emp_id = '$emp_id'";
			$res = mysqli_query($conn,$get_status);
			if(!$res) {
				mysqli_error($conn);
			}
			else {
				$row_count = mysqli_num_rows($res);
				if($row_count == 1) {
					$temp = $res->fetch_assoc();
					$status = $temp['status'];
					if($status == 'Pending') {
						$ulip = $temp['amount_declared'];
					}
					else {
						$ulip = $temp['amount_proved'];
					}
				}
				else {
					$ulip = 0;
				}
			}
		}
		
		if(!empty($_POST['ann_ins']) || $_POST['ann_ins'] === '0') {
			$new_ann_ins = $_POST['ann_ins'];
			$get_ann_ins = "select amount_declared from Declarations where dec_id = 'ann_ins' and emp_id = '$emp_id'";
			$res = mysqli_query($conn,$get_ann_ins);
			if(!$res) {
				echo mysqli_error($conn);
			}
			else {
				if(mysqli_num_rows($res) == 0 && $new_ann_ins != 0) {
					$ann_ins = 0;
					$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'Annual Insurance',$new_ann_ins,0,'Pending','ann_ins')";
					mysqli_query($conn,$sql);
				}
				else if(mysqli_num_rows($res) == 1 && $new_ann_ins === '0') {
					$temp = $res->fetch_assoc();
					$ann_ins = $temp['amount_declared'];
					$sql = "delete from Declarations where emp_id = '$emp_id' and dec_id = 'ann_ins'";
					mysqli_query($conn,$sql);
				}
				else {
					$temp = $res->fetch_assoc();
					$ann_ins = $temp['amount_declared'];
					$sql = "update Declarations set amount_declared = '$new_ann_ins' where emp_id = '$emp_id' and dec_id = 'ann_ins'";
					mysqli_query($conn,$sql);
				}
			}
		}
		else {
			$new_ann_ins = null;
			$get_status = "select * from Declarations where dec_id = 'ann_ins' and emp_id = '$emp_id'";
			$res = mysqli_query($conn,$get_status);
			if(!$res) {
				mysqli_error($conn);
			}
			else {
				$row_count = mysqli_num_rows($res);
				if($row_count == 1) {
					$temp = $res->fetch_assoc();
					$status = $temp['status'];
					if($status == 'Pending') {
						$ann_ins = $temp['amount_declared'];
					}
					else {
						$ann_ins = $temp['amount_proved'];
					}
				}
				else {
					$ann_ins = 0;
				}
			}
		}
		
		if(!empty($_POST['hsg_loan_prin']) || $_POST['hsg_loan_prin'] === '0') {
			$new_hsg_loan_prin = $_POST['hsg_loan_prin'];
			$get_hsg_loan_prin = "select amount_declared from Declarations where dec_id = 'hsg_loan_prin' and emp_id = '$emp_id'";
			$res = mysqli_query($conn,$get_hsg_loan_prin);
			if(!$res) {
				echo mysqli_error($conn);
			}
			else {
				if(mysqli_num_rows($res) == 0 && $new_hsg_loan_prin != 0) {
					$hsg_loan_prin = 0;
					$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'Housing Loan Principal',$new_hsg_loan_prin,0,'Pending','hsg_loan_prin')";
					mysqli_query($conn,$sql);
				}
				else if(mysqli_num_rows($res) == 1 && $new_hsg_loan_prin === '0') {
					$temp = $res->fetch_assoc();
					$hsg_loan_prin = $temp['amount_declared'];
					$sql = "delete from Declarations where emp_id = '$emp_id' and dec_id = 'hsg_loan_prin'";
					mysqli_query($conn,$sql);
				}
				else {
					$temp = $res->fetch_assoc();
					$hsg_loan_prin = $temp['amount_declared'];
					$sql = "update Declarations set amount_declared = '$new_hsg_loan_prin' where emp_id = '$emp_id' and dec_id = 'hsg_loan_prin'";
					mysqli_query($conn,$sql);
				}
			}
		}
		else {
			$new_hsg_loan_prin = null;
			$get_status = "select * from Declarations where dec_id = 'hsg_loan_prin' and emp_id = '$emp_id'";
			$res = mysqli_query($conn,$get_status);
			if(!$res) {
				mysqli_error($conn);
			}
			else {
				$row_count = mysqli_num_rows($res);
				if($row_count == 1) {
					$temp = $res->fetch_assoc();
					$status = $temp['status'];
					if($status == 'Pending') {
						$hsg_loan_prin = $temp['amount_declared'];
					}
					else {
						$hsg_loan_prin = $temp['amount_proved'];
					}
				}
				else {
					$hsg_loan_prin = 0;
				}
			}
		}
		
		if(!empty($_POST['tuition_fee']) || $_POST['tuition_fee'] === '0') {
			$new_tuition_fee = $_POST['tuition_fee'];
			$get_tuition_fee = "select amount_declared from Declarations where dec_id = 'tuition_fee' and emp_id = '$emp_id'";
			$res = mysqli_query($conn,$get_tuition_fee);
			if(!$res) {
				echo mysqli_error($conn);
			}
			else {
				if(mysqli_num_rows($res) == 0 && $new_tuition_fee != 0) {
					$tuition_fee = 0;
					$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'Children Tuition Fee',$new_tuition_fee,0,'Pending','tuition_fee')";
					mysqli_query($conn,$sql);
				}
				else if(mysqli_num_rows($res) == 1 && $new_tuition_fee === '0') {
					$temp = $res->fetch_assoc();
					$tuition_fee = $temp['amount_declared'];
					$sql = "delete from Declarations where emp_id = '$emp_id' and dec_id = 'tuition_fee'";
					mysqli_query($conn,$sql);
				}
				else {
					$temp = $res->fetch_assoc();
					$tuition_fee = $temp['amount_declared'];
					$sql = "update Declarations set amount_declared = '$new_tuition_fee' where emp_id = '$emp_id' and dec_id = 'tuition_fee'";
					mysqli_query($conn,$sql);
				}
			}
		}
		else {
			$new_tuition_fee = null;
			$get_status = "select * from Declarations where dec_id = 'tuition_fee' and emp_id = '$emp_id'";
			$res = mysqli_query($conn,$get_status);
			if(!$res) {
				mysqli_error($conn);
			}
			else {
				$row_count = mysqli_num_rows($res);
				if($row_count == 1) {
					$temp = $res->fetch_assoc();
					$status = $temp['status'];
					if($status == 'Pending') {
						$tuition_fee = $temp['amount_declared'];
					}
					else {
						$tuition_fee = $temp['amount_proved'];
					}
				}
				else {
					$tuition_fee = 0;
				}
			}
		}
		
		if(!empty($_POST['bank_deposit']) || $_POST['bank_deposit'] === '0') {
			$new_bank_deposit = $_POST['bank_deposit'];
			$get_bank_deposit = "select amount_declared from Declarations where dec_id = 'bank_deposit' and emp_id = '$emp_id'";
			$res = mysqli_query($conn,$get_bank_deposit);
			if(!$res) {
				echo mysqli_error($conn);
			}
			else {
				if(mysqli_num_rows($res) == 0 && $new_bank_deposit != 0) {
					$bank_deposit = 0;
					$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'Bank Deposit',$new_bank_deposit,0,'Pending','bank_deposit')";
					mysqli_query($conn,$sql);
				}
				else if(mysqli_num_rows($res) == 1 && $new_bank_deposit === '0') {
					$temp = $res->fetch_assoc();
					$bank_deposit = $temp['amount_declared'];
					$sql = "delete from Declarations where emp_id = '$emp_id' and dec_id = 'bank_deposit'";
					mysqli_query($conn,$sql);
				}
				else {
					$temp = $res->fetch_assoc();
					$bank_deposit = $temp['amount_declared'];
					$sql = "update Declarations set amount_declared = '$new_bank_deposit' where emp_id = '$emp_id' and dec_id = 'bank_deposit'";
					mysqli_query($conn,$sql);
				}
			}
		}
		else {
			$new_bank_deposit = null;
			$get_status = "select * from Declarations where dec_id = 'bank_deposit' and emp_id = '$emp_id'";
			$res = mysqli_query($conn,$get_status);
			if(!$res) {
				mysqli_error($conn);
			}
			else {
				$row_count = mysqli_num_rows($res);
				if($row_count == 1) {
					$temp = $res->fetch_assoc();
					$status = $temp['status'];
					if($status == 'Pending') {
						$bank_deposit = $temp['amount_declared'];
					}
					else {
						$bank_deposit = $temp['amount_proved'];
					}
				}
				else {
					$bank_deposit = 0;
				}
			}
		}
		
		if(!empty($_POST['reg_fee']) || $_POST['reg_fee'] === '0') {
			$new_reg_fee = $_POST['reg_fee'];
			$get_reg_fee = "select amount_declared from Declarations where dec_id = 'reg_fee' and emp_id = '$emp_id'";
			$res = mysqli_query($conn,$get_reg_fee);
			if(!$res) {
				echo mysqli_error($conn);
			}
			else {
				if(mysqli_num_rows($res) == 0 && $new_reg_fee != 0) {
					$reg_fee = 0;
					$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ($emp_id,'Registration Fee',$new_reg_fee,0,'Pending','reg_fee')";
					mysqli_query($conn,$sql);
				}
				else if(mysqli_num_rows($res) == 1 && $new_reg_fee === '0') {
					$temp = $res->fetch_assoc();
					$reg_fee = $temp['amount_declared'];
					$sql = "delete from Declarations where emp_id = '$emp_id' and dec_id = 'reg_fee'";
					mysqli_query($conn,$sql);
				}
				else {
					$temp = $res->fetch_assoc();
					$reg_fee = $temp['amount_declared'];
					$sql = "update Declarations set amount_declared = '$new_reg_fee' where emp_id = '$emp_id' and dec_id = 'reg_fee'";
					mysqli_query($conn,$sql);
				}
			}
		}
		else {
			$new_reg_fee = null;
			$get_status = "select * from Declarations where dec_id = 'reg_fee' and emp_id = '$emp_id'";
			$res = mysqli_query($conn,$get_status);
			if(!$res) {
				mysqli_error($conn);
			}
			else {
				$row_count = mysqli_num_rows($res);
				if($row_count == 1) {
					$temp = $res->fetch_assoc();
					$status = $temp['status'];
					if($status == 'Pending') {
						$reg_fee = $temp['amount_declared'];
					}
					else {
						$reg_fee = $temp['amount_proved'];
					}
				}
				else {
					$reg_fee = 0;
				}
			}
		}
		
		$old_inv = $cpf + $ppf + $nsc + $ulip + $ann_ins + $hsg_loan_prin + $tuition_fee + $bank_deposit + $reg_fee;
	
		$new_inv = 0;
	
		if(!empty($new_cpf) || $new_cpf === '0') {
			$new_inv += $new_cpf;
		}
		else {
			$new_inv += $cpf;
		}
	
		if(!empty($new_ppf) || $new_ppf === '0') {
			$new_inv += $new_ppf;
		}
		else {
			$new_inv += $ppf;
		}
	
		if(!empty($new_nsc) || $new_nsc === '0') {
			$new_inv += $new_nsc;
		}
		else {
			$new_inv += $nsc;
		}
	
		if(!empty($new_ulip) || $new_ulip === '0') {
			$new_inv += $new_ulip;
		}
		else {
			$new_inv += $ulip;
		}
	
		if(!empty($new_ann_ins) || $new_ann_ins === '0') {
			$new_inv += $new_ann_ins;
		}
		else {
			$new_inv += $ann_ins;
		}
	
		if(!empty($new_hsg_loan_prin) || $new_hsg_loan_prin === '0') {
			$new_inv += $new_hsg_loan_prin;
		}
		else {
			$new_inv += $hsg_loan_prin;
		}
	
		if(!empty($new_tuition_fee) || $new_tuition_fee === '0') {
			$new_inv += $new_tuition_fee;
		}
		else {
			$new_inv += $tuition_fee;
		}
	
		if(!empty($new_bank_deposit) || $new_bank_deposit === '0') {
			$new_inv += $new_bank_deposit;
		}
		else {
			$new_inv += $bank_deposit;
		}
		
		if(!empty($new_reg_fee) || $new_reg_fee === '0') {
			$new_inv += $new_reg_fee;
		}
		else {
			$new_inv += $reg_fee;
		}
	
	//echo 'New investment' . $new_inv;
	//echo 'Old Investment' . $old_inv;
	
		$get_limit = "select tax_limit from Limits where entry='Investments'";
		$res = mysqli_query($conn,$get_limit);
		$temp = $res->fetch_assoc();
		$limit = $temp['tax_limit'];
		
	//echo 'Annual taxable' . $annual_taxable;
	
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
		$adjust_monthly = $adjust/$no_of_months;
 				
		while($cnt < 12) {
			$tax_months[$cnt] = $adjust_monthly;
			$cnt += 1;
		}
 				
		$sql = "update Taxable_monthly set April = '$tax_months[0]', May = '$tax_months[1]', June = '$tax_months[2]', July = '$tax_months[3]', August = '$tax_months[4]', September = '$tax_months[5]', October = '$tax_months[6]', November = '$tax_months[7]', December = '$tax_months[8]', January = '$tax_months[9]', February = '$tax_months[10]', March = '$tax_months[11]' where emp_id = '$emp_id'";
		
		if(mysqli_query($conn,$sql)) {
			echo "Declaration Form submitted successfully.";
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
		$adjust_monthly = $adjust/$no_of_months;
 				
		while($cnt < 12) {
			$tax_months[$cnt] = $adjust_monthly;
			$cnt += 1;
		}
 				
		$sql = "update Tax_monthly set April = '$tax_months[0]', May = '$tax_months[1]', June = '$tax_months[2]', July = '$tax_months[3]', August = '$tax_months[4]', September = '$tax_months[5]', October = '$tax_months[6]', November = '$tax_months[7]', December = '$tax_months[8]', January = '$tax_months[9]', February = '$tax_months[10]', March = '$tax_months[11]', Annual = '$new_tax' where emp_id = '$emp_id'";
		
		if(mysqli_query($conn,$sql)) {
			echo ' Tax calculated successfully.';
		}
		else {
			echo mysqli_error($conn);
		}
		
	}
	
?>

	<br>
	<br>
	<button onclick="getBack();">Back</button>

</div>

</body>

<script>

	function getBack() {
		window.location.href = "form.php";
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
