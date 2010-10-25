<?php 
class HJVformatMISC{
  
  static public function formatParticipantData($raw){
    $str = substr($raw, strpos($raw,'<tbody>')+9);
    $str = substr($str,0, strpos($str,'</tbody>'));
    $xml = simplexml_load_string('<table>'.$str.'</table>');
    foreach ($xml->tr as $row){
      $member['ma_number'] = trim($row->td[1]);
      $member['name'] = trim($row->td[2]->a);
      list($ignore,$phone) = explode(':',trim($row->td[2]->a['title']));
      $member['phone'] = trim($phone);
      list($ignore,$email) = explode(':',trim(trim($row->td[2]->a['href'])));
      $member['mail'] = trim($email);
      $member['function'] = trim($row->td[3]);
      $member['unit'] = trim($row->td[4]);
      $member['updatetime'] = trim($row->td[5]);
      $member['signedup'] = trim($row->td[6]->table->tr->td[1]->span);
      $participantData[] = $member;
    }
    return $participantData; 
  }
  static public function formatActivityData($raw){
    $str = substr($raw, strpos($raw,'activityD')-46);
    $str = substr($str,0, strpos($str,'</table>'));
    
    # close table 
    $str = $str.'</table>';
    
    #remove entries that breaks validation
    $str = str_replace(array('&nbsp;','<br>','<br />',';'),array('','','',''),$str);
    
    
    $xml = simplexml_load_string($str);
    
    #var_dump(htmlentities($raw));
    
    $title = substr($raw,strpos($raw,'class="PageHeader"')+19);
    $line['title'] = substr($title,0,strpos($title,'</span>'));
    
    $line['time'] = trim($xml->tr[1]->td[1]->span);
    $line['signupbefore'] = trim($xml->tr[2]->td[1]->span);
    $line['location'] = trim($xml->tr[3]->td[1]->span);
    $line['description'] = trim($xml->tr[6]->td[1]->span);
    $line['notes'] = trim($xml->tr[8]->td[1]->span);
    $line['duration'] = trim($xml->tr[11]->td[1]->span);
    
    return $line;
  }
  static public function getStartAndEndTime($str){
    
    $parts = explode('-',trim($str));
    $start = explode(' ',trim($parts[0]));
    $end = explode(' ',trim($parts[1]));
    
    $months = array('','JAN','FEB','MAR','APR','MAJ','JUN','JUL','AUG','SEP','OKT','NOV','DEC');
    $months = array_flip($months);
    
    if(sizeof($end) == 1){
       $end = mktime(substr($end[0],0,2),substr($end[0],2,4),0, $months[$start[1]], $start[0],$start[2]);
    }
    else{
      $end = mktime(substr($end[3],0,2),substr($end[3],2,4),0, $months[$end[1]], $end[0],$end[2]);
    }
    $start = mktime(substr($start[3],0,2),substr($start[3],2,4),0, $months[$start[1]], $start[0],$start[2]);
    
    return array($start,$end);
  }
  static public function load_HJV_user($ma){
    return user_load(array('profile_manumber' => $ma));
    
  }
  
}


?>