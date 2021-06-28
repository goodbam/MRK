<?php
    $ip = $_GET['ip'];

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

    $check =0;
    foreach($ACL_list['rules'] as $key => $value){
        if($value['comment'] =='Default rule')unset($ACL_list['rules'][$key]);
        if($value['srcCidr'] == $ip.'/32')$check= 1;
    }

    if($check == 0){
        $ip_add =array(
            'comment'=>"policy",
            'policy' => 'deny',
            'ipVersion'=> 'ipv4',
            'protocol'=> 'tcp',
            'srcCidr'=> $ip,
            'srcPort'=> 'any',
            'dstCidr'=> 'any',
            'dstPort'=> 'any',
            'vlan'=> '120'
        );
        $ip_add['srcCidr']=str_replace('"',"",$ip_add['srcCidr']);
        array_push($ACL_list['rules'],$ip_add); 
    }
    $ACL_list['rules'] = array_values($ACL_list['rules']);

    $data_json = json_encode($ACL_list, TRUE);

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

    date_default_timezone_set('Asia/Seoul');
    $date = date("Y_m_d",time());
    $detail_date = date("Y_m_d_h:i:s");
    $fp=fopen("backup/ACL_log/$date.txt","a+");//경로
    $msg ="\r\n";
    $msg.="[add {time:$detail_date}]\r\n";
    $msg.="{ip : $ip}\r\n";
    $msg.="--response massege--\r\n";
    $msg.=$res."\r\n";
    fwrite($fp,$msg);
    fclose($fp);
    curl_close($ch1);
?>
 

    




