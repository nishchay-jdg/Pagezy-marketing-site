<?php
// One-time fix — delete this file after use
if (($_GET['token'] ?? '') !== 'pagezy-fix-2026') {
    http_response_code(403); exit('Forbidden');
}

$repo = '/home/pagezy/public_html';
$results = [];

// Try every available PHP exec method
$cmd = "cd {$repo} && git status 2>&1 && echo '---DIFF---' && git diff --name-only 2>&1";

if (function_exists('shell_exec')) {
    $results['shell_exec'] = shell_exec($cmd);
} elseif (function_exists('passthru')) {
    ob_start(); passthru($cmd); $results['passthru'] = ob_get_clean();
} elseif (function_exists('system')) {
    ob_start(); system($cmd); $results['system'] = ob_get_clean();
} elseif (function_exists('proc_open')) {
    $p = proc_open($cmd, [1=>['pipe','w'],2=>['pipe','w']], $pipes);
    if ($p) { $results['proc_open'] = stream_get_contents($pipes[1]) . stream_get_contents($pipes[2]); proc_close($p); }
} elseif (function_exists('popen')) {
    $p = popen($cmd, 'r');
    if ($p) { $results['popen'] = stream_get_contents($p); pclose($p); }
} else {
    $results['error'] = 'All exec functions are disabled on this server.';
}

// Also check disabled functions
$results['disable_functions'] = ini_get('disable_functions');

header('Content-Type: text/plain');
foreach ($results as $method => $out) {
    echo "=== {$method} ===\n{$out}\n\n";
}
echo "Done. Delete this file now.\n";
