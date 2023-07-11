<div class="nav">
	<?php
		/*if($page=="data_summary.php"){
			$target_page = "index.php";
		}
		else{
			$target_page = 
		}*/
	?>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
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