<?php
    //==============================================================
    // 1. Methane is selected from the options menu
    // ==============================================================
    if($pollutant == "methane")
    {
        
        $metric = "kg/hr";
        $sql = "SELECT sc.state_code, name, type, geometry, population, weather_value 
                FROM onehealth02h.state_codes sc 
                RIGHT JOIN 
                (SELECT state_code, round(avg(emission)) as weather_value 
                FROM onehealth02h.daily_methane 
                GROUP BY state_code ) m 
                ON sc.state_code = m.state_code;";

        $result = $conn->query($sql);
        
        $geojsonData = '{"type":"FeatureCollection","features":[';
        // loop through to get all the states data and 
        // populate the GeoJson file with features
        if ($result->num_rows > 0) 
        {
            while($row = $result->fetch_assoc()) 
            {
                if(!is_null($row['state_code']))
                {
                    $geojsonData .= '{"type":"Feature","properties":{"name":"'.$row['name'].'",
                        "weather_value":'.round($row['weather_value']).'},
                        "geometry":{"type":"'.$row['type'].'",
                        "coordinates":'.$row['geometry'].'}},';
                }
            }
            $geojsonData .= ']};';

            //die($geojsonData);
        }
        else 
        {
            echo "Empty results";
        }

        // ========================================================
        // Generate the Deciles for Colour Coding
        // ========================================================
        // 1. Methane is selected from the options menu
        // ========================================================
        $sql = "SELECT min(avg_emissions) as min_emissions, max(avg_emissions) as max_emissions 
                FROM (SELECT state_code, round(avg(emission)) as avg_emissions 
                FROM onehealth02h.daily_methane  
                GROUP BY state_code) m;";
                
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        // generate the decile values
        $d_interval = round($row["max_emissions"]/10);
        $deciles = array();
        for($i=0;$i<10;$i++)
        {
            $deciles[] = $i*$d_interval; 
        }
        
        $color_gen = "return d > ".$deciles[9]." ? '#FF0000' :
            d > ".$deciles[8]." ? '#FF0000' :
            d > ".$deciles[7]." ? '#FF3300' :
            d > ".$deciles[6]." ? '#FF3300' :
            d > ".$deciles[5]." ? '#ff6600' :
            d > ".$deciles[4]." ? '#FC4E2A' :
            d > ".$deciles[3]." ? '#FD8D3C' :
            d > ".$deciles[2]." ? '#FEB24C' :
            d > ".$deciles[1]." ? '#FED976' : '#FFEDA0';";
    }
    
    //==============================================================
    // 2. Nitrogen Oxide (NOx) is selected from the options menu
    // ==============================================================
    if($pollutant == "nox")
    {
        $metric = "parts/billion";
        $sql = "SELECT sc.state_code, name, type, geometry, population, weather_value 
                FROM onehealth02h.state_codes sc 
                RIGHT JOIN 
                (SELECT state_code, round(avg(monthly_average)) as weather_value 
                FROM onehealth02h.daily_nox 
                GROUP BY state_code ) n 
                ON sc.state_code = n.state_code;";

        $result = $conn->query($sql);
        
        $geojsonData = '{"type":"FeatureCollection","features":[';
        // loop through to get all the states data and 
        // populate the GeoJson file with features
        if ($result->num_rows > 0) 
        {
            while($row = $result->fetch_assoc()) 
            {
                if(!is_null($row['state_code']))
                {
                    $geojsonData .= '{"type":"Feature","properties":{"name":"'.$row['name'].'",
                    "weather_value":'.round($row['weather_value']).'},
                    "geometry":{"type":"'.$row['type'].'",
                    "coordinates":'.$row['geometry'].'}},';
                }
            }
            $geojsonData .= ']};';
        }
        else 
        {
            echo "Empty results";
        }
        
        // ========================================================
        // Generate the Deciles for Colour Coding
        // ========================================================
        // 2. Nitrogen Oxide is selected from the options menu
        // ========================================================
        $sql = "SELECT min(avg_emissions) as min_emissions, max(avg_emissions) as max_emissions 
                FROM (SELECT state_code, round(avg(monthly_average)) as avg_emissions 
                FROM onehealth02h.daily_nox 
                GROUP BY state_code) n;";
                
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        // generate the decile values
        $d_interval = round($row["max_emissions"]/10);
        $deciles = array();
        for($i=0;$i<10;$i++)
        {
            $deciles[] = $i*$d_interval; 
        }
        
        
        $color_gen = "return d > ".$deciles[9]." ? '#003314' :
            d > ".$deciles[8]." ? '#00441b' :
            d > ".$deciles[7]." ? '#006d2c' :
            d > ".$deciles[6]." ? '#238b45' :
            d > ".$deciles[5]." ? '#41ab5d' :
            d > ".$deciles[4]." ? '#74c476' :
            d > ".$deciles[3]." ? '#a1d99b' :
            d > ".$deciles[2]." ? '#c7e9c0' :
            d > ".$deciles[1]." ? '#e5f5e0' : '#f7fcf5';";
        
    }
    
    //==============================================================
    // 3. UV INDEX (UVI) is selected from the options menu
    // ==============================================================
    if($pollutant == "uv")
    {
        $metric = "scale 1 - 12+";
        $sql = "SELECT sc.state_code, name, type, geometry, population, weather_value 
                FROM onehealth02h.state_codes sc 
                RIGHT JOIN 
                (SELECT state_code, round(avg(uvi)) as weather_value 
                FROM onehealth02h.daily_uvi 
                GROUP BY state_code ) u 
                ON sc.state_code = u.state_code;";

        $result = $conn->query($sql);
        
        $geojsonData = '{"type":"FeatureCollection","features":[';
        // loop through to get all the states data and 
        // populate the GeoJson file with features
        if ($result->num_rows > 0) 
        {
            while($row = $result->fetch_assoc()) 
            {
                if(!is_null($row['state_code']))
                {
                    $geojsonData .= '{"type":"Feature","properties":{"name":"'.$row['name'].'",
                    "weather_value":'.round($row['weather_value']).'},
                    "geometry":{"type":"'.$row['type'].'",
                    "coordinates":'.$row['geometry'].'}},';
                }
            }
            $geojsonData .= ']};';
        }
        else 
        {
            echo "Empty results";
        }
        
        
        // ========================================================
        // Generate the Sacle for Colour Coding
        // ========================================================
        // 3. UV is selected from the options menu
        // ========================================================
        $sql = "SELECT min(max_uvi) as min_emissions, max(max_uvi) as max_uv 
                FROM (SELECT state_code, round(max(uvi)) as max_uvi
                FROM onehealth02h.daily_uvi 
                GROUP BY state_code) n;";
                
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        // generate the decile values
        $deciles = array();
        for($i=0;$i<12;$i++)
        {
            $deciles[] = $i; 
        }
        
        $color_gen = "return d > ".$deciles[11]." ? '#7F00FF' :
            d > ".$deciles[10]." ? '#7F00FF' :
            d > ".$deciles[9]." ? '#FF0000' :
            d > ".$deciles[8]." ? '#FF0000' :
            d > ".$deciles[7]." ? '#FF0000' :
            d > ".$deciles[6]." ? '#FFA500' :
            d > ".$deciles[5]." ? '#FFA500' :
            d > ".$deciles[4]." ? '#FFFF00' :
            d > ".$deciles[3]." ? '#FFFF00' :
            d > ".$deciles[2]." ? '#FFFF00' :
            d > ".$deciles[1]." ? '#FFFF00' : '#00FF00';";
    
        
    }
    
?>