<?php
    //ini_set('display_errors', 1); 
    //ini_set('display_startup_errors', 1); 
    //error_reporting(E_ALL);
    if (isset($_POST["import"])) {
        // read the uploaded data from the csv file
        $fileName = $_FILES["file"]["tmp_name"];
        
        if ($_FILES["file"]["size"] > 0) {
        
            $row = 0;
            if (($handle = fopen($fileName, "r")) !== FALSE) {

                $insert_data = "";
                // Delete existing data for that user before adding new (like a temp table)
                $sql = "DELETE FROM onehealth02h.daily_respiratory WHERE email='".$_SESSION["email"]."'";
                $result = $conn->query($sql);

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $insert_sql = "INSERT INTO `onehealth02h`.`daily_respiratory` 
                    (`id`,`datetime`, `cause_of_death`, `cause_of_death_code`, `deaths`, 
                    `cause_of_death_category`, `state_code`, `state_lat`, `state_long`, `email`) 
                    VALUES (NULL, ";
                    $num = count($data);

                    if($row > 0){
                        // form the sql statement forr a row
                        for ($c=0; $c < $num; $c++) {
                            if($c == ($num - 1)){
                                $insert_sql .= "'".$data[$c]."', '".$_SESSION["email"]."');";
                            }
                            else{
                                $insert_sql .= "'".$data[$c]."', ";
                            }             
                        }

                        // add the row to the database
                        $result = $conn->query($insert_sql);
                        //echo $insert_sql;
                    }
                    $row++;
                }
                fclose($handle);

            }
        
        }

    }
?>


<hr/>
<div>
<fieldset>
	<legend>DATA UPLOAD:</legend>
	<b>Choose file to upload health data.</b>
    <form action="" method="post" name="frmCSVImport" id="frmCSVImport"
        enctype="multipart/form-data" onsubmit="return validateFile()">
        <div>
            <input type="file" name="file" id="file" accept=".csv,.xls,.xlsx">
            <div>
                <button type="submit" id="submit" name="import" class="submit_style">Upload!</button>
            </div>
        </div>
    </form>
    <br/>
    Need the CSV template? <a href="test_csv/respiratory_template.csv" download>Download now!</a>
</fieldset>
</div>
