<!DOCTYPE html>

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
		
		h2 {
			margin-top: 0;
			text-align: center;
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
  			<a onclick="gotoLimit();">Exemption Limits</a>
  			<a onclick="gotoSlabs();">Tax Slabs</a>
  			<a onclick="gotoSalary();">Gross Salary</a>
  			<a onclick="gotoReset();">Reset</a>
  			<a onclick="gotoAddField();">Add Field</a>
  			<a onclick="gotoRemoveField();">Remove Field</a>
		</div>
				
	</div>
	
	<div class='contents'>

    <h2>
        Reset
    </h2>

    <center>
    	<form method="POST">
    		<button type="submit" value="Reset all Records" name="rst_btn">Reset all Records</button>
    	</form>
		<?php
			
			if(isset($_POST['rst_btn'])) {
	
				include 'db_connect.php';
				$conn = OpenCon();
				
				$flag = "False";
				$del = "delete from Declarations";
				if(mysqli_query($conn,$del)) {
					$flag = "True";
				}
		
				$del = "delete from Taxable_monthly";
				if(mysqli_query($conn,$del)) {
					$flag = "True";
				}
		
				$del = "delete from Tax_monthly";
				if(mysqli_query($conn,$del)) {
					$flag = "True";
				}
		
				if($flag == "True") {
					echo "<br>";
					echo "Records deleted successfully.";
				}
			
				CloseCon($conn);
			}
		?>
    </center>
    
    </div>

    <script>
		
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

</body>
