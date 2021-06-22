<?php
    $ip=$_GET["ip"];
    
    $url ='https://api.meraki.com/api/v0/networks/L_575334852396597311/switch/accessControlLists';
    
    $header = array(
        "Content-Type:application/json",
        "Accept:application/json",
        "X-Cisco-Meraki-API-Key:6bec40cf957de430a6f1f2baa056b99a4fac9ea0"
    );
    
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
    $res = curl_exec ($ch);
    curl_close($ch);
    
    $ACL_list = json_decode($res,TRUE);

    foreach ( $ACL_list['rules'] as $key=>$value ) {
        if ( $value['policy']=='allow' || $value['srcCidr']=="$ip/32" )unset($ACL_list['rules'][$key]);
    }

    $ACL_list['rules'] = array_values($ACL_list['rules']);
    
    $data_json = json_encode($ACL_list,JSON_UNESCAPED_SLASHES);
    
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

?>