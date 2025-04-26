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
 * Version:      0.5
 * Last updated: 2025-04-26
 *
 * Copyright (c) 2025 Erhard Maria Klein, lowmark.de
 * Licensed under the GNU General Public License v3.0 or later
 * See https://www.gnu.org/licenses/gpl-3.0.html
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
