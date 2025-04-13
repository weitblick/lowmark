<?php
/*


	██       ██████  ██     ███    ███  █████  ██████  ██   ██ 
	██      ██    ██ ██     ████  ████ ██   ██ ██   ██ ██  ██  
	██      ██    ██ ██  █  ██ ████ ██ ███████ ██████  █████   
	██      ██    ██ ██ ███ ██  ██  ██ ██   ██ ██   ██ ██  ██  
	███████  ██████   ███ ████      ██ ██   ██ ██   ██ ██   ██ 
                                                                                                                          
	LOWMARK – A Low-tech Markdown Website Generator
	Version: 0.31 (2025-04-13)
	https://lowmark.de
	
	by Erhard Maria Klein
	emk@lowmark.de
	CC BY-NC-SA 4.0
	
*/

// ============ LOWMARK COMPONENTS ================

// expand <img> tags to <figure><img><figcaption></figcaption></figure> and add lazy loading an alignment
function img_to_figure($html) {
    $html = preg_replace('/<p>(<img\s+[^>]+>)<\/p>/', '$1', $html); // Remove enclosing <p> tags if necessary

    // Replacing the <img> tag with <figure> tags
    $pattern = '/<img\s+([^>]*)>/';
    $html = preg_replace_callback($pattern, function($matches) {
        $imgTag = $matches[0];
        $attributes = $matches[1];
        $align = '';

        // Check for alignment in alt attribute
        if (preg_match('/alt=":((left|right|center)(\s*))/', $attributes, $altMatches)) {
            $attributes = str_replace(':' . $altMatches[1], '', $attributes); // Remove the alignment part from the alt attribute
            $align = trim($altMatches[1]); // align without trailing spaces
            $imgTag = '<img ' . $attributes . '>'; // Rebuild the img tag with modified attributes
        }

        // Add loading="lazy"
        $imgTag = preg_replace('/\s*\/>$/', ' loading="lazy" />', $imgTag);

        // Build the figure tag
        $figureTag = '<figure';
        if ($align) {
            $figureTag .= ' class="lowmark-' . $align . '"';
        }
        $figureTag .= ">$imgTag";

        // Use the title attribute for <figcaption> - if available
        if (preg_match('/title="([^"]*)"/', $attributes, $titleMatches)) {
            $caption = $titleMatches[1];
            $caption = html_entity_decode(html_entity_decode($caption, ENT_QUOTES), ENT_QUOTES); // prevent double encoding of special characters
            $figureTag .= "<figcaption>$caption</figcaption>";
        }
        $figureTag .= "</figure>";

        return $figureTag;
    }, $html);

    return $html;
}

// Replace internal links to *.md with *.html and extend external links with target="_blank"
function extend_links($content) {
    $pattern = '/<a\s+(.*?)href=["\'](.*?\.md)["\'](.*?)>(.*?)<\/a>/i'; // Regular expression to identify internal .md links
    $content = preg_replace_callback($pattern, function($matches) {
        $tag = $matches[0];
        $attributes = $matches[1];
        $href = $matches[2];
        $rest = $matches[3];
        $linkText = $matches[4];
        
        if (strpos($href, '://') === false) { // Check that the path is local
            $newHref = str_replace('.md', '.html', $href); // Replace *.md with *.html
            $newTag = "<a $attributes href=\"$newHref\"$rest>$linkText</a>"; // Create the new <a> tag
            return $newTag;
        } else {
            return $tag; // do nothing if link is external
        }
    }, $content);
    
    // Regular expression to extend the <a> tags with target="_blank" if they are external
    $pattern = '/<a\s+([^>]*)href=(["\'])(.*?)(?:\\2)([^>]*)>/i';

    $content = preg_replace_callback($pattern, function($matches) {
        $attributes = $matches[1];
        $href = $matches[3];
        $rest = $matches[4];
        if (strpos($href, '://') !== false) { // Check whether the link is an external URL (contains ://)
            return "<a$attributes href=\"$href\" target=\"_blank\"$rest>"; // Add target="_blank"
        } else {
            return $matches[0]; // No changes required
        }
    }, $content);
    
    return $content;
}

