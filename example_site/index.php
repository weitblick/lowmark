<?php include_once 'lowmark/core.php'; ?>
<!doctype html>
<html lang="en">
<head>
    <!-- render time: <?= $execution_time ?> milliseconds -->
    <title><?= (!$lowmark['home'] ? $lowmark['title'] . " | " : '') . ($lowmark['sitename'] ?? 'Missing Site Name') ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="<?= htmlspecialchars($lowmark['description']) ?>">

    <link rel="canonical" href="<?= $lowmark['canonical_url'] ?>">

    <link href="/assets/css/lowmark_simple.css" rel="stylesheet">

    <?php if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) echo '<script src="/livereload/livereload.js" data-no-instant defer></script>' // local development: livereload on changes ?>

</head>

<body>
    <header>
        <div class="sitename">
        	<a href="/">
                <?= $lowmark['logo'] ?? '' ?>
        	</a>
        </div>
        <nav>
            <a href="/">Home</a>
            <a href="/example.html">Example Page</a>
        </nav>
    </header>
    <div class="main">
        <main>
            <?= $lowmark['content'] ?>
        </main>
        <footer>
            <span><a href="/">Home</a></span>
            <span>| <a href="/example.html">Example Page</a></span>
            <span>| <a href="/legal.html">Legal Notice</a></span>
            <span>| <a href="/privacy.html">Privacy Policy</a></span>
            <br><span>powered by <a href="https://lowmark.de" target="_blank">lowmark</a></span>
        </footer>
    </div>
</body>
</html>
