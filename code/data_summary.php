<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>One-Health-02H</title>
	<link rel="shortcut icon" type="image/x-icon" href="images/uol.ico" />
	<link rel="stylesheet" href="css/app.css"/>

    <style>
        #health_overlay {
            font-family: Arial, Helvetica, sans-serif;
            font-size:11px;
            border-collapse: collapse;
            width: 90%;
            margin-left:5%;
        }

        #health_overlay td, #health_overlay th {
            border: 1px solid #ddd;
            padding: 4px;
        }

        #health_overlay tr:nth-child(even){background-color: #f2f2f2;}

        #health_overlay tr:hover {background-color: #ddd;}

        #health_overlay th {
            padding-top: 5px;
            padding-bottom: 5px;
            text-align: left;
            background-color: #04AA6D;
            color: white;
        }
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
	
	// Populate the health codes
	include_once("health_codes.php");	

?>
<div class="display-table">
	<div id='map'>
        <br />
        <form method="post" action="index.php">
            <table id="health_overlay">
                <tr>
                    <td>
                        <input type="radio" id="pollutant" name="pollutant" value="methane" checked="checked">
                        <label for="pollutant">Methane (Plumes)</label>
                    </td>
                    <td>
                        <input type="radio" id="pollutant" name="pollutant" value="uv">
                        <label for="pollutant">Ultraviolet (UV)</label>
                    </td>
                    <td>
                        <input type="radio" id="pollutant" name="pollutant" value="nox">
                        <label for="pollutant">Nitrogen Oxides (NOx)</label><br>
                    </td>
                    <td>
                        <select name="health_code" id="health_code" class="select_box">
                            <option disabled="disabled" selected="selected">Select Health Overlay</option>
                            <?php echo $select_options; ?>
                        </select>
                    </td>
                    <td>
                        <input type="submit" value="Overlay Data" class="submit_style">
                    </td>
                </tr>
            </table>
        </form>
        <br />

        <table id="health_overlay">
        <tr>
            <th>ID</th>
            <th>DateTime</th>
            <th>CoD</th>
            <th>CoD Code</th>
            <th>Deaths</th>
            <th>CoD Category</th>
            <th>State Code</th>
            <th>Latitude</th>
            <th>Longitude</th>
        </tr>

        <?php
            // 1. Select the users uploaded data
            $sql = "SELECT * FROM onehealth02h.daily_respiratory WHERE email = '".$_SESSION["email"]."';";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) 
            {
                while($row = $result->fetch_assoc()) 
                {
        ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['DateTime']; ?></td>
                        <td><?php echo $row['cause_of_death']; ?></td>
                        <td><?php echo $row['cause_of_death_code']; ?></td>
                        <td><?php echo $row['deaths']; ?></td>
                        <td><?php echo $row['cause_of_death_category']; ?></td>
                        <td><?php echo $row['state_code']; ?></td>
                        <td><?php echo $row['state_lat']; ?></td>
                        <td><?php echo $row['state_long']; ?></td>
                    </tr>
        <?php
                }
            }
        ?>
                
        </table>
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
