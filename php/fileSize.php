<?php
include("functions.php");
function updateFileSizes() {
    $pdo = db_connect('doc_storage'); // Adjust your database connection function
    
    // Query to fetch all loan documents
    $stmt = $pdo->query("SELECT loan_document_id, file_path FROM Loan_Document WHERE file_path IS NOT NULL");
    $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($documents as $doc) {
        $filePath = $doc['file_path'];
        $loanDocumentId = $doc['loan_document_id'];

        if (file_exists($filePath)) {
            $fileSize = filesize($filePath); // Get file size in bytes

            // Update the file size in the database
            $updateStmt = $pdo->prepare("UPDATE Loan_Document SET file_size = :file_size WHERE loan_document_id = :loan_document_id");
            $updateStmt->execute(['file_size' => $fileSize, 'loan_document_id' => $loanDocumentId]);

            echo "Updated loan_document_id $loanDocumentId with file size $fileSize bytes\n";
        } else {
            error_log("File not found: $filePath");
        }
    }
}
updateFileSizes();
?>
