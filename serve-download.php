<?php
// Serves the CMS ZIP securely (no direct file URL exposed)
require_once 'config.php';

$file = __DIR__ . '/' . DOWNLOAD_FILE;

if (!file_exists($file)) {
    http_response_code(404);
    exit('Download file not found. Please contact support.');
}

// Log the download
try {
    ensureTable();
    db()->exec("UPDATE pagezy_downloads SET downloaded=1
                WHERE email='" . ($_SESSION['dl_email'] ?? '') . "'
                ORDER BY created_at DESC LIMIT 1");
} catch (Exception $e) { /* silent */ }

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . DOWNLOAD_FILENAME . '"');
header('Content-Length: ' . filesize($file));
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
readfile($file);
exit;
