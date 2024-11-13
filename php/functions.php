<?php

enum ApiEndpoint: string 
{
    case CLEAR_SESSION = 'https://cs4743.professorvaladez.com/api/clear_session';
    case CREATE_SESSION = 'https://cs4743.professorvaladez.com/api/create_session';
    case QUERY_FILES = 'https://cs4743.professorvaladez.com/api/query_files';
    CASE REQUEST_FILE = 'https://cs4743.professorvaladez.com/api/request_file';
    CASE REQUEST_ALL_DOCUMENTS = 'https://cs4743.professorvaladez.com/api/request_all_documents';
    CASE REQUEST_ALL_LOANS = 'https://cs4743.professorvaladez.com/api/request_all_loans';
    CASE REQUEST_FILE_BY_LOAN = 'https://cs4743.professorvaladez.com/api/request_file_by_loan';
}

const S_USERNAME = "bxj214";
const S_PASSWORD = "94JFRTwjfn37mk6t";

const HOSTNAME = "localhost";
const DB_USERNAME = "webuser";
const DB_PASSWORD = "nRF5Rh*eXG9bm4KZ";

const LOG_DIR = "/var/private_files/logs/audit_log_";

function db_connect($db){
    try {
        $dsn = "mysql:host=".HOSTNAME.";dbname=".$db.";charset=utf8mb4";

        // Create a new PDO instance with error mode set to Exception for better error handling
        $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;

    } catch (PDOException $e) {
        echo "Error connecting to the database: " . $e->getMessage();
        return null;
    }
}

/**
 * Summary of fetchLoanIDsFromDatabase
 * @return array|null
 */
function fetchLoanIDsFromDatabase(){
    try {
        $pdo = db_connect('doc_storage');
        $stmt = $pdo->query("Select loan_id, loan_number from Loan");
        $loanIDs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $loanIDs;
    } catch (PDOException $e) {
        error_log("Error fetching loan ids from database: " . $e->getMessage());
        return null;
    }
}

function fetchDocumentsFromDatabase(){
    try {
        $pdo = db_connect('doc_storage');
        $stmt = $pdo->query("Select document_id, document_name from Document");
        $documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $documents;
    } catch (PDOException $e) {
        error_log("Error fetching loan ids from database: " . $e->getMessage());
        return null;
    }    
}

function fetchPendingDocuments(){
    try{
        $pdo = db_connect('file_metadata');
        $stmt = $pdo->query('Select file_id from file_imports where status = "Pending"');
        $documents = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $documents;
    } catch (PDOException $e) {
        error_log("Error fetching pending documents from database: " . $e->getMessage());
        return null;
    }
}

/**
 * Fetch all documents from the file_imports table in the file_metadata database.
 * If the document is in the database, then it has been imported.
 * @return array|null
 */
function fetchAllDocuments(){
    try{
        $pdo = db_connect("file_metadata");
        $stmt = $pdo->query("Select file_id from file_imports");
        $documents = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $documents;
    }catch (PDOException $e) {
        error_log("Error fetching all documents from database: " . $e->getMessage());
        logMessage("Error fetching all documents from database: " . $e->getMessage());
        return null;
    }
}

/**
 * Insert a loan ID into the doc_storage database into the Loan table.
 * @param string $loanID
 * @return void
 */
function insertLoanID($loanID){
    try {
        $pdo = db_connect('doc_storage');
        $sql = "INSERT INTO Loan (loan_number) VALUES (:loanID)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':loanID', $loanID, PDO::PARAM_STR);
        $stmt->execute();

        logMessage("Inserted loan id $loanID into database.", "INFO");
    } catch (PDOException $e) {
        logMessage("Error inserting {$loanID} into database: " . $e->getMessage());
        error_log("Error inserting loan id into database: " . $e->getMessage());
    }
}


