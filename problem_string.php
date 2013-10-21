<?php


require_once('mysql.php');

$query = $_GET['query'];

		$sql = "SELECT 
				codes.code_id,
				codes.code_text,
				codes.code_text_short,
				codes.code,
				codes.code_type,
				codes.modifier,
				ydp_protocol.id as protocol_id,
				ydp_protocol.name
 	 		FROM `codes` 
			LEFT JOIN codes_to_protocol on codes_to_protocol.code_id = codes.code_id 
			LEFT JOIN ydp_protocol on ydp_protocol.id = codes_to_protocol.protocol_id 
			WHERE 
				`code_text` LIKE '%$query%' 
				OR `code` LIKE '%$query%' ";


	$codes = _get_codes($sql);


		if(count($codes)<5){

			$search_array = split(' ',$query);
			$sql = "SELECT 
				codes.code_id,
				codes.code_text,
				codes.code_text_short,
				codes.code,
				codes.code_type,
				codes.modifier,
				ydp_protocol.id as protocol_id,
				ydp_protocol.name
 	 		FROM `codes` 
			LEFT JOIN codes_to_protocol on codes_to_protocol.code_id = codes.code_id 
			LEFT JOIN ydp_protocol on ydp_protocol.id = codes_to_protocol.protocol_id 
			WHERE ";
				
				$first = true;
				foreach($search_array as $term){
					if(!$first){
						$sql .= " AND ";
					}else{
						$first = false;
					}

					$sql .= " `code_text_short` LIKE '%$query%'"; 
					
				}

				//`code_text` LIKE '%$search_string%' 
				//OR `code` LIKE '%$search_string%' ";


			$codes = $codes + _get_codes($sql);
		}




$string = "<?xml version='1.0' standalone='yes'?>
<problems>";

		$free_text_option = "<problem planId='freetext_$query' name='$query' display='Create a plan without an ICD code using the text -$query-' ></problem>\n";

	if(count($codes) > 0){
		foreach($codes as $this_code){
	
				$string .= "<problem planId='".$this_code['code_id']."_".$this_code['protocol_id']."' name='$query' display='";
				$string .= $this_code['code_type'].":".$this_code['code']." - ".$this_code['protocol'];
				$string .= ": ".$this_code['code_text']."'>";
				$string .= "</problem> \n";
			}
			$string .= "  $free_text_option ";
	}else{

		$string .= "<problem planId='0' iname='$query' display='No ICD codes or descriptions match -$query-' >  </problem>\n ";			
		$string .= $free_text_option ."\n";
	}



$string .= "</problems>";

header("Content-Type: text/xml");
echo $string;

	function _get_codes($sql){

		require_once('mysql.php');

	        $query = mysql_query($sql);
		$my_codes = array();
                while($res = mysql_fetch_array($query)) {
			$code =  $res['code'];
			$code_id =  $res['code_id'];
			$code_text =  $stripped = ereg_replace("[^A-Za-z0-9 ]", "", $res['code_text']);
			$protocol =  $res['name'];
			$protocol_id =  $res['protocol_id'];
			if(strlen($protocol) == 0){
				$protocol = "Other";
				$protocol_id = 15;	
			// TODO find a non-hardcoded way to do above...
			}

                        $my_codes[$code_id] = array (
					'code' => $code,
					'code_id' => $code_id,
					'code_text' => $code_text,
					'code_type' => 'ICD9',
					'protocol' => $protocol,
					'protocol_id' => $protocol_id,
					'full_array' => $res
					);

                }
		
		return($my_codes);

	}




?>
