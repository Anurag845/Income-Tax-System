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
	
	td {
		padding: 10px 20px;
	}

	fieldset {
		width: 50%;
	}
	
	input[type="submit"] {
		margin-top: 35px;
		margin-bottom: 35px;
	}
	
	fieldset {
		margin: 40px;
	}
	
	#decide input {
		padding: 10px;
		margin-left: 20px;
		margin-right: 20px;
	}
	
	#new_field,#new_std {
		display: none;
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
	
		<h2>Add New Entry</h2>
	
		<center>
		
			<div id="decide">
				<input type="radio" name="new_type" value="field" onclick="show_field();">Field
				<input type="radio" name="new_type" value="std_deduc" onclick="show_std();">Standard Deduction
			</div>
		
			<div id="new_field">

				<form action="submit_fields.php" method="POST">
				
					<fieldset>
						<legend>New Field</legend>
					<table id="field">
						<tr>
							<td><label for="dec_name">Enter declaration field name</label></td>
							<td><input type="text" name="dec_name"></td>
						</tr>
						<tr>
							<td><label for="dec_desc">Enter field description</label></td>
							<td><textarea rows="3" name="dec_desc"></textarea></td>
						</tr>
						<tr>
							<td><label for="dec_limit">Enter exemption limit</label></td>
							<td><input type="number" name="dec_limit"></td>
						</tr>
						<tr>
							<td><label for="sub_field">Sub-fields present?</label></td>
							<td><input type="radio" id="yes" name="sub_field_present" value="yes" onclick="show_num();">Yes
								<input type="radio" id="no" name="sub_field_present" value="no" onclick="hide_num();">No</td>
						</tr>			
					</table>
					<input type="submit" name="submit" value="Submit Field">
					</fieldset>
				
				</form>
		
			</div>
			
			<div id="new_std">
				
				<form action="submit_fields.php" method="POST">
				
					<fieldset>
						<legend>New Deduction</legend>
						<table>
							<tr>
								<td><label for="ded_name">Enter deduction name</label></td>
								<td><input type="text" name="ded_name"></td>
							</tr>
							<tr>
								<td><label for="ded_desc">Enter deduction description</label></td>
								<td><textarea rows="3" name="ded_desc"></textarea></td>
							</tr>
							<tr>
								<td><label for="ded_value">Enter deduction value</td>
								<td><input type="number" name="ded_value"></td>
							</tr>
						</table>
						<input type="submit" name="submit" value="Submit Deduction">
					</fieldset>
				
				</form>
				
			</div>
			
		</center>
	
	</div>

</body>

<script>

	function show_field() {
		var std = document.getElementById("new_std");
		std.style.display = "none";
		var field = document.getElementById("new_field");
		field.style.display = "block";
	}

	function show_std() {
		var field = document.getElementById("new_field");
		field.style.display = "none";
		var std = document.getElementById("new_std");
		std.style.display = "block";
	}
	
	function show_num() {
		var table = document.getElementById("field");
		var len = table.rows.length;
		if(len == 4) {
			var num_row = table.insertRow();
			var num_label = num_row.insertCell();
			num_label.innerHTML = "<td><label for='sub_num'>Enter number of sub-fields</label></td>";
			var num_input = num_row.insertCell();
			num_input.innerHTML = "<td><input type='number' id='sub_num' name='sub_num' onkeydown='delete_sub();' onkeyup='insert_sub();'></td>";
		}
	}
	
	function hide_num() {
		var table = document.getElementById("field");
		var len = table.rows.length;
		for(var cnt=len-1;cnt>3;cnt--) {
			table.deleteRow(cnt);
		}
	}
	
	function insert_sub() {
		var no = document.getElementById("sub_num").value;
		for(var cnt=0;cnt<no;cnt++) {
			var table = document.getElementById("field");
			var name_row = table.insertRow();
			var name_label = name_row.insertCell();
			name_label.innerHTML = "<td><label for='sub_field_"+cnt+"'>Enter name of sub-field "+cnt+"</label></td>";
			var name_input = name_row.insertCell();
			name_input.innerHTML = "<td><input type='text' name='sub_field[]'></td>";
		}
	}

	function delete_sub() {
		var table = document.getElementById("field");
		var len = table.rows.length;
		for(var cnt=len-1;cnt>4;cnt--) {
			table.deleteRow(cnt);
		}
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
