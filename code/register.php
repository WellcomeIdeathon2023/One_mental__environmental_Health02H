<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>One-Health-02H</title>
	<link rel="shortcut icon" type="image/x-icon" href="images/uol.ico" />
    <link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/app.css"/>
  
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
	
    // The registration variables
    $email = "";
    $re_pass = "";
    $pass = "";

    // The error variables.
    $err_email = "";
    $err_re_pass = "";
    $err_pass = "";

    // Only process the data on form submission
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $email_pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i";
        // email validation
        if(empty(trim($_POST["email"]))){
            $err_email = "Email cannot be empty!";
        } elseif(!preg_match($email_pattern, trim($_POST["email"]))){
            // Check the email is in a valid format
            $err_email = "This is not a valid email address!";
        } else{
            // check email already exists in the database of users
            $sql = 'SELECT * FROM onehealth02h.users WHERE email = "'.trim($_POST["email"]).'";';
            $result = $conn->query($sql);
            if ($result->num_rows > 0) 
            {
                $err_email = "User with this email already exists!";
            }
            else
            {
                $email = trim($_POST["email"]);	
            }
            
        }
        
        // password validation
        if(empty(trim($_POST["pass"]))){
            // check to see if a password was entered
            $err_pass = "Password cannot be empty!";     
        } elseif(strlen(trim($_POST["pass"])) < 8){
            // check the strength of the entered password
            $err_pass = "Password must have atleast 8 characters.";
        } else{
            $pass = trim($_POST["pass"]);
        }
        
        // re-entered password validation
        if(empty(trim($_POST["re_pass"]))){
            $err_re_pass = "Please re-enter the password.";     
        } else{
            $re_pass = trim($_POST["re_pass"]);
            if(empty($err_pass) && ($pass != $re_pass)){
                $err_re_pass = "Password did not match the re-entered.";
            }
        }
        
        // We have to insert the data into database only if no errors
        if(empty($err_email) && empty($err_pass) && empty($err_re_pass)){
            $token = base64_encode($email); 
            $datetime = date("Y-m-d H:i:s");
            $hash_password = password_hash($pass, PASSWORD_DEFAULT);
            // The insert SQL statement
            $sql = "INSERT INTO `onehealth02h`.`users` (`email`, `password`, `reset_token`, `created_at`) VALUES ('".$email."', '".$hash_password."', '".$token."', '".$datetime."');";
            $result = $conn->query($sql);
            if ($result) {
                // send a email to the user with token for activation
                $host_url = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']);
                $host_url .= "/activate.php?token=".$token;
                $email_message = '<p>Welcome to the One-Health-02H site. <br>
                You are recieving this email because you signed up to use ours services. <br>
                In order to use the services fully, please activate you account now using the link below:<br>
                <a href="'.$host_url.'">'.host_url.'</a></p>';
                $subject = 'One-Health-02H Activation';
                $headers = 'From: jatanbori@yahoo.com' . "\r\n" .
                    'Reply-To: no-reply@yahoo.com' . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();
                mail($email, $subject, $email_message, $headers);

            // redirect to the login page with message
            $message = "Check email to activate account and login!";
            header("Location: login.php?m=".$message);
            exit();
            }
            
        }
        
    }


	// Populate the health codes
	include_once("health_codes.php");	

?>
<div class="display-table">
	<div id='map'>
    <div class="wrapper">
        <h2>Sign Up</h2>
        <p>Please fill this form to create an account.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label>Email</label>
                <input type="text" name="email" class="form-control <?php echo (!empty($err_email)) ? 'is not valid' : ''; ?>" value="<?php echo $email; ?>">
                <span class="invalid-feedback"><?php echo $err_email; ?></span>
            </div>    
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="pass" class="form-control <?php echo (!empty($err_pass)) ? 'is not valid' : ''; ?>" value="<?php echo $pass; ?>">
                <span class="invalid-feedback"><?php echo $err_pass; ?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="re_pass" class="form-control <?php echo (!empty($err_re_pass)) ? 'is not valid' : ''; ?>" value="<?php echo $re_pass; ?>">
                <span class="invalid-feedback"><?php echo $err_re_pass; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <input type="reset" class="btn btn-secondary ml-2" value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
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
