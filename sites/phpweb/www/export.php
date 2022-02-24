<?php
date_default_timezone_set('Asia/Taipei');
$resJson = array('status' => 200);

if ($_FILES['file']['error'] === UPLOAD_ERR_OK){
  $file = $_FILES['file'];
  // 檔案副檔名
  $extArr = array('csv');
  $extension = pathinfo($file['name'], PATHINFO_EXTENSION);

  if(!in_array($extension, $extArr)){
    $resJson['mesg'] = '請上傳csv檔';
    echo json_encode($resJson);
    exit;
  }

  // getToken
  $tokenObj = getToken();
  $token = (isset($tokenObj->token) && $tokenObj->token !== '')? $tokenObj->token : '';
  if($token === ''){
    $resJson['mesg'] = 'token設定錯誤';
    echo json_encode($resJson);
    exit;
  }

  // 檔案大小
  ini_set('post_max_size', '10M');
  ini_set('upload_max_filesize', '10M');
  // 超過 10 MB (this size is in bytes)
  if($file['size'] <= 10485760){
    $f = fopen($file['tmp_name'], 'rb');
    $num = 0;
    // 匯出字串
    $exportStr = '';
    while(($line = fgetcsv($f)) !== false ){
      if($num === 0){
        // 列位數確認
        if(count($line) !== 2){
          $resJson['mesg'] = '列位數為2';
          echo json_encode($resJson);
          exit;
        }
      }
      else{
        // convert utf8
        $userId = $line[0];
        $email = $line[1];
        // 留言
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
  }
  else{
    $resJson['mesg'] = '檔案最大為10mb';
    echo json_encode($resJson);
    exit;
  }
}
else{
  $resJson['mesg'] = '請上傳檔案';
  echo json_encode($resJson);
  exit;
}

ob_clean();
// 表頭
// header('Content-Type text/x-csv');
header('Content-Disposition: attachment; filename='. date('Y-m-d-H-i-s') .'.csv');
header('Content-Type text/x-csv; charset=UTF-8');
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
  $post_data = array("username"=>"", "password"=>"");
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
