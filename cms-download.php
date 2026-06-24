<?php
$file = __DIR__ . '/downloads/pagezy-cms-latest.zip';
if (!file_exists($file)) {
    http_response_code(404);
    exit('File not found.');
}
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="pagezy-cms-latest.zip"');
header('Content-Length: ' . filesize($file));
header('Cache-Control: no-cache');
readfile($file);
