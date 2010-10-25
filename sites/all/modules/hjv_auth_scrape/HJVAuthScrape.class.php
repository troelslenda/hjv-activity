<?php 

class HJVAuthScrape{
  
  private $guid;
  private $loginattemptRAWHTML;
  private $username;
  private $password;
  public $curl;
  
  public function __construct($guid){
    $this->guid = $guid;
  }
  public function authorize($username,$password){
    $curl = $this->initCURL($this->getURL('auth'));

    $this->loginattemptRAWHTML = curl_exec($curl);
    $this->username = $username;
    $this->password = $password;
    
    $this->fillPostFields($curl);
    curl_exec($curl);
    $this->curl = $curl;    
  }
  
  /**
   * Returns a url to be used for fetching data with cURL
   */
  private function getURL($type){
    $guid = $this->guid;
    switch ($type){
      case 'auth':
        return 'http://www.hjv.dk/_layouts/login.aspx?ReturnUrl=' . urlencode('/faelles/aktiviteter/sider/oversigt.aspx?IFrameURL=%5Chjv%5Cactivities%5CActivityDetails.aspx%3fguid%3d'.$guid.'&IFrameURL=\hjv\activities\ActivityDetails.aspx?guid='.$guid). $guid;
        break;
      case 'activity':
        return 'http://specmod.hjv.dk/hjv/activities/ActivityDetails.aspx?GUID='.$guid;
        break;
      case 'participants':
        return 'http://specmod.hjv.dk/hjv/activities/ActivityParticipantsList.aspx?GUID='.$guid;
        break;
    }
  }
  private function initCURL($url){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_FAILONERROR, 1);
    curl_setopt($curl, CURLOPT_COOKIEJAR, "cookie.txt");
    curl_setopt($curl, CURLOPT_COOKIEFILE, "cookie.txt");
    curl_setopt($curl, CURLOPT_COOKIESESSION, true);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION,  true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($curl, CURLOPT_VERBOSE, 1);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
    curl_setopt($curl, CURLOPT_HTTPHEADER,array("Content-type: application/x-www-form-urlencoded"));
    curl_setopt($curl, CURLOPT_POSTFIELDS, '');
    return $curl;
  }
  private function fillPostFields(&$curl){
    $args = array(
    	'ctl00$PlaceHolderMain$login$UserName' => trim($this->username),
    	'ctl00$PlaceHolderMain$login$password' => trim($this->password),
    	'__LASTFOCUS' => null,
    	'__VIEWSTATE' => $this->getViewstate($this->loginattemptRAWHTML),
    	'__EVENTTARGET' => '',
    	'__EVENTARGUMENT' => '',
    	'__EVENTVALIDATION' => $this->getEventvalidation($this->loginattemptRAWHTML),	
    	'ctl00$PlaceHolderMain$login$login' => 'Log på',
    	'ctl00$PlaceHolderMain$login$RememberMe' => '',
    	'__spDummyText1' => null,
    	'__spDummyText2' => null	
    );
    foreach ($args as $key => $value){
    	$it[] = $key.'='.$value;
    }

    #do the login
    curl_setopt($curl, CURLOPT_POSTFIELDS, implode('&',$it));
  }
  private function getViewstate($rawhtml){
    $str = substr($rawhtml,strpos($rawhtml, 'name="__VIEWSTATE"')-22,2000);
    $str = substr($str,0,strpos($str, '/>')+2);
    $str = simplexml_load_string($str);
    return trim($str['value']);
  }
  private function getEventvalidation($rawhtml){
    $str = substr($rawhtml,strpos($rawhtml, 'name="__EVENTVALIDATION"')-22,2000);
    $str = substr($str,0,strpos($str, '/>')+2);
    $str = simplexml_load_string($str);
    return trim($str['value']);
  }
  public function getActivityRAW(){
    curl_setopt($this->curl, CURLOPT_URL, $this->getURL('activity'));
    return curl_exec($this->curl);
  }
  public function getActivityParticipantsRAW(){
    curl_setopt($this->curl, CURLOPT_URL, $this->getURL('participants'));
    return curl_exec($this->curl);
  }
}

?>