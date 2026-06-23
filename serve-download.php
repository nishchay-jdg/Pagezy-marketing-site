<?php
require_once 'config.php';

// If we have a cached GitHub release URL, redirect there
if (file_exists(RELEASE_CACHE)) {
    $release = json_decode(file_get_contents(RELEASE_CACHE), true);
    if (!empty($release['download_url'])) {
        header('Location: ' . $release['download_url']);
        exit;
    }
}

// Fallback: serve local ZIP if present
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
