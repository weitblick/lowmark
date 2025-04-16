<?php include_once 'lowmark/core.php'; ?>
<!doctype html>
<html lang="de">
<head>
    <!-- render time: <?= $execution_time ?> milliseconds -->
    <title><?= (!$lowmark['home'] ? $lowmark['title'] . " | " : '') . $lowmark['sitename'] ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="<?= htmlspecialchars($lowmark['description']) ?>">

    <link rel="canonical" href="<?= $lowmark['canonical_url'] ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/touch/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/touch/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/touch/favicon-16x16.png">
    <link rel="manifest" href="/assets/touch/site.manifest">
    
    <meta name="twitter:card" content="summary_large_image">
    <meta property="og:title" content="<?= htmlspecialchars($lowmark['title']) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($lowmark['description']) ?>">
    <meta property="og:image" content="<?= $lowmark['base_url'] . '/' . $lowmark['image']  ?>">
    <meta property="og:url" content="<?= $lowmark['base_url'] ?>">
    <meta property="og:type" content="website">
    
    <link href="/assets/css/lowmark_simple.css" rel="stylesheet">
    <?php if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) echo '<script src="/livereload/livereload.js" data-no-instant defer></script>' ?>
    <style>
    /* lowmark theme »simple« */
    /* Use lowmark_simple.css */
    /* Monochrome color set */

    :root,
    ::backdrop {
            /* Colors of default (light) theme */
            --bg: #fff;
            --logo-color: #212121;
            --accent-bg: #f5f5f5;
            --text: #212121;
            --text-light: #585858;
            --border: #898EA4;
            --accent: var(--text-light);
            --accent-text: var(--bg);
            --code: var(--text-light);
            --preformatted: #444;
            --marked: #ffdd33;
            --disabled: #efefef;
            --accent-hover: var(--text);
        }

    /* Colors of dark theme */
    @media (prefers-color-scheme: dark) {
        :root,
        ::backdrop {
            color-scheme: dark;
            --bg: #212121;
            --logo-color: #fff;
            --accent-bg: #37383e;
            --text: #dadadb;
            --text-light: #ababab;
            --code: var(--text-light);
            --preformatted: #ccc;
            --disabled: #111;
        }
    }
    </style>
    <?= ($lowmark ['highlight'] ? '<script defer src="/assets/js/highlight.js" onload="hljs.initHighlightingOnLoad()"></script>' . "\n" : '') ?>
    <?= ($lowmark ['highlight'] ? '<link href="/assets/css/highlight.css" rel="stylesheet">' . "\n" : '') ?>
</head>

<body>
    <header>
        <div class="sitename">
        	<a href="/">
        	    <svg style="width: 150px; height: auto;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 810 178" alt="Logo <?= $lowmark['sitename'] ?>">
                        <style>
                            .cls-1 {
                                fill: var(--logo-color);
                                stroke-width: 0px;
                            }
                        </style>
                        <path class="cls-1" d="M552.17,55.47c-20.3,0-56.18,10.87-58.29,56.75-2.1,45.88,19.02,59.67,40.75,59.67s44.75-19.22,44.75-19.22v19.22h29.83v-67.2s-5.49-49.21-57.05-49.21ZM535.16,143.29c-10.2,0-10.2-12.87-10.2-12.87v-18.2s1.08-26.14,27.22-26.14c23.68,0,27.22,22.81,27.22,26.14s-30.91,31.06-44.24,31.06ZM37.06,172.04H5V4.96h32.06v167.09ZM120.05,55.58c-32.44,0-58.73,26.3-58.73,58.73s26.3,58.73,58.73,58.73,58.73-26.3,58.73-58.73-26.3-58.73-58.73-58.73ZM120.05,142.83c-15.75,0-28.51-12.77-28.51-28.51s12.77-28.51,28.51-28.51,28.51,12.77,28.51,28.51-12.77,28.51-28.51,28.51ZM470.9,98.53v74.03h-30.76l.13-73.98s-2.55-10.71-14.38-10.69c-11.85.03-14.69,10.69-14.69,10.69l.08,73.98h-31.01l.03-73.9s-3.38-10.79-14.46-10.85c-11.08-.04-14.07,10.77-14.07,10.77v32.14s0,41.84-45.31,41.84c-18.66,0-29.43-11.59-29.43-11.59,0,0-9.22,11.59-30.55,11.59-45.11,0-45.11-41.84-45.11-41.84V63.26h30.76l-.13,67.42s2.53,10.71,14.38,10.68c11.84-.02,14.68-10.68,14.68-10.68l-.08-67.42h31.01l-.02,67.34s3.38,10.79,14.45,10.83c11.08.06,14.07-10.76,14.07-10.76v-32.15s0-41.83,45.32-41.83c18.66,0,29.42,11.58,29.42,11.58,0,0,9.24-11.58,30.56-11.58,45.11,0,45.11,41.83,45.11,41.83ZM693.04,86.08s-14.56,0-18.25,0c-7.92,0-10.05,7.2-10.05,9.89v75.92h-30.91v-78.12c0-11.23,9.38-36.91,33.68-36.91h25.53v29.22ZM744.56,98.84c17.17,0,29.22-14.16,29.22-23.84v-18.15h31.22v18.15c0,23.84-18.95,39.37-18.95,39.37,0,0,18.95,15.69,18.95,39.68s0,17.84,0,17.84h-31.22v-17.84c0-15.5-18.59-23.99-29.22-23.99v41.83h-31.83V56.86h31.83v41.98Z"/>
                    </svg>
        	</a>
        </div>
        <nav>
            <a href="/">Home</a>
            <a href="/toc.html">Inhalt</a>
            <a href="/about.html">Intro</a>
        </nav>
    </header>
    <div class="main">
        <main>
            <?= $lowmark['content'] ?>
        </main>
        <footer>
            <span><a href="/">Home</a></span>
            <span>| <a href="/toc.html">Inhalt</a></span>
            <span>| <a href="/imprint.html">Impressum</a></span>
            <span>| <a href="/privacy.html">Datenschutz</a></span>
            <span>| <a rel="me" href="https://kirche.social/@emk">Mastodon</a></span>
            <span>| <a href="<?= $md_file_path ?>" title="this page as markdown source file" rel="nofollow noopener noreferrer">md</a></span>
            <br><span>powered by <a href="https://lowmark.de" target="_blank">Lowmark</a></span>
        </footer>
    </div>
</body>
</html>