// Find all email addresses in the content and call email encoding function
function mail_encode($content) {
    preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $content, $matches);

    // Iterate through each found email address and replace them with the cloaked version
    foreach ($matches[0] as $address) {
        $content = str_replace($address, generateCloakedEmailLink($address), $content);
    }

    return $content;
}

// Email encoding
function generateCloakedEmailLink($address) {

    // Get user and domain parts of the email address
    $parts = explode("@", $address);
    $user = $parts[0];
    $domain = isset($parts[1]) ? $parts[1] : "";

    $fingerprint = md5($address . "mailto" . str_shuffle(implode(range(0, 999)))[0]); // Compute md5 fingerprint

    // Generate cloaked email span
    $userReversed = strrev($user);
    $domainReversed = strrev($domain);
    $userChars = str_split($userReversed);
    $domainChars = str_split($domainReversed);
    $spanAttributes = 'data-user="' . implode('', $userChars) . '"';
    if (!empty($domain)) {
        $spanAttributes .= ' data-domain="' . implode('', $domainChars) . '"';
    }
    $spanElement = '<span class="cloaked-e-mail" ' . $spanAttributes . '></span>';

    // Generate JavaScript code
    $scriptCode = <<<EOD
<script id="$fingerprint">
      var scriptTag = document.getElementById("$fingerprint");
      var wblink = document.createElement("a");
      var address = "$userReversed".split('').reverse().join('') + "@" + "$domainReversed".split('').reverse().join('');
      wblink.href = "mailto:" + address;
      wblink.innerText = address;
      scriptTag.parentElement.insertBefore(wblink, scriptTag.previousElementSibling);
      scriptTag.parentElement.removeChild(scriptTag.previousElementSibling);
</script>
EOD;

    return $spanElement . $scriptCode;
}

// add unique ids to headlines
function headline_ids($content, $totop) {
    // Regex pattern to match headline tags
    $pattern = '/<h([1-6])>(.*?)<\/h\1>/i';
    $headlines = [];

    // Function to generate a valid ID from a headline
    $generate_id = function($headline) {
        // Convert to lowercase
        $headline = strtolower($headline);
        // Transliterate German umlauts and ß
        $translit = [
            'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss'
        ];
        $headline = strtr($headline, $translit);
        // Remove special characters and replace spaces with hyphens
        $id = preg_replace('/[^a-z0-9]+/i', '-', trim($headline));
        // Ensure the ID is unique by appending a suffix if necessary
        static $ids = [];
        $original_id = $id;
        $count = 1;
        while (isset($ids[$id])) {
            $id = $original_id . '-' . $count++;
        }
        $ids[$id] = true;
        return $id;
    };

    // Callback function to replace headline tags with IDs
    $callback = function($matches) use (&$headlines, $generate_id, $totop) {
        $level = $matches[1];
        $headline = $matches[2];
        $id = $generate_id($headline);
        $headlines[$id] = $headline;
        $content = '';
        if (!empty($totop)) $content .= $totop ."\n";
        $content .= "<h{$level} id=\"{$id}\">{$headline}</h{$level}>";

        return $content;
    };

    // Replace headline tags with IDs
    $content = preg_replace_callback($pattern, $callback, $content);

    return ['content' => $content, 'headlines' => $headlines];
}

// Add <details> as a workaround with HTML comments
function details_patch($content) {
    $content = preg_replace('/<!-- DETAILS (.*) -->/', "<details><summary>$1</summary>", $content); // replace the DETAILS HTML comment (start)
    $content = str_replace("<!-- /DETAILS -->", "</details>", $content); // replace the DETAILS HTML comment (end)
    return $content;
}
?>
