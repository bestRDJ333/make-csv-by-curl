<?php
/**
 * 將curl改成物件
 */
class curlClass
{
  public $CURLOPT_MAXREDIRS = 10;

  public $CURLOPT_TIMEOUT = 0;

  public $InitArray;
  
  public function __construct(){
    $this->InitArray = array(
      CURLOPT_MAXREDIRS => $this->CURLOPT_MAXREDIRS,
      CURLOPT_TIMEOUT   => $this->CURLOPT_TIMEOUT
    );
  }

  public function getCurlData($url, $method = 'POST', $postData = array(), $token = '', $CURLOPT_RETURNTRANSFER = true){
    
    // 判斷url
    if(empty($url) || is_null($url)){
      return false;
    }
    $this->InitArray[CURLOPT_URL] = $url;

    // 設定標頭
    $header = array(
      "Content-Type: multipart/form-data",
      "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)",
    );

    //token
    if(!empty($token)){
      $header[] = 'Authorization: Bearer '.$token;
    }

    $this->InitArray[CURLOPT_HTTPHEADER] = $header;

    // 返回內容作為變數儲存
    if(!empty($CURLOPT_RETURNTRANSFER)){
      $this->InitArray[CURLOPT_RETURNTRANSFER] = true;
    }

    // 
    if(strtoupper($method) === 'POST'){
      $this->InitArray[CURLOPT_CUSTOMREQUEST] = 'POST';
    }
    else{
      $this->InitArray[CURLOPT_CUSTOMREQUEST] = 'GET';
    }

    if(!empty($postData)){
      $this->InitArray[CURLOPT_POST] = true;
      $this->InitArray[CURLOPT_POSTFIELDS] = $postData;
    }

    $curl = curl_init();

    // var_dump($this->InitArray);

    curl_setopt_array($curl, $this->InitArray);
    $result = curl_exec($curl);
    curl_close($curl);

    return $result;
  }

  // 設定加密編碼
  public function setEnCoding($CURLOPT_ENCODING){
    $this->InitArray[CURLOPT_ENCODING] = $CURLOPT_ENCODING;
  }

  public function getToken($username, $password){

    $postData = array(
      'username' => $username,
      'password' => $password
    );

    $result = $this->getCurlData( _TOKENURL, 'post', $postData);
    $resultobj = json_decode($result);
  
    // 回傳為json格式 :obj
    return $resultobj = json_decode($result);
  }

  public function getUserComments($token, $userId, $start_date, $end_date, $page = 1, $per_page = 50){
    if($token === '' || $userId === '' || $start_date === '' || $end_date === ''){ 
      return false;
    }
    $paramsStr = 'user_id='.$userId.'&start_date='.$start_date.'&end_date='.$end_date.'&page='.$page.'&per_page='.$per_page;
    $url = _USERCOMMENTSURL . '?' .$paramsStr;

    $result = $this->getCurlData( $url, 'get', array(), $token);

    return $resultobj = json_decode($result);
  }

}
