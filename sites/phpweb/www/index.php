<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> -->
  <title>Document</title>
</head>
<body>
  上傳檔案
  <form id="form" method="post" action="export.php" enctype="multipart/form-data">
      <input type="file" id="file" name="file" accept=".csv" />
      <!-- <input type="text" name="daterange" value="01/01/2018 - 01/15/2018" /> -->
      <input type="submit" value="送出"/>
  </form>
</body>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script> -->
<script type="text/javascript">

</script>
</html>
