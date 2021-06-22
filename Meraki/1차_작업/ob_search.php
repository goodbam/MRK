<?php
   $ip=$_GET["ip"];

   $url ="https://api.meraki.com/api/v0/networks/L_575334852396597311/switch/accessControlLists";
    
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
    
   $ACL_list = json_decode($res,TRUE);
   
   for( $i = 0 ; $i < count($ACL_list["rules"]); $i++){      
      if($ACL_list["rules"][$i]["srcCidr"]=="$ip/32"){
         $policy = $ACL_list["rules"][$i]["policy"];
         break;
      }
   };
   
   if(isset($policy)){
      echo "OK";
   }else{
      echo "Null";
   }

?>
