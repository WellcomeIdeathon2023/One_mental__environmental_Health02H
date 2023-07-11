<?php    
    //========================================================
    // Health Markers
    //==========================================================
    $health_markers = "";
    /* add variability to the longitude and latitude values so 
    they are all not plotted on the centre co-ordinate of the state*/
    function varyCoordinates(float $co_ordinate) 
    {
        return $co_ordinate + (rand(0, 10) / 1000);
    }
    

    $opt = explode("+", $health_option);
    //==================================
    // Respiratory data (blue markers)
    //==================================
    if($opt[0] == "Res")
    {	
        // Respiratory data (blue markers)
        $sql = "SELECT sc.state_code, state_lat, state_long, deaths, `year`, cause_of_death_code, cause_of_death
                FROM onehealth02h.state_codes sc 
                RIGHT JOIN 
                (SELECT state_code, deaths, cause_of_death_code, population, `year`, cause_of_death
                FROM onehealth02h.respiratory 
                WHERE cause_of_death_category = '".$opt[1]."' AND `year` BETWEEN '".$start_year."' AND '".$end_year."') r
                ON sc.state_code = r.state_code;";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) 
        {
            while($row = $result->fetch_assoc()) 
            {
                
                for($i=0;$i<$row['deaths'];$i++)
                {
                    //$lat = $row['state_lat'];
                    //$lon = $row['state_long'];
                    $lat = varyCoordinates($row['state_lat']);
                    $lon = varyCoordinates($row['state_long']);
                    $health_markers .= "markers.addLayer(L.marker([".$lat.", ".$lon."],{icon: blueIcon}));";
                }
            }
        }
        else 
        {
            echo "Empty results";
        }
    }
    
    //==================================
    // Respiratory data (blue markers)
    //==================================
    if($opt[0] == "Mental")
    {	
        // Respiratory data (blue markers)
        // Cases per 100 people
        $sql = "SELECT *
                FROM onehealth02h.state_codes sc 
                LEFT JOIN 
                (SELECT state_code, round(cases*100) as m_cases, category, category_code, `year`
                FROM onehealth02h.mental_health 
                WHERE category = '".$opt[1]."' AND `year` BETWEEN '".$start_year."' AND '".$end_year."') m
                ON sc.state_code = m.state_code;";

        $result = $conn->query($sql);
        if ($result->num_rows > 0) 
        {
            while($row = $result->fetch_assoc()) 
            {
                
                for($i=0;$i<$row['m_cases'];$i++)
                {
                    $lat = varyCoordinates($row['state_lat']);
                    $lon = varyCoordinates($row['state_long']);
                    $health_markers .= "markers.addLayer(L.marker([".$lat.", ".$lon."],{icon: blueIcon}));";
                }
            }
        }
        else 
        {
            echo "Empty results";
        }
    }
	
?>