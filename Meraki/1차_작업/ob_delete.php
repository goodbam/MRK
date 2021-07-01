<?php
    $ip_info=$_GET["ip"];
    
    $url_info='https://api.meraki.com/api/v0/networks/L_575334852396597311/switch/accessControlLists';
    
    $header_info=array(
        "Content-Type:application/json",
        "Accept:application/json",
        "X-Cisco-Meraki-API-Key:6bec40cf957de430a6f1f2baa056b99a4fac9ea0"
    );
    
    function API_GET($url,$header){
        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
        $res = curl_exec ($ch);        
        $res_array = json_decode($res,TRUE);
        curl_close($ch);
        return $res_array;
    }

    function API_PUT($url,$header,$data_json){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); 
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json); 
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        $res=curl_exec($ch);
        curl_close($ch);
        return $res;
    }

    function processing($data, $ip){
        foreach ($data['rules'] as $key=>$value ) {
            if ( $value['comment']=='Default rule' || $value['srcCidr']=="$ip/32" )unset($data['rules'][$key]);
        }
        $data['rules']=array_values($data['rules']);   
        $data_json=json_encode($data,JSON_UNESCAPED_SLASHES);
        return $data_json;
    }

    function log_create($ip,$data){
        date_default_timezone_set('Asia/Seoul');
        $date=date("Y_m_d",time());
        $detail_date=date("Y_m_d_h:i:s");
        $fp=fopen("backup/ACL_log/$date.txt","a+");//경로
        $msg ="\r\n";
        $msg.="[delete {time:$detail_date}]\r\n";
        $msg.="{ip : $ip}\r\n";
        $msg.="--response massege--\r\n";
        $msg.=$data."\r\n";
        fwrite($fp,$msg);
        fclose($fp);
    }

    $ACL_list=API_GET($url_info,$header_info);
    $data=processing($ACL_list,$ip_info);
    $result=API_PUT($url_info,$header_info,$data);
    log_create($ip_info,$result);
    
    
    
    
    

?>