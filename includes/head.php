<?php
$pageTitle       = $pageTitle       ?? 'Pagezy — Build beautiful websites in minutes.';
$pageDescription = $pageDescription ?? 'Pagezy is a powerful, easy-to-use CMS that lets anyone build beautiful websites in minutes. Download free and get started today.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($pageTitle) ?></title>
<meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
<meta name="robots" content="index,follow">
<meta property="og:title"       content="<?= htmlspecialchars($pageTitle) ?>">
<meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>">
<meta property="og:image"       content="https://pagezy.io/assets/img/og.png">
<meta property="og:url"         content="https://pagezy.io">
<meta name="twitter:card"       content="summary_large_image">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/style.css">
<link rel="icon" href="/assets/img/pagezy-logo.png" type="image/png">
<link rel="shortcut icon" href="/assets/img/pagezy-logo.png">
</head>
<body>