function insertImportData($fileName, $fileParts){
    try{
        $pdo = db_connect("file_metadata");
        $sql = "INSERT INTO file_imports (file_id, loan_number, document_type, version, created_at) VALUES (:file_id, :loan_id, :document_name, :version, :timestamp)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':file_id', $fileName, PDO::PARAM_STR);
        $stmt->bindParam(':loan_id', $fileParts[0], PDO::PARAM_STR);
        $stmt->bindParam(':document_name', $fileParts[1], PDO::PARAM_STR);
        $stmt->bindParam(':version', $fileParts[2], PDO::PARAM_INT);
        $stmt->bindParam(':timestamp', $fileParts[3], PDO::PARAM_STR);
        $stmt->execute();

        logMessage('Inserted import data for ' . $fileName . ' into database.', "INFO");
    } catch (PDOException $e) {
        error_log("Error inserting import data into database: " . $e->getMessage());
        logMessage("Error inserting import data into database: " . $e->getMessage());
    }
}

/**
 * Check if a loan ID exists in the doc_storage database. Return true if it exists, false otherwise.
 * @param mixed $loanID
 * @return bool
 */
function checkLoanID($loanID){
    try{
        $pdo = db_connect("doc_storage");
        $stmt = $pdo->prepare("SELECT 1 FROM Loan WHERE loan_number = :loan_id LIMIT 1");
        $stmt->bindParam(':loan_id', $loanID);
        $stmt->execute();        
        
        return $stmt->fetchColumn() !== false;
    }catch (PDOException $e) {
        error_log("". $e->getMessage());
        logMessage("". $e->getMessage());
    }
}

/**
 * Check if a document name exists in the doc_storage database. Return true if it exists, false otherwise.
 * @param mixed $document
 * @return bool
 */
function checkDocumentName($document){
    try{
        $pdo = db_connect("doc_storage");
        $stmt = $pdo->prepare("SELECT 1 FROM Document WHERE document_name = :document_name LIMIT 1");
        $stmt->bindParam(':document_name', $document);
        $stmt->execute();

        return $stmt->fetchColumn() !== false;
    }catch (PDOException $e) {
        error_log("". $e->getMessage());
        logMessage("". $e->getMessage());
    }
}

function queryFiles($sid){
    $data = "uid=". S_USERNAME . "&sid=" . $sid;
    $response = sendCurlRequest(ApiEndpoint::QUERY_FILES, $data);

    if ($response["status"] === "error") {
        error_log($response["message"]);
        throw new Exception($response["message"]);
    }

    $cinfo = json_decode($response["result"], true);
    if ($cinfo[0] == "Status: OK") {
        $tmp = explode(":", $cinfo[1]);
        $payLoad = json_decode($tmp[1]);

        if ($payLoad == null){
            return null;
        }

        return $payLoad;
    } else {
        error_log("Failed to query files: $cinfo[1]");
        throw new Exception("Failed to query files: $cinfo[1]");
    }
}

function breakDownFileName($fileName){
    $pattern = '/_([0-9]+)$/';
    $parts = explode("-", $fileName);

    $loanID = $parts[0];
    $documentName = $parts[1];
    
    if (preg_match($pattern, $documentName, $matches)) {
        $temp = explode("_", $documentName);
        $documentName = $temp[0];
        $version = (int) $matches[1];
        $version++;
    } else {
        $version = 1;
    }

    $tmp = explode(".", $parts[2]);
    $timestamp = DateTime::createFromFormat('Ymd_H_i_s', $tmp[0])->format('Y-m-d H:i:s');

    return [$loanID, $documentName, $version, $timestamp];
}

function updateFileStatus($fileName, $status){
    try{
        $pdo = db_connect("file_metadata");
        $sql = "UPDATE file_imports SET status = :status, updated_at = NOW() WHERE file_id = :file_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':file_id', $fileName, PDO::PARAM_STR);
        $stmt->execute();

        logMessage("Updated file status for $fileName to $status", "INFO");
    }catch (PDOException $e){
        error_log("Error updating file status: " . $e->getMessage());
        logMessage("Error updating file status: " . $e->getMessage());
    }
}

