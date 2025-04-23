<?php
/**
 * Shortcode:    menu
 * Type:         Theme shortcode
 * Description:  Render Site Menu
 * Usage:        Insert in your template: <?= render_menu($lowmark['menu'], $uri) ?>
 *               Menu Configuration in config.php, e.g.
 *               'menu' => [
 *                             'index.html'   => 'Home',
 *                             'example.html' => 'Example Page',
 *                             'sub/'         => 'Submenu',
 *                             'sub/sub.html' => 'Subpage',
 *                         ],
 *
 * Installation: Copy this file into the shortcodes/ folder
 * Depends on:   Lowmark (https://lowmark.de)
 * Part of:      Theme "Lowmark Simple Theme"
 *
 * Author:       Erhard Maria Klein (https://lowmark.de)
 * Version:      0.5
 * Last updated: 2025-04-23
 */

function render_menu($menu, $current_uri) {
    $tree = [];

    // Convert flat menu array into a nested structure
    foreach ($menu as $path => $label) {
        $parts = explode('/', $path);
        $current = &$tree;

        foreach ($parts as $i => $part) {
            if ($i === count($parts) - 1) {
                // Assign label to full path
                $current[$path] = $label;
            } else {
                $folder = $part . '/';
                if (!isset($current[$folder])) {
                    $current[$folder] = [];
                }
                $current = &$current[$folder];
            }
        }
    }

    return render_menu_html($tree, $menu, $current_uri);
}

function render_menu_html($tree, $flat_menu, $current_uri, $parent_path = '') {
    $html = "<ul>\n";

    foreach ($tree as $key => $value) {
        $is_folder = is_array($value);

        // Label is either from the flat menu, or fallback to empty
        $label = $is_folder ? ($flat_menu[$key] ?? '') : $value;

        // Determine href
        if ($is_folder) {
            // If folder has an index.html entry, use that
            $index_key = $key . 'index.html';
            if (isset($flat_menu[$index_key])) {
                $href = $index_key;
                $label = $flat_menu[$index_key];
            } elseif (isset($flat_menu[$key])) {
                $href = '#'; // No real page, just menu entry
                $label = $flat_menu[$key];
            } else {
                continue; // Skip folders not explicitly defined
            }
        } else {
            $href = $key;
        }

        // Active class logic
        $active = ($href === $current_uri) ? 'active' : '';
        $active_parent = ($is_folder && str_starts_with($current_uri, $key)) ? 'active-parent' : '';
        $classes = trim("$active $active_parent");
        $class_attr = $classes ? ' class="' . $classes . '"' : '';

        // Render list item
        $html .= "  <li$class_attr><a href=\"/" . htmlspecialchars($href) . "\">" . htmlspecialchars($label) . "</a>";

        if ($is_folder) {
            // Exclude folder itself from children if already rendered in flat menu
            $subitems = $value;

            // Remove the folder key from its own children, if it exists (to prevent duplication)
            if (isset($subitems[$key])) {
                unset($subitems[$key]);
            }

            // Only render submenu if children remain
            if (!empty($subitems)) {
                $html .= "\n" . render_menu_html($subitems, $flat_menu, $current_uri, $key) . "  ";
            }
        }

        $html .= "</li>\n";
    }

    $html .= "</ul>\n";
    return $html;
}
?>
