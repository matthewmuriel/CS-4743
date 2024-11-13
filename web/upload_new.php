<!doctype html>
<html>
<meta charset="utf-8">
<title>Upload New</title>
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
include("../php/functions.php");

echo '<div id="page-inner">';
echo '<h1 class="page-head-line">Upload a New File to Database</h1>';
echo '<div class="panel-body">';
echo '<form method="post" enctype="multipart/form-data" action="">';
echo '<input type="hidden" name="MAX_FILE_SIZE" value="10000000">';
echo '<div class="form-group">';
echo '<label for="loanNum" class="control-label">Loan Number</label>';
echo '<input type="text" name="loanNum" class="form-control">';
echo '</div>';

echo '<div class="form-group">';
echo '<label for="docType" class="control-label">Document Type</label>';
echo '<select class="form-control" name="docType">';

$documents = fetchDocumentsFromDatabase();

foreach ($documents as $document) {
   echo '<option value="'.$document['document_id'].'">'.$document['document_name'].'</option>';
}
echo '<div class="form-group">';
echo '<label class="control-label col-lg-4">File Upload</label>';
echo '<div class="">';
echo '<div class="fileupload fileupload-new" data-provides="fileupload">';
echo '<div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;"></div>';
echo '<div class="row"><div class="col-md-2"><span class="btn btn-file btn-primary">';
echo '<span class="fileupload-new">Select File</span>';
echo '<span class="fileupload-exists">Change</span>';
echo '<input name="userfile" type="file"></span></div>';
echo '<div class="col-md-2"><a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload">Remove</a></div></div></div></div></div><hr>';
echo '<button type="submit" name="submit" value="submit" class="btn btn-lg btn-block btn-success">Upload File</button>';
echo '</form>';
echo '</div>';
echo '</div>';
echo '</div>';
?>
</body>
</html>