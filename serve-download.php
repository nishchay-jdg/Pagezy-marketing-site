<?php
require_once 'config.php';

$file = __DIR__ . '/' . DOWNLOAD_FILE;
if (!file_exists($file)) {
    http_response_code(404);
    exit('Download not available yet. Please contact support@pagezy.io');
}

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . DOWNLOAD_FILENAME . '"');
header('Content-Length: ' . filesize($file));
header('Cache-Control: no-cache, must-revalidate');
readfile($file);
exit;
