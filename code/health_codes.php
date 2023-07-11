<?php
    //==================================
	// Use to populate the health codes
	//==================================
	$select_options = "";
	
	// 1. Respiratory codes
	$sql = "SELECT DISTINCT cause_of_death_category
			FROM onehealth02h.respiratory 
			WHERE cause_of_death_category IS NOT NULL
			ORDER BY cause_of_death_category;";

	$result = $conn->query($sql);
	if ($result->num_rows > 0) 
	{
		$select_options = '<optgroup label="Respiratory">';
		while($row = $result->fetch_assoc()) 
		{
			
			if($health_option=='Res+'.$row["cause_of_death_category"])
			{
				$select_options .= '<option value="Res+'.$row["cause_of_death_category"].'" class="res" selected="selected">'.$row["cause_of_death_category"].'</option>';
			}
			else
			{
				$select_options .= '<option value="Res+'.$row["cause_of_death_category"].'" class="res" >'.$row["cause_of_death_category"].'</option>';
			}
			
		}
		$select_options .= "</optgroup>";
    }
	
	// 2. Mental Health codes
	$sql = "SELECT DISTINCT category, category_code 
			FROM onehealth02h.mental_health 
			ORDER BY category_code;";

	$result = $conn->query($sql);
	if ($result->num_rows > 0) 
	{
		$select_options .= '<optgroup label="Mental Health">';
		while($row = $result->fetch_assoc()) 
		{
			
			if($health_option=='Mental+'.$row["category"])
			{
				$select_options .= '<option value="Mental+'.$row["category"].'" selected="selected">'.$row["category"].'</option>';
			}
			else
			{
				$select_options .= '<option value="Mental+'.$row["category"].'" >'.$row["category"].'</option>';
			}
			
		}
		$select_options .= "</optgroup>";
    }
?>