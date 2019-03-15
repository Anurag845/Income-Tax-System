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
	
	$month = date('m');
	$emp_id = $_POST['emp_id'];
		
	if($month < 3) {
		$no_of_months = 3 - $month;
	}
	else {
		$no_of_months = 12 - $month + 3;
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
	
	if(!empty($_POST['ann_rent'])) {
	
		$prov_ann_rent = $_POST['ann_rent'];
		//echo $prov_ann_rent;
		$sql = "update Declarations set amount_proved = '$prov_ann_rent', status = 'Proved' where emp_id = '$emp_id' and dec_id = 'ann_rent'";
		mysqli_query($conn,$sql);
			
		$get_declared = "select amount_declared from Declarations where emp_id = '$emp_id' and dec_id = 'ann_rent'";
		$dec = mysqli_query($conn,$get_declared);
		if(!$dec) {
			echo mysqli_error($conn);
		}
		else {
			$temp = $dec->fetch_assoc();
			$dec_ann_rent = $temp['amount_declared'];
		}
		if($dec_ann_rent != $prov_ann_rent) {
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
			if($prov_ann_rent < $limit) {
				$annual_taxable = $annual_taxable - $prov_ann_rent;
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

	
	if(!empty($_POST['medi'])) {
	
		$prov_medi = $_POST['medi'];
		//echo $prov_ann_rent;
		$sql = "update Declarations set amount_proved = '$prov_medi', status = 'Proved' where emp_id = '$emp_id' and dec_id = 'medi'";
		mysqli_query($conn,$sql);
			
		$get_declared = "select amount_declared from Declarations where emp_id = '$emp_id' and dec_id = 'medi'";
		$dec = mysqli_query($conn,$get_declared);
		if(!$dec) {
			echo mysqli_error($conn);
		}
		else {
			$temp = $dec->fetch_assoc();
			$dec_medi = $temp['amount_declared'];
		}
		if($dec_medi != $prov_medi) {
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
			if($prov_medi < $limit) {
				$annual_taxable = $annual_taxable - $prov_medi;
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
	
	
	if(!empty($_POST['home_int'])) {
	
	//echo 'inside home_int';
	
		$prov_home_int = $_POST['home_int'];
		//echo $prov_ann_rent;
		$sql = "update Declarations set amount_proved = '$prov_home_int', status = 'Proved' where emp_id = '$emp_id' and dec_id = 'home_int'";
		mysqli_query($conn,$sql);
			
		$get_declared = "select amount_declared from Declarations where emp_id = '$emp_id' and dec_id = 'home_int'";
		$dec = mysqli_query($conn,$get_declared);
		if(!$dec) {
			echo mysqli_error($conn);
		}
		else {
			$temp = $dec->fetch_assoc();
			$dec_home_int = $temp['amount_declared'];
		}
		if($dec_home_int != $prov_home_int) {
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
			if($prov_home_int < $limit) {
				$annual_taxable = $annual_taxable - $prov_home_int;
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
	
	
	if(!empty($_POST['nat_pen'])) {
	
	//echo 'inside nat_pen';
	
		$prov_nat_pen = $_POST['nat_pen'];
		//echo $prov_ann_rent;
		$sql = "update Declarations set amount_proved = '$prov_nat_pen', status = 'Proved' where emp_id = '$emp_id' and dec_id = 'nat_pen'";
		mysqli_query($conn,$sql);
			
		$get_declared = "select amount_declared from Declarations where emp_id = '$emp_id' and dec_id = 'nat_pen'";
		$dec = mysqli_query($conn,$get_declared);
		if(!$dec) {
			echo mysqli_error($conn);
		}
		else {
			$temp = $dec->fetch_assoc();
			$dec_nat_pen = $temp['amount_declared'];
		}
		if($dec_nat_pen != $prov_nat_pen) {
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
			if($prov_nat_pen < $limit) {
				$annual_taxable = $annual_taxable - $prov_nat_pen;
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
	
	
	if(!empty($_POST['phy_hand'])) {
	
		//echo 'inside phy_hand';
	
		$prov_phy_hand = $_POST['phy_hand'];
		//echo $prov_ann_rent;
		$sql = "update Declarations set amount_proved = '$prov_phy_hand', status = 'Proved' where emp_id = '$emp_id' and dec_id = 'phy_hand'";
		mysqli_query($conn,$sql);
			
		$get_declared = "select amount_declared from Declarations where emp_id = '$emp_id' and dec_id = 'phy_hand'";
		$dec = mysqli_query($conn,$get_declared);
		if(!$dec) {
			echo mysqli_error($conn);
		}
		else {
			$temp = $dec->fetch_assoc();
			$dec_phy_hand = $temp['amount_declared'];
		}
		if($dec_phy_hand != $prov_phy_hand) {
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
			if($prov_phy_hand < $limit) {
				$annual_taxable = $annual_taxable - $prov_phy_hand;
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
	
	
	if(!empty($_POST['edu_int'])) {
	
		//echo 'inside phy_hand';
	
		$prov_edu_int = $_POST['edu_int'];
		//echo $prov_ann_rent;
		$sql = "update Declarations set amount_proved = '$prov_edu_int', status = 'Proved' where emp_id = '$emp_id' and dec_id = 'edu_int'";
		mysqli_query($conn,$sql);
			
		$get_declared = "select amount_declared from Declarations where emp_id = '$emp_id' and dec_id = 'edu_int'";
		$dec = mysqli_query($conn,$get_declared);
		if(!$dec) {
			echo mysqli_error($conn);
		}
		else {
			$temp = $dec->fetch_assoc();
			$dec_edu_int = $temp['amount_declared'];
		}
		if($dec_edu_int != $prov_edu_int) {
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
			if($prov_edu_int < $limit) {
				$annual_taxable = $annual_taxable - $prov_edu_int;
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
	
	
	if(!empty($_POST['cpf'])) {
		$p_cpf = $_POST['cpf'];
		$get_cpf = "select amount_declared from Declarations where dec_id = 'cpf' and emp_id = '$emp_id'";
		$res = mysqli_query($conn,$get_cpf);
		if(!$res) {
			echo mysqli_error($conn);
		}
		else {
			$temp = $res->fetch_assoc();
			$cpf = $temp['amount_declared'];
		}
	}
	else {
		$get_cpf = "select amount_declared,amount_proved,status from Declarations where emp_id = '$emp_id' and dec_id = 'cpf'";
		$res = mysqli_query($conn,$get_cpf);
		$num_rows = mysqli_num_rows($res);
		if($num_rows > 0) {
			$temp = $res->fetch_assoc();
			$status = $temp['status'];
			if($status == 'Pending') {
				$cpf = $temp['amount_declared'];
			}
			else if($status == 'Proved') {
				$cpf = $temp['amount_proved'];
			}
		}
		else {
			$cpf = 0;
		}
	}
	
	
	if(!empty($_POST['ppf'])) {
		$p_ppf = $_POST['ppf'];
		$get_ppf = "select amount_declared from Declarations where dec_id = 'ppf' and emp_id = '$emp_id'";
		$res = mysqli_query($conn,$get_ppf);
		if(!$res) {
			echo mysqli_error($conn);
		}
		else {
			$temp = $res->fetch_assoc();
			$ppf = $temp['amount_declared'];
		}
	}
	else {
		$get_ppf = "select amount_declared,amount_proved,status from Declarations where emp_id = '$emp_id' and dec_id = 'ppf'";
		$res = mysqli_query($conn,$get_ppf);
		$num_rows = mysqli_num_rows($res);
		if($num_rows > 0) {
			$temp = $res->fetch_assoc();
			$status = $temp['status'];
			if($status == 'Pending') {
				$ppf = $temp['amount_declared'];
			}
			else if($status == 'Proved') {
				$ppf = $temp['amount_proved'];
			}
		}
		else {
			$ppf = 0;
		}
	}
	
	if(!empty($_POST['nsc'])) {
		$p_nsc = $_POST['nsc'];
		$get_nsc = "select amount_declared from Declarations where dec_id = 'nsc' and emp_id = '$emp_id'";
		$res = mysqli_query($conn,$get_nsc);
		if(!$res) {
			echo mysqli_error($conn);
		}
		else {
			$temp = $res->fetch_assoc();
			$nsc = $temp['amount_declared'];
		}
	}
	else {
		$get_nsc = "select amount_declared,amount_proved,status from Declarations where emp_id = '$emp_id' and dec_id = 'nsc'";
		$res = mysqli_query($conn,$get_nsc);
		$num_rows = mysqli_num_rows($res);
		if($num_rows > 0) {
			$temp = $res->fetch_assoc();
			$status = $temp['status'];
			if($status == 'Pending') {
				$nsc = $temp['amount_declared'];
			}
			else if($status == 'Proved') {
				$nsc = $temp['amount_proved'];
			}
		}
		else {
			$nsc = 0;
		}
	}
	
	if(!empty($_POST['ulip'])) {
		$p_ulip = $_POST['ulip'];
		$get_ulip = "select amount_declared from Declarations where dec_id = 'ulip' and emp_id = '$emp_id'";
		$res = mysqli_query($conn,$get_ulip);
		if(!$res) {
			echo mysqli_error($conn);
		}
		else {
			$temp = $res->fetch_assoc();
			$ulip = $temp['amount_declared'];
		}
	}
	else {
		$get_ulip = "select amount_declared,amount_proved,status from Declarations where emp_id = '$emp_id' and dec_id = 'ulip'";
		$res = mysqli_query($conn,$get_ulip);
		$num_rows = mysqli_num_rows($res);
		if($num_rows > 0) {
			$temp = $res->fetch_assoc();
			$status = $temp['status'];
			if($status == 'Pending') {
				$ulip = $temp['amount_declared'];
			}
			else if($status == 'Proved') {
				$ulip = $temp['amount_proved'];
			}
		}
		else {
			$ulip = 0;
		}
	}
	
	if(!empty($_POST['ann_ins'])) {
		$p_ann_ins = $_POST['ann_ins'];
		$get_ann_ins = "select amount_declared from Declarations where dec_id = 'ann_ins' and emp_id = '$emp_id'";
		$res = mysqli_query($conn,$get_ann_ins);
		if(!$res) {
			echo mysqli_error($conn);
		}
		else {
			$temp = $res->fetch_assoc();
			$ann_ins = $temp['amount_declared'];
		}
	}
	else {
		$get_ann_ins = "select amount_declared,amount_proved,status from Declarations where emp_id = '$emp_id' and dec_id = 'ann_ins'";
		$res = mysqli_query($conn,$get_ann_ins);
		$num_rows = mysqli_num_rows($res);
		if($num_rows > 0) {
			$temp = $res->fetch_assoc();
			$status = $temp['status'];
			if($status == 'Pending') {
				$ann_ins = $temp['amount_declared'];
			}
			else if($status == 'Proved') {
				$ann_ins = $temp['amount_proved'];
			}
		}
		else {
			$ann_ins = 0;
		}
	}
	
	if(!empty($_POST['hsg_loan_prin'])) {
		$p_hsg_loan_prin = $_POST['hsg_loan_prin'];
		$get_hsg_loan_prin = "select amount_declared from Declarations where dec_id = 'hsg_loan_prin' and emp_id = '$emp_id'";
		$res = mysqli_query($conn,$get_hsg_loan_prin);
		if(!$res) {
			echo mysqli_error($conn);
		}
		else {
			$temp = $res->fetch_assoc();
			$hsg_loan_prin = $temp['amount_declared'];
		}
	}
	else {
		$get_hsg_loan_prin = "select amount_declared,amount_proved,status from Declarations where emp_id = '$emp_id' and dec_id = 'hsg_loan_prin'";
		$res = mysqli_query($conn,$get_hsg_loan_prin);
		$num_rows = mysqli_num_rows($res);
		if($num_rows > 0) {
			$temp = $res->fetch_assoc();
			$status = $temp['status'];
			if($status == 'Pending') {
				$hsg_loan_prin = $temp['amount_declared'];
			}
			else if($status == 'Proved') {
				$hsg_loan_prin = $temp['amount_proved'];
			}
		}
		else {
			$hsg_loan_prin = 0;
		}
	}
	
	if(!empty($_POST['tuition_fee'])) {
		$p_tuition_fee = $_POST['tuition_fee'];
		$get_tuition_fee = "select amount_declared from Declarations where dec_id = 'tuition_fee' and emp_id = '$emp_id'";
		$res = mysqli_query($conn,$get_tuition_fee);
		if(!$res) {
			echo mysqli_error($conn);
		}
		else {
			$temp = $res->fetch_assoc();
			$tuition_fee = $temp['amount_declared'];
		}
	}
	else {
		$get_tuition_fee = "select amount_declared,amount_proved,status from Declarations where emp_id = '$emp_id' and dec_id = 'tuition_fee'";
		$res = mysqli_query($conn,$get_tuition_fee);
		$num_rows = mysqli_num_rows($res);
		if($num_rows > 0) {
			$temp = $res->fetch_assoc();
			$status = $temp['status'];
			if($status == 'Pending') {
				$tuition_fee = $temp['amount_declared'];
			}
			else if($status == 'Proved') {
				$tuition_fee = $temp['amount_proved'];
			}
		}
		else {
			$tuition_fee = 0;
		}
	}
	
	if(!empty($_POST['bank_deposit'])) {
		$p_bank_deposit = $_POST['bank_deposit'];
		$get_bank_deposit = "select amount_declared from Declarations where dec_id = 'bank_deposit' and emp_id = '$emp_id'";
		$res = mysqli_query($conn,$get_bank_deposit);
		if(!$res) {
			echo mysqli_error($conn);
		}
		else {
			$temp = $res->fetch_assoc();
			$bank_deposit = $temp['amount_declared'];
		}
	}
	else {
		$get_bank_deposit = "select amount_declared,amount_proved,status from Declarations where emp_id = '$emp_id' and dec_id = 'bank_deposit'";
		$res = mysqli_query($conn,$get_bank_deposit);
		$num_rows = mysqli_num_rows($res);
		if($num_rows > 0) {
			$temp = $res->fetch_assoc();
			$status = $temp['status'];
			if($status == 'Pending') {
				$bank_deposit = $temp['amount_declared'];
			}
			else if($status == 'Proved') {
				$bank_deposit = $temp['amount_proved'];
			}
		}
		else {
			$bank_deposit = 0;
		}
	}
	
	if(!empty($_POST['reg_fee'])) {
		$p_reg_fee = $_POST['reg_fee'];
		$get_reg_fee = "select amount_declared from Declarations where dec_id = 'reg_fee' and emp_id = '$emp_id'";
		$res = mysqli_query($conn,$get_reg_fee);
		if(!$res) {
			echo mysqli_error($conn);
		}
		else {
			$temp = $res->fetch_assoc();
			$reg_fee = $temp['amount_declared'];
		}
	}
	else {
		$get_reg_fee = "select amount_declared,amount_proved,status from Declarations where emp_id = '$emp_id' and dec_id = 'reg_fee'";
		$res = mysqli_query($conn,$get_reg_fee);
		$num_rows = mysqli_num_rows($res);
		if($num_rows > 0) {
			$temp = $res->fetch_assoc();
			$status = $temp['status'];
			if($status == 'Pending') {
				$reg_fee = $temp['amount_declared'];
			}
			else if($status == 'Proved') {
				$reg_fee = $temp['amount_proved'];
			}
		}
		else {
			$reg_fee = 0;
		}
	}
	
	$old_inv = $cpf + $ppf + $nsc + $ulip + $ann_ins + $hsg_loan_prin + $tuition_fee + $bank_deposit + $reg_fee;	
	$new_inv = 0;
	
	if(!empty($p_cpf)) {
		$new_inv += $p_cpf;
		$sql = "update Declarations set amount_proved = '$p_cpf', status = 'Proved' where emp_id = '$emp_id' and dec_id = 'cpf'";
		mysqli_query($conn,$sql);
	}
	else {
		$new_inv += $cpf;
	}
	
	if(!empty($p_ppf)) {
		$new_inv += $p_ppf;
		$sql = "update Declarations set amount_proved = '$p_ppf', status = 'Proved' where emp_id = '$emp_id' and dec_id = 'ppf'";
		mysqli_query($conn,$sql);
	}
	else {
		$new_inv += $ppf;
	}
	
	if(!empty($p_nsc)) {
		$new_inv += $p_nsc;
		$sql = "update Declarations set amount_proved = '$p_nsc', status = 'Proved' where emp_id = '$emp_id' and dec_id = 'nsc'";
		mysqli_query($conn,$sql);
	}
	else {
		$new_inv += $nsc;
	}
	
	if(!empty($p_ulip)) {
		$new_inv += $p_ulip;
		$sql = "update Declarations set amount_proved = '$p_ulip', status = 'Proved' where emp_id = '$emp_id' and dec_id = 'ulip'";
		mysqli_query($conn,$sql);
	}
	else {
		$new_inv += $ulip;
	}
	
	if(!empty($p_ann_ins)) {
		$new_inv += $p_ann_ins;
		$sql = "update Declarations set amount_proved = '$p_ann_ins', status = 'Proved' where emp_id = '$emp_id' and dec_id = 'ann_ins'";
		mysqli_query($conn,$sql);
	}
	else {
		$new_inv += $ann_ins;
	}
	
	if(!empty($p_hsg_loan_prin)) {
		$new_inv += $p_hsg_loan_prin;
		$sql = "update Declarations set amount_proved = '$p_hsg_loan_prin', status = 'Proved' where emp_id = '$emp_id' and dec_id = 'hsg_loan_prin'";
		mysqli_query($conn,$sql);
	}
	else {
		$new_inv += $hsg_loan_prin;
	}
	
	if(!empty($p_tuition_fee)) {
		$new_inv += $p_tuition_fee;
		$sql = "update Declarations set amount_proved = '$p_tuition_fee', status = 'Proved' where emp_id = '$emp_id' and dec_id = 'tuition_fee'";
		mysqli_query($conn,$sql);
	}
	else {
		$new_inv += $tuition_fee;
	}
	
	if(!empty($p_bank_deposit)) {
		$new_inv += $p_bank_deposit;
		$sql = "update Declarations set amount_proved = '$p_bank_deposit', status = 'Proved' where emp_id = '$emp_id' and dec_id = 'bank_deposit'";
		mysqli_query($conn,$sql);
	}
	else {
		$new_inv += $bank_deposit;
	}
	
	if(!empty($p_reg_fee)) {
		$new_inv += $p_reg_fee;
		$sql = "update Declarations set amount_proved = '$p_reg_fee', status = 'Proved' where emp_id = '$emp_id' and dec_id = 'reg_fee'";
		mysqli_query($conn,$sql);
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

</script>

</html>
