<?php
    $conn = new mysqli("{hostname}", "{ID}", "{passwd}", "{TableName}");
    
    $sql ="SELECT {ColumnName} FROM {TableName}" ;

    $result = mysqli_query($conn,$sql);
    if($result === false){
        echo 'query error';
    }

    while($row = mysqli_fetch_assoc($result)){
        $ip_list[] = $row;
    }
    
    $ACL_list = array(
        'rules'=> array(
        )
    );

    foreach($ip_list as $key => $value){
        $ip_add = array(
            "comment"=>"policy",
            "policy" => "deny",
            "ipVersion"=> "ipv4",
            "protocol"=> "tcp",
            "srcCidr"=> $value['ip'],
            "srcPort"=> "any",
            "dstCidr"=> "any",
            "dstPort"=> "any",
            "vlan"=> "any"
        );
        array_push($ACL_list['rules'],$ip_add);
    }

    $data_json = json_encode($ACL_list, TRUE);

    $url ="https://api.meraki.com/api/v0/networks/L_575334852396597311/switch/accessControlLists";
    
    $header = array(
         "Content-Type:application/json",
         "Accept:application/json",
         "X-Cisco-Meraki-API-Key:{API Key}"
    );

    $ch1 = curl_init();
    curl_setopt($ch1, CURLOPT_URL, $url); 
    curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false); 
    curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch1, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch1, CURLOPT_CUSTOMREQUEST, "PUT"); 
    curl_setopt($ch1, CURLOPT_HEADER, 1);
    curl_setopt($ch1, CURLOPT_HTTPHEADER, $header); 
    curl_setopt($ch1, CURLOPT_POSTFIELDS, $data_json); 
    curl_setopt ($ch1, CURLOPT_RETURNTRANSFER, 1);
    $res=curl_exec($ch1);
    curl_close($ch1);

    date_default_timezone_set('Asia/Seoul');
    $date = date("Y_m_d",time());
    $detail_date =date("Y_m_d_h:i:s");
    $fp=fopen("ACL_log/$date.txt","a+");
    $msg ="\r\n";
    $msg.="[add {time:$detail_date}]\r\n";
    $msg.="--response massege--\r\n";
    $msg.=$res."\r\n";
    fwrite($fp,$msg);
    fclose($fp);

?>
