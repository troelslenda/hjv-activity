<?php 


function soldat_preprocess_node(&$variables) {

  $node = node_load($variables['nid']);
  
  $signups = hjv_users_get_signups_for_activity($node);
  $variables['members'] = theme('hjv_member_matrix',$signups['all'],$signups['attendees'],$signups['absentees']);
  
  $signuptext = t('Tilmeld dig via hjv.dk');
  if(is_array($signups['absentees'])){
    $signuptext = t('Afmeld aktiviteten via hjv.dk');
    foreach($signups['absentees'] as $absentee){
      if($absentee['uid'] == $user->uid){
        $signuptext = t('Tilmeld aktiviteten via hjv.dk');
      }
    }
  }
  
   // if activity didnt pass and you're not signed up then provide this link
  if($variables['field_guid'][0]['value'] && (date_convert($variables['field_duration'][0]['value'], DATE_ISO, DATE_UNIX) > time())){
    $variables['signuplink'] = l($signuptext,'http://specmod.hjv.dk/hjv/activities/ActivityDetails.aspx?GUID='.$variables['field_guid'][0]['value']); 
  }
  
  
  #$tzdiff = 200000000;
  $res = db_query('SELECT * FROM {hjv_auth_scrape_queue} WHERE aid = %d',$variables['nid']);
  while($row = db_fetch_array($res)){
    $time = ($row['updatetime']-(variable_get('hjv_'.$row['type'].'_update_frequency','')*60*60));
    $variables['lastupdate_'.$row['type']] = format_interval(time()-$time,1);
  }
  
  if ((preg_match('/[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}/i', $variables['field_guid'][0]['value'],$results))) {
    $variables['updatelink'] = l(t('Opdater deltagere'),'node/'.$variables['nid'].'/reset_activity'); 
  }
  
  
  
   
   
}


function soldat_username($object) {

  
  #TODO: is it wise to read the whole user object? And why isent it loaded in the first place?
  $object = user_load($object->uid);
  
  if ($object->uid && $object->name) {
    // Shorten the name when it is too long or it will break many tables.
    if (drupal_strlen($object->name) > 20) {
      $name = drupal_substr($object->name, 0, 15) .'...';
    }
    else {
      if(!($name = $object->profile_callsign)){
        if($object->profile_fullname){
          $parts = explode(' ',$object->profile_fullname);
          $firstname = $parts[0];
          $parts = array_reverse($parts);
          $name = $firstname[0].'. '.$parts[0];
        }
        else{
          $name = $object->name;
        }  
      }
    }

    if (user_access('access user profiles')) {
      $output = l($name, 'user/'. $object->uid, array('attributes' => array('title' => t('View user profile.'))));
    }
    else {
      $output = check_plain($name);
    }
  }
  else if ($object->name) {
    // Sometimes modules display content composed by people who are
    // not registered members of the site (e.g. mailing list or news
    // aggregator modules). This clause enables modules to display
    // the true author of the content.
    if (!empty($object->homepage)) {
      $output = l($object->name, $object->homepage, array('attributes' => array('rel' => 'nofollow')));
    }
    else {
      $output = check_plain($object->name);
    }

    $output .= ' ('. t('not verified') .')';
  }
  else {
    $output = check_plain(variable_get('anonymous', t('Anonymous')));
  }

  return $output;
}



?>