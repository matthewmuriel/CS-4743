<!doctype html>
<html>
<meta charset="utf-8">
<title>Search Loan Number</title>
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

$pdo = db_connect('doc_storage');
$query = "SELECT file_name, loan_document_id FROM Loan_Document";
$stmt = $pdo->prepare($query);
$stmt->execute();

$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo '<div id ="page-inner">';
echo '<h1 class="page-head-line">Results for all Files </h1>';
echo '<div class="panel-body">';

if ($files == null){
    echo '<h2>No Files Found</h2>';
}else{
    echo '<table class="table table-hover">';
    echo '<thead>';
    echo '<tr>';
    echo '<th scope="col">'."ID".'</th>';
    echo '<th scope="col">'."File Name".'</th>';
    echo '</tr>';
    echo '<tbody>';

    foreach ($files as $file){
        echo '<tr>';
        echo '<th scope="row">'.$file['loan_document_id'].'</th>';
        echo '<td>';
        echo '<a href="view_doc.php?docid='.$file['loan_document_id'].'" class="link-dark">'. $file['file_name'].'</a>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '<table>';
}

echo '</div>';
echo '</div>';
echo '</div>';

?>
</body>
</html>