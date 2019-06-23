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
  			<a onclick="gotoLimit();">Exemption Limits</a>
  			<a onclick="gotoSlabs();">Tax Slabs</a>
  			<a onclick="gotoSalary();">Gross Salary</a>
			<a onclick="gotoReset();">Reset</a>
			<a onclick="gotoAddField();">Add Field</a>
			<a onclick="gotoRemoveField();">Remove Field</a>
		</div>
				
	</div>
	
	<div class="contents">

		<?php

			include 'db_connect.php';

			$conn = OpenCon();
			
			$month = date('m');
			if($month <= 3) {
				$no_of_months = 4 - $month;
			}
			else {
				$no_of_months = 12 - $month + 4;
			}
			
			$lower_bound = array();
			$upper_bound = array();
			$percent = array();
			$cnt = 0;
	
			$s1_p = $_POST['slab_1'];
			$s2_p = $_POST['slab_2'];
			$s3_p = $_POST['slab_3'];
			$s4_p = $_POST['slab_4'];
			$s5_p = $_POST['slab_5'];
			$s6_p = $_POST['slab_6'];
			$s7_p = $_POST['slab_7'];
			
			$s1_lb = $_POST['lbslab_1'];
			$s2_lb = $_POST['lbslab_2'];
			$s3_lb = $_POST['lbslab_3'];
			$s4_lb = $_POST['lbslab_4'];
			$s5_lb = $_POST['lbslab_5'];
			$s6_lb = $_POST['lbslab_6'];
			$s7_lb = $_POST['lbslab_7'];
			
			$s1_ub = $_POST['ubslab_1'];
			$s2_ub = $_POST['ubslab_2'];
			$s3_ub = $_POST['ubslab_3'];
			$s4_ub = $_POST['ubslab_4'];
			$s5_ub = $_POST['ubslab_5'];
			$s6_ub = $_POST['ubslab_6'];
			$s7_ub = $_POST['ubslab_7'];
			
			mysqli_query($conn,'Delete from Tax_slabs');
			
			if((!empty($s1_p) || $s1_p==0) && (!empty($s1_lb) || $s1_lb==0) && (!empty($s1_ub) || $s1_ub==0)) {
				$lower_bound[$cnt] = $s1_lb;
				$upper_bound[$cnt] = $s1_ub;
				$percent[$cnt] = $s1_p;
				$cnt++;
				$sql = "insert into Tax_slabs values($s1_lb,$s1_ub,$s1_p)";
				mysqli_query($conn,$sql);
			}
			
			if((!empty($s2_p) || $s2_p==0) && (!empty($s2_lb) || $s2_lb==0) && (!empty($s2_ub) || $s2_ub==0)) {
				$lower_bound[$cnt] = $s2_lb;
				$upper_bound[$cnt] = $s2_ub;
				$percent[$cnt] = $s2_p;
				$cnt++;
				$sql = "insert into Tax_slabs values($s2_lb,$s2_ub,$s2_p)";
				mysqli_query($conn,$sql);
			}
			
			if((!empty($s3_p) || $s3_p==0) && (!empty($s3_lb) || $s3_lb==0) && (!empty($s3_ub) || $s3_ub==0)) {
				$lower_bound[$cnt] = $s3_lb;
				$upper_bound[$cnt] = $s3_ub;
				$percent[$cnt] = $s3_p;
				$cnt++;
				$sql = "insert into Tax_slabs values($s3_lb,$s3_ub,$s3_p)";
				mysqli_query($conn,$sql);
			}
			
			if((!empty($s4_p) || $s4_p==0) && (!empty($s4_lb) || $s4_lb==0) && (!empty($s4_ub) || $s4_ub==0)) {
				$lower_bound[$cnt] = $s4_lb;
				$upper_bound[$cnt] = $s4_ub;
				$percent[$cnt] = $s4_p;
				$cnt++;
				$sql = "insert into Tax_slabs values($s4_lb,$s4_ub,$s4_p)";
				mysqli_query($conn,$sql);
			}
			
			if((!empty($s5_p) || $s5_p==0) && (!empty($s5_lb) || $s5_lb==0) && (!empty($s5_ub) || $s5_ub==0)) {
				$lower_bound[$cnt] = $s5_lb;
				$upper_bound[$cnt] = $s5_ub;
				$percent[$cnt] = $s5_p;
				$cnt++;
				$sql = "insert into Tax_slabs values($s5_lb,$s5_ub,$s5_p)";
				mysqli_query($conn,$sql);
			}
			
			if((!empty($s6_p) || $s6_p==0) && (!empty($s6_lb) || $s6_lb==0) && (!empty($s6_ub) || $s6_ub==0)) {
				$lower_bound[$cnt] = $s6_lb;
				$upper_bound[$cnt] = $s6_ub;
				$percent[$cnt] = $s6_p;
				$cnt++;
				$sql = "insert into Tax_slabs values($s6_lb,$s6_ub,$s6_p)";
				mysqli_query($conn,$sql);
			}
			
			if((!empty($s7_p) || $s7_p==0) && (!empty($s7_lb) || $s7_lb==0) && (!empty($s7_ub) || $s7_ub==0)) {
				$lower_bound[$cnt] = $s7_lb;
				$upper_bound[$cnt] = $s7_ub;
				$percent[$cnt] = $s7_p;
				$cnt++;
				$sql = "insert into Tax_slabs values($s7_lb,$s7_ub,$s7_p)";
				mysqli_query($conn,$sql);
			}
			
			echo 'Slabs updated successfully. ';
			
			$dec_sql = "select tax_limit,dec_id,sub_field from Limits";
			$decs = mysqli_query($conn,$dec_sql);
			
			if(!$decs) {
				echo mysqli_error($conn);
			}
			else {
				$dec_ids = array();
				$sub_present = array();
				$limits = array();
				$cnt = 0;
				while($row = mysqli_fetch_array($decs)) {
					$dec_ids[$cnt] = $row['dec_id'];
					$sub_present[$cnt] = $row['sub_field'];
					$limits[$cnt] = $row['tax_limit'];
					$cnt++;
				}
			}			
			
			$month = date('m');
			if($month <= 3) {
				$no_of_months = 4 - $month;
			}
			else {
				$no_of_months = 12 - $month + 4;
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
					
					$get_dec = "select * from Declarations where emp_id='$emp_id'";
					$decs = mysqli_query($conn,$get_dec);
					
					if(!$decs) {
						echo mysqli_error($conn);
					}
					else {
						$declared_id = array();
						$amount = array();
						$status = array();
						$dec_cnt = 0;
						while($row = mysqli_fetch_array($decs)) {
							$declared_id[$dec_cnt] = $row['dec_id'];
							$status[$dec_cnt] = $row['status'];
							if($status[$dec_cnt] == "Proved") {
								$amount[$dec_cnt] = $row['amount_proved'];
							}
							else {
								$amount[$dec_cnt] = $row['amount_declared'];
							}
							$dec_cnt++;
						}
					}
					
					$c = 0;
					while($c < $cnt) {
						if($sub_present[$c] == "yes") {
							$get_subs = "select sub_id from Dec_sub_fields where field_id = '$dec_ids[$c]'";
							$res = mysqli_query($conn,$get_subs);
							if(!$res) {
								echo mysqli_error($conn);
							}
							else {
								$sub_id = array();
								$sub_cnt = 0;
								while($row = mysqli_fetch_array($res)) {
									$sub_id[$sub_cnt] = $row['sub_id'];
									$sub_cnt++;
								}
							}
							$value = 0;
							for($i = 0; $i < $sub_cnt; $i++) {
								for($j = 0; $j < $dec_cnt; $j++) {
									if($sub_id[$i] == $declared_id[$j]) {
										$value += $amount[$j];
									}
								}
							}
							if($value < $limits[$c]) {
								$annual_taxable -= $value;
							}
							else {
								$annual_taxable -= $limits[$c];
							}
						}
						else {
							for($k = 0; $k < $dec_cnt; $k++) {
								if($declared_id[$k] == $dec_ids[$c]) {
									$value = $amount[$k];
									$limit = $limits[$c];
									if($value < $limit) {
										$annual_taxable -= $value;
									}
									else {
										$annual_taxable -= $limit;
									}
									break;
								}
							}
						}
						$c++;
					}
			
					$get_std = "select value from Standard_Deduc";
					$std = mysqli_query($conn,$get_std);
					if(!$std) {
						echo mysqli_error($conn);
					}
					else {
						while($row = mysqli_fetch_array($std)) {
							$annual_taxable -= $row['value'];
						}
					}
			
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
 				
					$count = 0;
					$taxable = 0;
					while($count < 12-$no_of_months) {
						$taxable += $tax_months[$count];
						$count += 1;
					}
 				
					$adjust = $annual_taxable - $taxable;
					$adjust_monthly = ceil($adjust/$no_of_months);
 				
					while($count < 12) {
						$tax_months[$count] = $adjust_monthly;
						$count += 1;
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
					}
					else {
						$taxable_flag = false;
						echo mysqli_error($conn);
					}
			
					$count = 0;
					$new_tax = 0;
					while($adjusted_taxable > $upper_bound[$count]) {
						$new_tax += ($upper_bound[$count]-$lower_bound[$count])*$percent[$count];
						$count++;
					}
					$new_tax += ($adjusted_taxable-$lower_bound[$count])*$percent[$count];
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
 				
					$count = 0;
					$tax_ald = 0;
					while($count < 12-$no_of_months) {
						$tax_ald += $tax_months[$count];
						$count += 1;
					}
 				
					$adjust = $new_tax - $tax_ald;
					$adjust_monthly = ceil($adjust/$no_of_months);
 				
					while($count < 12) {
						$tax_months[$count] = $adjust_monthly;
						$count += 1;
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
					}
					else {
						$tax_flag = false;
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
		window.location.href = "tax_slabs.php";
	}

	function gotoHome() {
		window.location.href = "admin_menu.html";
	}
   
	function gotoLimit() {
		window.location.href = "limits.php";
	}
	
	function gotoSlabs() {
		window.location.href = "tax_slabs.php";
	}

	function gotoSalary() {
		window.location.href = "salary.php";
	}
	
	function gotoReset() {
		window.location.href = "reset.php";
	}
	
	function gotoAddField() {
		window.location.href = "new_fields.php";
	}
	
	function gotoRemoveField() {
		window.location.href = "remove_fields.php";
	}

</script>

</html>
