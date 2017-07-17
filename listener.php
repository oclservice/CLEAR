<?php
header('Access-Control-Allow-Origin: *');  
if(isset($_GET['license']) && isset($_GET['source']) && isset($_GET['college'])){

  $college = $_GET['college'];
  $licname = $_GET['license'];
  $use = $_GET['source'];
  $array = array();
  $arr = array();
  $str = array();
  $vidarray = array();
  
  $count = 0;
   if($use == "VIDEO"){

     if (strpos($licname, ',') !== false) {
       $vidarray = explode(',',$licname,-1);
     }else{
       $vidarray[0] = $licname;
     }
       
       for($n = 0; $n < count($vidarray); $n++) {
         
         if($vidarray[$n] == "Films Media Group"){

           $vidarray[$n] = "Films_on_Demand";
           $url = file_get_contents('https://clear.scholarsportal.info/centennial/json/'.$vidarray[$n]);
           $array[$x] = $url;
           $array[$x] =  json_decode($url,true);
         }
         if($vidarray[$n] == "National Film Board of Canada"){

           $vidarray[$n] = "NFB_Campus";
           $url = file_get_contents('https://clear.scholarsportal.info/centennial/json/'.$vidarray[$n]);
           $array[$x] = $url;
           $array[$x] =  json_decode($url,true);
         }
         if($vidarray[$n] == "Kanopy (Firm)"){

           $vidarray[$n] = "Kanopy_Streaming_Video";
           $url = file_get_contents('https://clear.scholarsportal.info/centennial/json/'.$vidarray[$n]);
           $array[$x] = $url;
           $array[$x] =  json_decode($url,true);
         }
       }
       
       echo $_GET['callback'] . '('.json_encode($array).')';
   }else{

      if($use == "EISBNS" || $use == "ISBNS"){
          
          $array = explode(',',$licname,2);
          /*print_r($array);*/
          $use = "ISBN";
          $licname = $array[1];
          
      }
      $reader = new XMLReader();
      if($college == "centennial"){
        $url = "http://apikey.openurl.xml.serialssolutions.com/openurlxml?version=1.0&rft.".$use."=" . $licname;
      }
      
      
      $reader->open($url);
      
      while($reader->read())
      {
          $node = $reader->expand();
          // process $node...
           
        
           foreach($node->childNodes as $item) {
              /*print_r($item);*/
             
              if($item->nodeName == "ssopenurl:databaseName" && !is_null($item->nodeValue)){
             
                $array[$count] = 'https://clear.scholarsportal.info/'.$college.'/json/?name='.urlencode($item->nodeValue);
                $new_arr = array_unique($array);
                $count++;   

                
              } 
               
           }
          
      }
      
      $reader->close();



      
      for($x = 0; $x < count($new_arr); $x++){
      

        $url = file_get_contents($new_arr[$x]);
        
        
        
        $arr[$x] = $url;
        
        $arr[$x] =  json_decode($url,true);
        
        if($arr[$x]['license-name'] == "Canadian Business &amp; Current Affairs (CBCA)"){
           $arr[$x]['license-name'] = "Canadian Business & Current Affairs (CBCA)";
           
        }
        if($arr[$x]['license-name'] == "Nursing &amp; Allied Health (ProQuest)"){
           $arr[$x]['license-name'] = "Nursing & Allied Health (ProQuest)";
           
        }
        if($arr[$x]['cms']['case'] == "Can I post a copy in a course management system?"){
            if($college == "centennial"){
              $arr[$x]['cms']['case'] = "Can I post a copy in eCentennial?";
            } 
           
        }
        
        $filtered = array_filter($arr, function($var){return !is_null($var);} );
        
             
             if($filtered[$x]['license-name'] == "Canadian Business &amp; Current Affairs (CBCA)"){
                $filtered[$x]['license-name'] = "Canadian Business & Current Affairs (CBCA)";
                
             }
             if($filtered[$x]['cms']['case'] == "Can I post a copy in a course management system?"){
                if($college == "centennial"){
                  $arr[$x]['cms']['case'] = "Can I post a copy in eCentennial?";
                }
             }
            
          
      }
      
      
       echo $_GET['callback'] . '('.json_encode($filtered).')';
       
   }

  

 
  
} 


?>
