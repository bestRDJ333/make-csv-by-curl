<?php
include_once('src/setting.php');
include_once('src/curlClass.php');

date_default_timezone_set('Asia/Taipei');



if(empty($_FILES['file'])){
  echo $resJsString = "<script type='text/javascript'> alert('請上傳檔案'); history.back(); </script>";
  exit;
}

if ($_FILES['file']['error'] !== UPLOAD_ERR_OK){
  $phpFileUploadErrors = array(
    0 => 'There is no error, the file uploaded with success',
    1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
    2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
    3 => 'The uploaded file was only partially uploaded',
    4 => 'No file was uploaded',
    6 => 'Missing a temporary folder',
    7 => 'Failed to write file to disk.',
    8 => 'A PHP extension stopped the file upload.',
  );

  $message = $phpFileUploadErrors[$_FILES['file']['error']];
  echo $resJsString = "<script type='text/javascript'> alert('$message'); history.back(); </script>";
  exit;
}

// 檔案大小
/*
ini_set('post_max_size', '10M');
ini_set('upload_max_filesize', '10M');
// 超過 10 MB (this size is in bytes)
if($file['size'] > 10485760){
  echo $resJsString = "<script type='text/javascript'> alert('檔案最大為10MB'); history.back(); </script>";
  exit;
}
*/

$file = $_FILES['file'];
// 檔案副檔名
$extArr = array('csv');
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);

if(!in_array($extension, $extArr)){
  echo $resJsString = "<script type='text/javascript'> alert('請上傳csv檔'); history.back(); </script>";
  exit;
}

// 拿token
$curlClass =  new curlClass();


$curlClass->getToken(_USERNAME, _PASSWORD);
die();
// getToken
$token = (isset($tokenObj->token) && $tokenObj->token !== '')? $tokenObj->token : '';
if($token === ''){
  echo $resJsString = "<script type='text/javascript'> alert('token設定錯誤'); history.back(); </script>";
  exit;
}

// 開檔案
$f = fopen($file['tmp_name'], 'rb');
$num = 0;
// 匯出字串
$exportStr = '';
while(($line = fgetcsv($f)) !== false ){
  if($num === 0){
    // 列位數確認
    if(count($line) !== 2){
      echo $resJsString = "<script type='text/javascript'> alert('列為數為2'); history.back(); </script>";
      exit;
    }
  }
  else{
    $userId = $line[0];
    $email = $line[1];
    // 留言
    // $curlClass->getCurlData( _getTokenUrl, 'post', $postData);
    $commentsArr = userComments($token, $userId, '2022-01-01', '2022-02-23');
    // var_dump($commentsObj);

    if(count($commentsArr) > 0){
      foreach($commentsArr as $k => $obj){
        $exportStr .= $email. ' ,';
        $exportStr .= '="' .$obj->comment_post_id. ' ,';
        $exportStr .= $obj->comment_date. ' ,';
        $exportStr .= '"' .$obj->comment_content. '" ,';
        $exportStr .= PHP_EOL;
      }
    }
    else{
      $exportStr .= $email. ' ,';
      $exportStr .= ' ,';
      $exportStr .= ' ,';
      $exportStr .= ' ,';
      $exportStr .= PHP_EOL;
    }
  }
  $num++;
}



die();














ob_clean();
// 表頭
// header('Content-Type text/x-csv');
header('Content-Type text/x-csv; charset=UTF-8');
header('Content-Disposition: attachment; filename=匯出'. date('Y-m-d H-i-s') .'.csv');
$title = 'email,文章ID,留言時間,留言內容'. PHP_EOL;
$title = "\xEF\xBB\xBF" . $title;
echo $title.$exportStr;

function getToken(){
  // token
  $url='https://pansci.asia/wp-json/jwt-auth/v1/token';
  $ch = curl_init();
  
  $headers = array(
    "Content-Type: multipart/form-data",
    "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)",
  );
  // 夾帶內容
  $post_data = array('username' => _USERNAME,
  'password' => _PASSWORD);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
  // 設置標頭
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_URL, $url);
  // 返回內容作為變數儲存
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  // post_data
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
  // 執行
  $result = curl_exec($ch);
  // 關閉
  curl_close($ch);
  // 回傳為json格式 :obj
  $resultobj = json_decode($result);
  return $resultobj;
}

function userComments($token, $userId, $start_date, $end_date, $page = 1, $per_page = 50){
  
  if($token === '' || $userId === '' || $start_date === '' || $end_date === ''){ return false; }
  $curl = curl_init();
  $paramsStr = 'user_id='.$userId.'&start_date='.$start_date.'&end_date='.$end_date;
  
  curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://pansci.asia/wp-json/pansci/v1/user_comments?'.$paramsStr,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
      'Authorization: Bearer '.$token
    ),
  ));

  $response = curl_exec($curl);
  $resultobj = json_decode($response);
  $data = $resultobj->data;

  // 反回陣列
  $dataArry = array();
  if($data->num_rows > 0){
    $dataArry = array_merge($dataArry, $data->results);
    
    if($data->num_pages > 1){
      // 1頁以上
      for($i = 1; $i <= $data->num_pages; $i++){
        $response = '';
        $resultobj = '';
        $paramsStr .= '&page='.$i;
        $response = curl_exec($curl);
        $resultobj = json_decode($response);
        $data = $resultobj->data;
        $dataArry = array_merge($dataArry, $data->results);
      }
    }
  }
  curl_close($curl);
  return $dataArry;
}
