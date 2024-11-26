<?php
$viewsDir = "/var/www/html/views";

$expirationTime = 300;

$currentTime = time();

if (is_dir($viewsDir)) {
    $files = scandir($viewsDir);

    foreach ($files as $file) {
        $filePath = $viewsDir . DIRECTORY_SEPARATOR . $file;

        if (is_file($filePath)) {
            // Get the file's last modification time
            $lastModifiedTime = filemtime($filePath);

            if ($currentTime - $lastModifiedTime > $expirationTime) {
                if (unlink($filePath)) {
                    echo "Deleted: $filePath\n";
                } else {
                    echo "Failed to delete: $filePath\n";
                }
            }
        }
    }
} else {
    echo "Directory does not exist: $viewsDir\n";
}
?>