function getFileData($fileName, $sid){
    $data = "uid=". S_USERNAME . "&sid=" . $sid . "&fid=" . $fileName;
    $response = sendCurlRequest(ApiEndpoint::REQUEST_FILE, $data);

    return $response;
}

/**
 * Get all loan ids from the API endpoint, return an array of loan ids.
 * @param string $sid
 * @throws Exception
 * @return array
 */
function getAllLoanIds($sid){
    $data = "uid=" . S_USERNAME . "&sid=" . $sid;
    $response = sendCurlRequest(ApiEndpoint::REQUEST_ALL_LOANS, $data);

    if ($response["status"] === "error") {
        error_log($response["message"]);
        throw new Exception($response["message"]);
    }

    $cinfo = json_decode($response["result"], true);

    if ($cinfo[0] == "Status: OK") {
        $tmp = explode(":", $cinfo[1]);
        $payLoad = json_decode($tmp[1]);

        return $payLoad;
    } else {
        error_log("Failed to get all loan ids: $cinfo[1]");
        throw new Exception("Failed to get all loan ids: $cinfo[1]");
    }

}

function getAllDocuments($sid){
    $data = "uid=" . S_USERNAME . "&sid=" . $sid;
    $response = sendCurlRequest(ApiEndpoint::REQUEST_ALL_DOCUMENTS, $data);

    if ($response["status"] === "error") {
        error_log($response["message"]);
        throw new Exception($response["message"]);
    }

    $cinfo = json_decode($response["result"], true);

    if ($cinfo[0] == "Status: OK") {
        $tmp = explode(":", $cinfo[1]);
        $payLoad = json_decode($tmp[1]);

        return $payLoad;
    } else {
        error_log("Failed to get all documents: $cinfo[1]");
        throw new Exception("Failed to get all documents: $cinfo[1]");
    }
}

function createSession($retryOnPrev = true){
    $data = "username=" . S_USERNAME . "&password=" . S_PASSWORD;
    $response = sendCurlRequest(ApiEndpoint::CREATE_SESSION, $data);

    if ($response['status'] === 'error') {
        error_log($response['message']);
        throw new Exception($response['message']);
    }

    $cinfo = json_decode($response['result'], true);

    if ($cinfo[0]  == "Status: OK") {
        $sid = $cinfo[2];
        return $sid;
    }elseif ($cinfo[1] == "MSG: Previous Session Found"){
        if ($retryOnPrev){
            clearSID();
            return createSession(false);
        }else{
            error_log("Failed to create session: Previous session exists and could not be cleared.");
            throw new Exception("Failed to create session: Previous session exists and could not be cleared.");            
        }
    }
    else {
        error_log("Failed to create session: $cinfo[1]");
        throw new Exception("Failed to create session: $cinfo[1]");
    }

}

function clearSID(){
    $data = "username=".S_USERNAME."&password=".S_PASSWORD;
    $response = sendCurlRequest(ApiEndpoint::CLEAR_SESSION, $data);

    if ($response['status'] === 'error') {
        error_log($response['message']);
        return [
            'status' => 'error',
            'message' => $response['message'],
        ];
    }

    $cinfo = json_decode($response['result'], true);

    if ($cinfo[0]  == "Status: OK") {
        return [
            'status' => 'success',
            'message' => 'Session cleared successfully',
        ];
    } else {
        error_log("Failed to clear session: $cinfo[1]");
        return [
            'status' => 'error',
            'message' => "Failed to clear session: $cinfo[1]",
        ];
    }
}

