<?php
    include("../php/functions.php");
    // Connect to both databases
    $fileMetadataDB = db_connect('file_metadata'); // file_metadata database connection
    $docStorageDB = db_connect('doc_storage');    // doc_storage database connection

    try {
        // Fetch all rows from file_imports
        $query = "SELECT file_id, loan_number FROM file_imports";
        $stmt = $fileMetadataDB->prepare($query);
        $stmt->execute();
        $fileImports = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Iterate through each file_import row
        foreach ($fileImports as $file) {
            $fileName = $file['file_id'];
            $loanNumber = $file['loan_number'];

            // Get the correct loan_id from the Loan table
            $loanQuery = "SELECT loan_id FROM Loan WHERE loan_number = :loan_number";
            $loanStmt = $docStorageDB->prepare($loanQuery);
            $loanStmt->execute(['loan_number' => $loanNumber]);
            $loan = $loanStmt->fetch(PDO::FETCH_ASSOC);

            if ($loan) {
                $loanID = $loan['loan_id'];

                // Update the Loan_Document table
                $updateQuery = "
                    UPDATE Loan_Document 
                    SET loan_id = :loan_id 
                    WHERE file_name = :file_name";
                $updateStmt = $docStorageDB->prepare($updateQuery);
                $updateStmt->execute(['loan_id' => $loanID, 'file_name' => $fileName]);

                echo "Updated file: $fileName with loan_id: $loanID\n";
            } else {
                // Log if no matching loan_id is found
                error_log("No matching loan_id for loan_number: $loanNumber");
                echo "No matching loan_id for file: $fileName (loan_number: $loanNumber)\n";
            }
        }

        echo "Loan_Document table updated successfully.";
    } catch (PDOException $e) {
        error_log("Error updating Loan_Document: " . $e->getMessage());
        echo "An error occurred while updating Loan_Document.";
    }

?>