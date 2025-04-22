<?php
function details_shortcode($attributes) {
    // If this is a closing tag, return closing HTML only
    if (!empty($attributes['end'])) {
        return '</details>';
    }

    // Check for 'summary' either as named or positional attribute
    $summary = $attributes['summary'] ?? $attributes['attribute_1'] ?? '';

    // Prepare summary tag if provided
    $summary_html = $summary !== '' ? '<summary>' . htmlspecialchars($summary) . '</summary>' : '';

    return '<details>' . $summary_html;
}
