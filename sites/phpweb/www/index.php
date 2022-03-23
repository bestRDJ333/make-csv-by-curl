<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" type="text/css" href="/build/css/daterangepicker.css" />
  <link rel="stylesheet" type="text/css" href="/build/css/app.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">
  <title>Document</title>
</head>
<body>
  <div class="content">
    <div class="content-block">
      <form id="form" method="post" action="export.php" enctype="multipart/form-data">
          <div>
            <label for="">上傳檔案</label>
            <input class="form-control" type="file" id="file" name="file" accept=".csv">
          </div>
          <div>
            <label for="">日期區間</label>
            <input class="form-control" type="text" name="daterange">
          </div>
          <input type="submit" value="送出">
      </form>
    </div>
  </div>
</body>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
<script type="text/javascript" src="/build/js/moment.min.js"></script>
<script type="text/javascript" src="/build/js/daterangepicker.js"></script>
<script type="text/javascript">
  $(function(){
    $('input[name="daterange"]').daterangepicker();
  })
</script>
</html>