function sendCurlRequest(ApiEndpoint $endpoint, $data, $maxRetries = 5, $timeout = 45, $connectTimeout = 5){
    $retryCount = 0;
    $result = null;
    
    while ($retryCount < $maxRetries) {
        $ch = curl_init($endpoint->value);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array (
            'content-type: application/x-www-form-urlencoded',
            'content-length: '. strlen($data)
        ));

        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $connectTimeout);
        curl_setopt($ch, CURLOPT_TCP_KEEPALIVE, 1);
        curl_setopt($ch, CURLOPT_TCP_KEEPIDLE, 10);
        curl_setopt($ch, CURLOPT_TCP_KEEPINTVL, 5);

        $time_start=microtime(true);
        $result=curl_exec($ch);
        $time_end=microtime(true);
        $execution_time=($time_end-$time_start)/60;
        curl_close($ch);

        if ($result !== false && !empty($result)) {

            if ($endpoint->value == 'https://cs4743.professorvaladez.com/api/request_file') {
                if(strstr($result, "Status")){
                    throw new Exception("There was an error with file");
                }else{
                    return $result;
                }
            }

            $details = explode("/", $endpoint->value);

            logPerformance($execution_time, 'API Request', $details[4], 'SUCCESS');

            return [
                'status' => 'success',
                'result' => $result,
            ];
        }

        $error_msg = curl_error($ch);
        error_log("Attempt ".($retryCount + 1)." failed: $error_msg");

        $retryCount++;

        sleep(2);
    }

    $error_msg = curl_error($ch);
    error_log("Failed to connect to {$endpoint->value} after $maxRetries attempts.");
    logPerformance($execution_time, 'API Request', $endpoint->value, 'ERROR');
    throw new Exception("Failed to connect to the API: $error_msg");

}

/**
 * Logs an error into the error_logs table.
 *
 * @param string $function_name - The function or module where the error occurred (e.g., 'createSession').
 * @param string $description   - A short description of the error.
 * @param string $details       - Additional details, such as the API response or error message.
 * @param enum   $severity      - The severity of the error (e.g., 'INFO','WARNING','ERROR','CRITICAL').
 */
function logError($errorMessage, $fileName = null, $functionName = null, $severity = 'ERROR'){
    try{
        $pdo = db_connect('system_logs');

        $sql = "
            INSERT INTO error_logs (error_message, file_name, function_name, severity)
            VALUES (:errorMessage, :fileName, :functionName, :severity)
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':errorMessage', $errorMessage, PDO::PARAM_STR);
        $stmt->bindParam(':fileName', $fileName, PDO::PARAM_STR);
        $stmt->bindParam(':functionName', $functionName, PDO::PARAM_STR);
        $stmt->bindParam(':severity', $severity, PDO::PARAM_STR);

        $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error Inserting into database: " . $e->getMessage());
        return;
    }
}

function logPerformance($execution_time, $operation_type, $details = null, $status){
    try{
        $pdo = db_connect('system_logs');

        $sql = "
            INSERT INTO performance_logs (execution_time, operation_type, details, status)
            VALUES (:execution_time, :operation_type, :details, :status)
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':execution_time', $execution_time, PDO::PARAM_STR);
        $stmt->bindParam(':operation_type', $operation_type, PDO::PARAM_STR);
        $stmt->bindParam(':details', $details, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);

        $stmt->execute();
    } catch (PDOException $e) {
        error_log("Error Inserting into database: " . $e->getMessage());
        return;
    }
}

function logMessage($message, $severity){
    $log = LOG_DIR . date("Y-m-d") . ".log";
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($log, "[$timestamp] $message $severity".PHP_EOL, FILE_APPEND);
}

function logCron($job_name, $time_start, $time_end, $execution_time, $status){
    try {
        $pdo = db_connect("system_logs");
        $sql = "
            INSERT INTO cron_job_logs (job_name, time_start, time_end, execution_time, status) 
            VALUES (:job_name, :time_start, :time_end, :execution_time, :status)
        ";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':job_name', $job_name, PDO::PARAM_STR);
        $stmt->bindParam(':time_start', $time_start, PDO::PARAM_STR);
        $stmt->bindParam(':time_end', $time_end, PDO::PARAM_STR);
        $stmt->bindParam(':execution_time', $execution_time, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        
        $stmt->execute();

    } catch (PDOException $e) {
        error_log("Error inserting into database: " . $e->getMessage());
        return;
    }
}

?>