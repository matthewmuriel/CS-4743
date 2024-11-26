<!doctype html>
<html>
<meta charset="utf-8">
<title>View Document</title>
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

if (isset($_GET['docid'])){
    echo '<div id ="page-inner">';
    echo '<h1 class="page-head-line">View Document</h1>';
    echo '<div class="panel-body">';
    $docId = $_GET['docid'];
    $pdo = db_connect('doc_storage');
    $query = "SELECT * FROM Loan_Document WHERE loan_document_id = :loan_document_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute(['loan_document_id' => $docId]);
    
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $loanNumber = getLoanNumberById($result['loan_id']);
    $documentType = getDocumentNameById($result['document_id']);
    $filePath = $result['file_path'];
    
    echo '<h3>Document Name: '. $result['file_name'].'</h3>';
    echo '<h3>Loan Number: '. $loanNumber.'</h3>';
    echo '<h3>Document Type: '. $documentType.'</h3>';
    echo '<h3>File Size: '. $result['file_size']." bytes".'</h3>';
    echo '<h3>Date Submitted: '. $result['submission_date'].'</h3>';
    echo '<h3>Last Access: '. $result['last_access'].'</h3>';
    
    $viewPath = makeFileAccessible($filePath, $result['file_name']);

    echo '<h3><a href="'.$viewPath.'" target="_blank">View Document</a></h3>';
    echo '</div>';
    echo '</div>';

    updateLastAccess($docId);
}else{
    redirect('search_main.php');
}


?>
</body>
</html>