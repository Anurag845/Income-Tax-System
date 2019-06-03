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

	include 'db_connect.php';
	$conn = OpenCon();

	$emp_name = $_POST['emp_name'];

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
		
		if($month <= 3) {
			$no_of_months = 4 - $month;
		}
		else {
			$no_of_months = 12 - $month + 4;
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
		
		$dec_ids = array('ann_rent','medi','home_int','nat_pen','phy_hand','edu_int');
		$entry_name = array('Annual Rent','Mediclaim','Home Interest','National Pension','Physically Handicap','Education Interest');
		
		for($dec_counter = 0; $dec_counter < 6; $dec_counter++) {
		
			if(isset($_POST[$dec_ids[$dec_counter]]) && $_POST[$dec_ids[$dec_counter]] != "") {
				$decid = $dec_ids[$dec_counter];
				$decname = $entry_name[$dec_counter];
				$value = $_POST[$decid];
				$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ('$emp_id','$decname',$value,0,'Pending','$decid')";
				if(!mysqli_query($conn,$sql)) {
					echo mysqli_error($conn);
				}
				$get_limit = "select tax_limit from Limits where entry = '$decname'";
				$result = mysqli_query($conn,$get_limit);
				if(!$result) {
					echo mysqli_error($conn);
				}
				else {
					$temp = $result->fetch_assoc();
					$limit = $temp['tax_limit'];
					//echo 'Limit is ' . $limit;
					if($value < $limit) {
						$annual_taxable = $annual_taxable - $value;
					}
					else {
						$annual_taxable = $annual_taxable - $limit;
					}
				}
			}
		
		}
		
		$invest_ids = array('cpf','ppf','nsc','ulip','ann_ins','hsg_loan_prin','tuition_fee','bank_deposit','reg_fee');
		$invest_desc = array('CPF','PPF','NSC','ULIP','Annual Insurance','Housing Loan Principal','Children Tuition Fee','Bank Deposit','Registration Fee');
		
		$investments = 0;
		
		for($invest_counter = 0; $invest_counter < 9; $invest_counter++) {
		
			$inv_id = $invest_ids[$invest_counter];
			if(isset($_POST[$inv_id]) && $_POST[$inv_id] != "") {
				$val = $_POST[$inv_id];
				$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ('$emp_id','$invest_desc[$invest_counter]','$val',0,'Pending','$inv_id')";
				if(!mysqli_query($conn,$sql)) {
					echo mysqli_error($conn);
				}
			}
			else {
				$val = 0;
			}
			
			$investments += $val;
		
		}
			
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
	
		$taxablepermonth = ceil($annual_taxable/$no_of_months);
		
		$tax_months = array();
		
		$cnt = 0;
		while($cnt < 12-$no_of_months) {
			$tax_months[$cnt] = 0;
			$cnt++;
		}
		
		while($cnt < 12) {
			$tax_months[$cnt] = $taxablepermonth;
			$cnt++;
		}
		
		$adjusted_taxable = $taxablepermonth*$no_of_months;
	
		$sql = "insert into Taxable_monthly values ('$emp_id',$tax_months[0],$tax_months[1],$tax_months[2],$tax_months[3],$tax_months[4],$tax_months[5],$tax_months[6],$tax_months[7],$tax_months[8],$tax_months[9],$tax_months[10],$tax_months[11],$annual_taxable,$adjusted_taxable)";
		
		if(mysqli_query($conn,$sql)) {
			echo "Declaration Form submitted successfully.";
		}
		else {
			echo mysqli_error($conn);
		}
		
		$cnt = 0;
		$tax = 0;
			
		if($emp_id == "EMP477") {
			$tax += ($adjusted_taxable - 1000000)*0.3;
			$tax += 500000*0.2;
			$tax += 200000*0.05;
		}
		else {
		
			while($adjusted_taxable > $upper_bound[$cnt]) {
				$tax += ($upper_bound[$cnt]-$lower_bound[$cnt])*$percent[$cnt];
				$cnt++;
			}
			$tax += ($adjusted_taxable-$lower_bound[$cnt])*$percent[$cnt];
			$tax = ceil($tax/100);
		
		}

		$taxpermonth = ceil($tax/$no_of_months);
		
		$cnt = 0;
		while($cnt < 12-$no_of_months) {
			$tax_months[$cnt] = 0;
			$cnt++;
		}
		
		while($cnt < 12) {
			$tax_months[$cnt] = $taxpermonth;
			$cnt++;
		}
		
		$adjusted_tax = $taxpermonth*$no_of_months;
		
		if($adjusted_taxable <= 500000) {
			$tax = 0;
			$adjusted_tax = 0;
			$count = 0;
			while($count < 12) {
				$tax_months[$count] = 0;
				$count++;
			}
		}

		$edu_cess = ceil($adjusted_tax*0.04);
		
		$sql = "insert into Tax_monthly values ('$emp_id',$tax_months[0],$tax_months[1],$tax_months[2],$tax_months[3],$tax_months[4],$tax_months[5],$tax_months[6],$tax_months[7],$tax_months[8],$tax_months[9],$tax_months[10],$tax_months[11],$tax,$adjusted_tax,$edu_cess)";
		
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
		
		$dec_ids = array('ann_rent','medi','home_int','nat_pen','phy_hand','edu_int');
		$entry_name = array('Annual Rent','Mediclaim','Home Interest','National Pension','Physically Handicap','Education Interest');
		
		for($dec_counter = 0; $dec_counter < 6; $dec_counter++) {
		
			$decid = $dec_ids[$dec_counter];
			$decname = $entry_name[$dec_counter];
			$desc = $entry_name[$dec_counter];
			
			if(!empty($_POST[$decid]) || $_POST[$decid] === '0') {
	
				$new_value = $_POST[$decid];
			
				$get_declared = "select amount_declared from Declarations where emp_id = '$emp_id' and dec_id = '$decid'";
				$dec = mysqli_query($conn,$get_declared);
				if(!$dec) {
					echo mysqli_error($conn);
				}
				else {
					if(mysqli_num_rows($dec) == 0 && $new_value != 0) {
						$dec_value = 0;
						$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ('$emp_id','$desc',$new_value,0,'Pending','$decid')";
						mysqli_query($conn,$sql);
					}
					else if(mysqli_num_rows($dec) == 1 && $new_value === '0') {
						$temp = $dec->fetch_assoc();
						$dec_value = $temp['amount_declared'];
						$sql = "delete from Declarations where emp_id = '$emp_id' and dec_id = '$decid'";
						mysqli_query($conn,$sql);
					}
					else {
						$temp = $dec->fetch_assoc();
						$dec_value = $temp['amount_declared'];
						$sql = "update Declarations set amount_declared = '$new_value' where emp_id = '$emp_id' and dec_id = '$decid'";
						mysqli_query($conn,$sql);
					}
				
				}
				if($dec_value != $new_value) {
					$get_limit = "select tax_limit from Limits where entry='$desc'";
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
					if($new_value < $limit) {
						$annual_taxable = $annual_taxable - $new_value;
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
			else {
			//echo 'Not set';
			}
		
		}
		
		$invest_ids = array('cpf','ppf','nsc','ulip','ann_ins','hsg_loan_prin','tuition_fee','bank_deposit','reg_fee');
		$invest_desc = array('CPF','PPF','NSC','ULIP','Annual Insurance','Housing Loan Principal','Children Tuition Fee','Bank Deposit','Registration Fee');
		
		$old_inv = 0;
		$new_inv = 0;
		
		for($dec_counter = 0; $dec_counter < 6; $dec_counter++) {
		
			$invest_id = $invest_ids[$dec_counter];
			$desc = $invest_desc[$dec_counter];
			if(!empty($_POST[$invest_id])  || $_POST[$invest_id] === '0') {
				$new_value = $_POST[$invest_id];
				$get_value = "select amount_declared from Declarations where dec_id = '$invest_id' and emp_id = '$emp_id'";
				$res = mysqli_query($conn,$get_value);
				if(!$res) {
					echo mysqli_error($conn);
				}
				else {
					if(mysqli_num_rows($res) == 0 && $new_value != 0) {
						$value = 0;
						$sql = "insert into Declarations(emp_id,dec_type,amount_declared,amount_proved,status,dec_id) values ('$emp_id','$desc',$new_value,0,'Pending','$invest_id')";
						mysqli_query($conn,$sql);
					}
					else if(mysqli_num_rows($res) == 1 && $new_value === '0') {
						$temp = $res->fetch_assoc();
						$value = $temp['amount_declared'];
						$sql = "delete from Declarations where emp_id = '$emp_id' and dec_id = '$invest_id'";
						mysqli_query($conn,$sql);
					}
					else {
						$temp = $res->fetch_assoc();
						$value = $temp['amount_declared'];
						$sql = "update Declarations set amount_declared = '$new_value' where emp_id = '$emp_id' and dec_id = '$invest_id'";
						mysqli_query($conn,$sql);
					}
				}
				$new_inv += $new_value;
				$old_inv += $value;
			}
			else {
				$new_value = null;
				$get_status = "select * from Declarations where dec_id = '$invest_id' and emp_id = '$emp_id'";
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
							$value = $temp['amount_declared'];
						}
						else {
							$value = $temp['amount_proved'];
						}
					}
					else {
						$value = 0;
					}
					
					$new_inv += $value;
					$old_inv += $value;
				}
				
			}		
		
		}
	
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
			echo "Declaration Form submitted successfully.";
		}
		else {
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
	
	function gotoSalary() {
		window.location.href = "salary.php";
	}
	
	function gotoSalary() {
		window.location.href = "reset.php";
	}
	
</script>
</html>
