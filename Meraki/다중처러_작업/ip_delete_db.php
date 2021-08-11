<?php
    $ip =$_GET['ip'];

    $conn =new mysqli("{hostname}", "{ID}", "{passwd}", "{TableName}");

    $sql ="DELETE FROM {TableName} WHERE {ColumnName} = '$ip'";

    $result =mysqli_query($conn, $sql);

    if($result === false){
        echo 'query failed';
    }else{
        echo 'query success';
    }
?>