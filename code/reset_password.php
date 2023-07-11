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
	
    // "data_conn.php" is needed to connect to the database
    require_once "data_conn.php";
        
    // the form variables
    $new_pass = "";
    $re_new_pass = "";

    // The error variables
    $err_new_pass = "";
    $err_re_new_pass = "";
    $err_token = "";
    $raw_token = "";

    if(isset($_GET["token"]))
    {
        $raw_token = $_GET["token"];
    }
    
    // Only process the data on form submission
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $raw_token = $_POST["token"];
        
        // Validate new password
        if(empty(trim($_POST["new_pass"]))){
            $err_new_pass = "The password cannot be empty.";     
        } elseif(strlen(trim($_POST["new_pass"])) < 8){
            $err_new_pass = "Password must have atleast 8 characters.";
        } else{
            $new_pass = trim($_POST["new_pass"]);
        }
        
        // Validate confirm password
        if(empty(trim($_POST["re_new_pass"]))){
            $err_re_new_pass = "Please confirm the password.";
        } else{
            $re_new_pass = trim($_POST["re_new_pass"]);
            if(empty($err_new_pass) && ($new_pass != $re_new_pass)){
                $err_re_new_pass = "Password did not match.";
            }
        }
        
        // Validate the reset token
        $sql = 'SELECT * FROM onehealth02h.users WHERE email = "'.base64_decode($raw_token).'";';
        $result = $conn->query($sql);
        if ($result->num_rows <= 0) 
        {
            $err_token = "The reset token has expired!";
        }
            
        // Check input errors before updating the database
        if(empty($err_new_pass) && empty($err_re_new_pass) && empty($err_token)){
            // Prepare an update statement
            $hash_password = password_hash($new_pass, PASSWORD_DEFAULT);
            $sql = "UPDATE `onehealth02h`.`users` SET `password` = '".$hash_password."' WHERE (`email` = '".base64_decode($raw_token)."');";
            $result = $conn->query($sql);
            if ($result) 
            {
                // redirect to login page
                header("Location: login.php?");
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
        <h2>Reset Password</h2>
        <p>Please fill out this form to reset your password.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"> 
            <div class="form-group">
                <label>New Password</label>
				<input type="hidden" id="token" name="token" value="<?php echo $raw_token; ?>">
                <input type="password" name="new_pass" class="form-control <?php echo (!empty($err_new_pass)) ? 'is not valid' : ''; ?>" value="<?php echo $new_pass; ?>">
                <span class="myspan"><?php echo $err_new_pass; ?></span>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="re_new_pass" class="form-control <?php echo (!empty($err_re_new_pass)) ? 'is not valid' : ''; ?>">
                <span class="myspan"><?php echo $err_re_new_pass; ?></span>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a class="btn btn-link ml-2" href="index.php">Cancel</a>
            </div>
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
