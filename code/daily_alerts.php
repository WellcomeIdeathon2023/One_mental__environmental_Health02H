<?php
    $nox_units = "parts/billion";
    $methane_units = "kg/hr";
    $uvi_units = "[0 - 12]";

    // year range is not selected, therefore produce daily alerts
    // this part is suppose to be based on daily feeds, but for demo we have used the generated data
    // uploaded to Github
    $alert_markers = "";

    // Get the min and max to be used in normalisation
    $sql = "SELECT MIN(avg_methane) AS min_methane, MAX(avg_methane) AS max_methane FROM (
            SELECT round(avg(emission)) AS avg_methane, state_code FROM onehealth02h.daily_methane group by state_code) n;";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) 
    {
        // result will be a single row no need to loop
        $row = $result->fetch_assoc();
        $min_methane = $row["min_methane"];
        $max_methane = $row["max_methane"];
    }
    
    $sql = "SELECT MIN(avg_nox) AS min_nox, MAX(avg_nox) AS max_nox FROM (
            SELECT round(avg(monthly_average)) AS avg_nox, state_code FROM onehealth02h.daily_nox group by state_code) n;";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) 
    {
        // result will be a single row no need to loop
        $row = $result->fetch_assoc();
        $min_nox = $row["min_nox"];
        $max_nox = $row["max_nox"];
    }
    
    $sql = "SELECT MIN(avg_uvi) AS min_uv, MAX(avg_uvi) AS max_uv FROM (
            SELECT round(avg(uvi)) AS avg_uvi, state_code FROM onehealth02h.daily_uvi group by state_code) u;";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) 
    {
        // result will be a single row no need to loop
        $row = $result->fetch_assoc();
        $min_uv = $row["min_uv"];
        $max_uv = $row["max_uv"];
    }

    $sql = "SELECT * FROM onehealth02h.state_codes sc
                RIGHT JOIN 
                (SELECT u.norm_uvi, u.avg_uvi, n.norm_nox, n.avg_nox, m.norm_methane, m.avg_methane, u.state_code FROM 
                (SELECT ((avg(uvi) - ".$min_uv.")/(".$max_uv." - ".$min_uv.")) AS norm_uvi, round(avg(uvi)) AS avg_uvi, state_code FROM onehealth02h.daily_uvi group by state_code) u,
                (SELECT ((avg(monthly_average) - ".$min_nox.")/(".$max_nox." - ".$min_nox.")) AS norm_nox, state_code, round(avg(monthly_average)) AS avg_nox FROM onehealth02h.daily_nox group by state_code) n,
                (SELECT ((avg(emission) - ".$min_methane.")/(".$max_methane." - ".$min_methane.")) AS norm_methane, state_code, round(avg(emission)) AS avg_methane FROM onehealth02h.daily_methane group by state_code) m
                WHERE u.state_code = n.state_code AND u.state_code = m.state_code) k
                ON sc.state_code = k.state_code;";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) 
    {
        while($row = $result->fetch_assoc()) 
        {
            // what alert? calculate the average normalised value, which will be between 0 and 1.
            $no_of_pollutants = 3.0;
            $total_norm = $row['norm_uvi'] +  $row['norm_nox'] +  $row['norm_methane'];
            //die($total_norm);
            if($total_norm > 1.5)
            {
                $lat = $row['state_lat'];
                $lon = $row['state_long'];
                $avg_uvi = $row['avg_uvi'];
                $avg_nox = $row['avg_nox'];
                $avg_methane = $row['avg_methane'];
                $alert_markers .=  "L.marker([".$lat.", ".$lon."], {icon: redIcon}).bindPopup(' UV Index: <b>".$avg_uvi."</b> ".$uvi_units."<br /> NOx: <b>".$avg_nox."</b> ".$nox_units."<br /> Methane: <b>".$avg_methane."</b> ".$methane_units."'),";
            
            }
            
        }
        //die($alert_markers );
    }


?>