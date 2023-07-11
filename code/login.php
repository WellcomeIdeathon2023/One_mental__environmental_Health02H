<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>One-Health-02H</title>
	<link rel="shortcut icon" type="image/x-icon" href="images/uol.ico" />
	<link rel="stylesheet" href="css/app.css"/>

	<link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body{ font: 14px sans-serif; }
        .wrapper{ width: 460px; padding: 20px; margin: 0 auto;}
		.myspan{font: 9px sans-serif; color:red;}
    </style>
    
</head>
<body>
<?php
// Check if the user is login
	session_start();
	$user_login=false;
	if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
		$user_login=true;
	}

	require_once "data_conn.php";
	
	// login variables
	$email = "";
	$pass = "";
	
	// login error variables
	$err_email = "";
	$err_pass = "";
	$err_login = "";
	 
	// Only process the data on form submission
	if($_SERVER["REQUEST_METHOD"] == "POST"){
	 
		// email validation (empty)
		if(empty(trim($_POST["email"]))){
			$err_email = "Please enter email.";
		} else{
			$email = trim($_POST["email"]);
		}
		
		// password validation (empty)
		if(empty(trim($_POST["pass"]))){
			$err_pass = "Please enter a valid password.";
		} else{
			$pass = trim($_POST["pass"]);
		}
		
		// Now grant access based on database entry
		if(empty($err_email) && empty($err_pass)){
			// Prepare a select statement
			$sql = "SELECT * FROM onehealth02h.users WHERE email = '".$email."';";
			$result = $conn->query($sql);
			if ($result->num_rows > 0) 
			{
				$row = $result->fetch_assoc();
				$hash_pass = $row["password"];
				if(password_verify($pass, $hash_pass))
				{
					// Password is correct, so start a new session
					session_start();
					
					// Store data in session variables
					$_SESSION["loggedin"] = true;
					$_SESSION["email"] = $email;
					                           
					// Redirect user to welcome page
					header("location: index.php");
				}
				else
				{
					$err_login = "Invalid username or password.";
				}
			}
			else
			{
				$err_login = "Invalid username or password.";
			}
			
		}
		

	}

	// Populate the health codes
	include_once("health_codes.php");	

?>
<div class="display-table">
	<div id='map'>
		<div class="wrapper">
			<h2>Login</h2>
			<p>Please fill in your credentials to login.</p>

			<?php 
			if(!empty($err_login)){
				echo '<div class="alert alert-danger">' . $err_login . '</div>';
			}        
			?>

			<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
				<div class="form-group">
					<label>Email</label>
					<input type="text" name="email" class="form-control <?php echo (!empty($err_email)) ? 'is not valid' : ''; ?>" value="<?php echo $email; ?>">
					<span class="myspan"><?php echo $err_email; ?></span>
				</div>    
				<div class="form-group">
					<label>Password</label>
					<input type="password" name="pass" class="form-control <?php echo (!empty($err_pass)) ? 'is not valid' : ''; ?>">
					<span class="myspan"><?php echo $err_pass; ?></span>
				</div>
				<div class="form-group">
					<input type="submit" class="btn btn-primary" value="Login">
				</div>
				<p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
				<p>Forgotten your password?<a href="forgotten_password.php"> Reset now</a>.</p>
			</form>
		</div>
    </div>

	<div class="nav">
		<form method="post" action="index.php">
		<fieldset>
		<legend>SELECT YEAR RANGE:</legend>
		<select name="start_year" id="start_year" class="select_box">
			<option value="Select Start Year" selected>Select Start Year</option>
			<option value="2019" <?php if($start_year=="2019") echo 'selected="selected"'; ?>>2019</option>
			<option value="2020" <?php if($start_year=="2020") echo 'selected="selected"'; ?>>2020</option>
			<option value="2021" <?php if($start_year=="2021") echo 'selected="selected"'; ?>>2021</option>
			<option value="2022" <?php if($start_year=="2022") echo 'selected="selected"'; ?>>2022</option>
		</select>
		
		<select name="end_year" id="end_year" class="select_box">
			<option value="Select End Year" selected>Select End Year</option>
			<option value="2019" <?php if($end_year=="2019") echo 'selected="selected"'; ?>>2019</option>
			<option value="2020" <?php if($end_year=="2020") echo 'selected="selected"'; ?>>2020</option>
			<option value="2021" <?php if($end_year=="2021") echo 'selected="selected"'; ?>>2021</option>
			<option value="2022" <?php if($end_year=="2022") echo 'selected="selected"'; ?>>2022</option>
		</select>

		</fieldset><br>
		
		<fieldset>
		<legend>SELECT POLLUTANT:</legend>
		<input type="radio" id="pollutant" name="pollutant" value="methane" <?php if($pollutant=="methane") echo 'checked="checked"'; ?>>
		<label for="pollutant">Methane (Plumes)</label><br>
		<input type="radio" id="pollutant" name="pollutant" value="uv" <?php if($pollutant=="uv") echo 'checked="checked"'; ?>>
		<label for="pollutant">Ultraviolet (UV)</label><br>
		<input type="radio" id="pollutant" name="pollutant" value="nox" <?php if($pollutant=="nox") echo 'checked="checked"'; ?>>
		<label for="pollutant">Nitrogen Oxides (NOx)</label><br>
		
		</fieldset><br>
		
		<fieldset>
		<legend>HEALTH INDICATOR:</legend>
		<select name="health_code" id="health_code" class="select_box">
			<option disabled="disabled" selected="selected">Select Health Indicator</option>
			<?php echo $select_options; ?>
		</select>
		
		</fieldset>

		<input type="submit" value="Filter" class="submit_style">
		</form>

		<?php 
			if($user_login==false)
			{ 
				include_once("explore_your_data.php");	
			} 
			else 
			{ 
				// 1. only allow user to overlay respiratory data if they have one uploaded
				$sql = "SELECT * FROM onehealth02h.daily_respiratory WHERE email = '".$_SESSION["email"]."';";
				$result = $conn->query($sql);
                $rowcount = mysqli_num_rows( $result );
                if($rowcount > 0){
                    include_once("overlay_explore.php");
                }
				
				include_once("upload_data.php");
			} 
		?>
	</div>
</div>


<?php
	$conn->close();	
?>

</body>
</html>
