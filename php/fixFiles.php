<?php
include ("functions.php");
    $file = 'files_fix.txt';
    $pdo = db_connect('file_metadata');
    $handle = fopen($file,'r');

    while(( $line = fgets($handle)) !== false) {
        $fileName = trim($line);
        $stmt = $pdo->prepare("SELECT * FROM file_imports WHERE file_id = :file_id");
        $stmt->execute(['file_id' => $fileName]);

        $file = $stmt->fetch(PDO::FETCH_ASSOC);

        $fileID = $file["file_id"];
        $loanNumber = $file["loan_number"];
        $documentType = $file["document_type"];
        $version = $file["version"];
        $created = $file["created_at"];

        $targetDirectory = "/var/private_files/files/loans/$loanNumber/";
        $finalFilePath = $targetDirectory . $fileID;

        $loanID = fetchLoanID($loanNumber);
        $documentID = fetchDocumentID($documentType);

        insertDocumentData($loanID, $documentID, $fileID, $created, $finalFilePath, $version, "Cron");

    }
    fclose($handle);
?>