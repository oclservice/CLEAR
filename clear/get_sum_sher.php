<?php
#THIS SCRIPT IS USED TO QUERY BOTH SERIAL SOLUTIONS AND SCHOLAR'S PORTAL. IT USED FOR THE SHERIDAN INSTANCE

#to test copy to the browser
#https://www.ocls.ca/virtualhelp/clear/get_sum_sher.php?college=sheridan&license=0191640107,9780191640100,9780199663217,0199663211&source=ISBNS
#https://www.ocls.ca/virtualhelp/clear//get_sum_sher.php?college=sheridan&license=9781400838592,1400838592,0691070911,9780691070919,0691157871,9780691157870&source=ISBNS
#https://www.ocls.ca/virtualhelp/clear/get_sum_sher.php?college=sheridan&license=9780231509435,023150943X&source=ISBNS
#or open Sheridan college library (http://sheridan.summon.serialssolutions.com/), search dogs, inspect elemens, see console messages

header('Content-Type: application/javascript','Access-Control-Allow-Origin: *');  
if(isset($_GET['license']) && isset($_GET['source']) && isset($_GET['college'])){

  $college = $_GET['college'];
  $licname = $_GET['license'];
  $use = $_GET['source'];
  $array = array();
  $arr = array();
  $str = array();
  $vidarray = array();
  $lic_array = array();
  $new_arr = array();
  $filtered = array();
  
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
           $url = file_get_contents('https://clear.scholarsportal.info/sheridan/json/'.$vidarray[$n]);
           $array[$x] = $url;
           $array[$x] =  json_decode($url,true);
         }
         if($vidarray[$n] == "National Film Board of Canada"){

           $vidarray[$n] = "NFB_Campus";
           $url = file_get_contents('https://clear.scholarsportal.info/sheridan/json/'.$vidarray[$n]);
           $array[$x] = $url;
           $array[$x] =  json_decode($url,true);
         }
         if($vidarray[$n] == "Kanopy (Firm)"){

           $vidarray[$n] = "Kanopy_Streaming_Video";
           $url = file_get_contents('https://clear.scholarsportal.info/sheridan/json/'.$vidarray[$n]);
           $array[$x] = $url;
           $array[$x] =  json_decode($url,true);
         }
       }
       
       #echo $_GET['callback'] . '('.json_encode($array).')';
	   if ( count($array)>0)
	   {
			echo isset($_GET['callback']) ? $_GET['callback'] . '('.json_encode($array).')' : '?' . '('.json_encode($array).')';
	   }
	   else
	   {
			echo isset($_GET['callback']) ? $_GET['callback'] . '([])' : '?' . '([])';
	   }
   }else{

      //WRAPPED IN A FOR LOOP FOR API CALLS
      if($use == "EISBNS" || $use == "ISBNS"){
          
          if(strpos($licname, ",") !== false ){
            $lic_array = explode(',',$licname,-1);
          }else{
            $lic_array[0] = $licname;
          }
          
          $use = "ISBN";
          
      }else{
        $lic_array[0] = $licname;
      }
      
      for($j = 0; $j < count($lic_array); $j++){

        $reader = new XMLReader();
        
        #THIS IS THE 360 Link XML API TO GET DATABASE NAME
        $url = "http://qg3yr4sv8z.openurl.xml.serialssolutions.com/openurlxml?version=1.0&rft.".$use."=" . $lic_array[$j];
        //echo $url;
        $reader->open($url);
        
        
        while($reader->read())
        {

            $node = $reader->expand();
			#echo "Main Node name: ".$node->nodeName." Node value: ".$node->nodeValue."\n";
            // process $node...             

             foreach($node->childNodes as $item) {
                
                
                if($item->nodeName == "ssopenurl:databaseName" && !is_null($item->nodeValue)){

                  if($item->nodeValue == "CPI.Q."){
                    $item->nodeValue = str_replace('.', '', $item->nodeValue);
                  }

                  if($item->nodeValue == "Wiley_Online_Library_Full_Collection_2016"){
                    $item->nodeValue = "WileyOnlineLibrary";
                  }
                  #THIS IS THE QUERY TO SCHOLAR"S PORTAL
                  //print_r($item->nodeValue);
                  $array[$count] = 'https://clear.scholarsportal.info/sheridan/json/?name='.urlencode($item->nodeValue);
				  #echo "HURRAY*************************************************************\n";
				  #echo "Node name: ".$item->nodeName." Node value: ".$item->nodeValue."\n";
				  #echo "\t\tArray".$count.": ".$array[$count]."\n";
                  $new_arr = array_unique($array);
                  $count++;                    
                } 
             }
        }
        $reader->close();
      }
      
 
      for($x = 0; $x < count($new_arr); $x++){
        /*print_r($new_arr[$x]);*/

        $url = file_get_contents($new_arr[$x]);
        
        #echo "*******************************************************************\n*";
		#print_r($new_arr[$x]);
		#echo "*\n$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$\n";
        
        $arr[$x] = $url;
        
        $arr[$x] =  json_decode($url,true);
        
        if($arr[$x]['license-name'] == "Canadian Business &amp; Current Affairs (CBCA)"){
           $arr[$x]['license-name'] = "Canadian Business & Current Affairs (CBCA)";
           
        }
        if($arr[$x]['license-name'] == "Nursing &amp; Allied Health (ProQuest)"){
           $arr[$x]['license-name'] = "Nursing & Allied Health (ProQuest)";
           
        }
        if($arr[$x]['e_reserves']['case'] == "Can I make an electronic copy for use in an e-reserve system?"){
            
            $arr[$x]['e_reserves']['case'] = "Can I use a copy in an e-reserve (eCOR) system?";
           
        }
        if($arr[$x]['cms']['case'] == "Can I post a copy in a course management system?"){
            
            $arr[$x]['cms']['case'] = "Can I post a copy in SLATE?";
           
        }
        if($arr[$x]['print']['case'] == "Can I make print or electronic copies?"){
            
            $arr[$x]['print']['case'] = "Can I make print or electronic copies for personal use?";
           
        }
        
        
        $filtered = array_filter($arr, function($var){return !is_null($var);} );

      }
       
	  if ( count($filtered)>0)
	  {
	   #RETURN THE JSON TO THE JAVASCRIPT SCRIPT
       #echo $_GET['callback'] . '('.json_encode($filtered).')';
	   echo isset($_GET['callback']) ? $_GET['callback'] . '('.json_encode($filtered).')' : '?' . '('.json_encode($filtered).')';
	  }
	  else
	  {
		echo isset($_GET['callback']) ? $_GET['callback'] . '([])' : '?' . '([])';
	  }
       
   }

  
} 


?>