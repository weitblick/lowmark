<?php
/**
 * Shortcode:    details
 * Type:         Inline shortcode
 * Description:  Adds <details><summary>…</summary>…</details> block
 * Usage:        <!-- [details "summary"] --> … <!-- [/details] -->
 *
 * Installation: Copy this file into the shortcodes/ folder
 * Depends on:   Lowmark (https://lowmark.de)
 * Part of:      Theme "Lowmark Simple Theme"
 *
 * Author:       Erhard Maria Klein (https://lowmark.de)
 * Version:      0.5
 * Last updated: 2025-04-23
 */

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
?>
