<!doctype html>
<html>
<meta charset="utf-8">
<title>Search Main</title>
<!-- BOOTSTRAP STYLES-->
<link href="assets/css/bootstrap.css" rel="stylesheet" />
<!-- FONTAWESOME STYLES-->
<link href="assets/css/font-awesome.css" rel="stylesheet" />
   <!--CUSTOM BASIC STYLES-->
<link href="assets/css/basic.css" rel="stylesheet" />
<!--CUSTOM MAIN STYLES-->
<link href="assets/css/custom.css" rel="stylesheet" />
<!-- PAGE LEVEL STYLES -->
<link href="assets/css/bootstrap-fileupload.min.css" rel="stylesheet" />
<!-- JQUERY SCRIPTS -->
<script src="assets/js/jquery-1.10.2.js"></script>
<!-- BOOTSTRAP SCRIPTS -->
<script src="assets/js/bootstrap.js"></script>
<script src="assets/js/bootstrap-fileupload.js"></script>
</head>
<body>
<?php
echo '<div id ="page-inner">';

if (isset($_GET['success'])){
   if ($_GET['success']=="FileUploaded"){
      echo '<div class="alert alert-success" role="alert">File Successfully Uploaded!</div>';
   }
}

echo '<h1 class="page-head-line">Select the Search Criteria</h1>';
echo '<div class="panel-body">';
echo '<p><a class="btn btn-primary" href="search_loan.php">By Loan ID</a></p>';
echo '<p><a class="btn btn-primary" href="search_docType.php">By Document Type</a></p>';
echo '<p><a class="btn btn-primary" href="search_date.php">By Date</a></p>';
echo '<p><a class="btn btn-primary" href="search_all.php">By All</a></p>';
echo '</div>';
echo '</div>';
?>
</body>
</html>