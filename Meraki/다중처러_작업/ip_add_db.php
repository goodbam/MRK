<?php
    $ip = $_GET['ip'];

    $conn = new mysqli("{hostname}", "{ID}", "{passwd}", "{TableName}");
    
    $sql = "
        INSERT INTO {TableName}
        (ip)
        VALUES(
            '$ip'
            )
        ";

    $result = mysqli_query($conn,$sql);
    
    if($result === false){
        echo 'query failed';
    }else{
        echo 'query success';
    };
    
?>