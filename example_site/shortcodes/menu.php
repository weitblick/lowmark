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
 * Last updated: 2025-04-25
 */

function render_menu($menu, $current_uri) {
    $tree = []; // will hold the nested menu structure

    foreach ($menu as $path => $label) {
        $parts = explode('/', $path); // split path into folders (e.g., 'doku/index.html' → ['doku', 'index.html'])
        $current = &$tree;
        $full_path = '';
        $skip_nesting = false;

        // Check if any parent path exists in the menu
        // If not, we should skip nesting and place the item at top level
        foreach ($parts as $i => $part) {
            if ($i === count($parts) - 1) {
                break; // skip the last part (the file)
            }

            $full_path .= $part . '/'; // reconstruct path, e.g., 'doku/'

            // If the parent is not defined in the menu, mark to skip nesting
            if (!isset($menu[$full_path])) {
                $skip_nesting = true;
            } else {
                $skip_nesting = false; // parent exists, allow nesting
                break;
            }
        }

        if ($skip_nesting) {
            // If nesting is to be skipped, add directly to the root of the tree
            $tree[$path] = $label;
            continue;
        }

        // Build nested tree structure
        $current = &$tree;
        foreach ($parts as $i => $part) {
            if ($i === count($parts) - 1) {
                // Last part → this is the actual file
                $current[$path] = $label;
            } else {
                // Intermediate part → this is a folder
                $folder = $part . '/';
                if (!isset($current[$folder])) {
                    $current[$folder] = [];
                }
                $current = &$current[$folder]; // go one level deeper
            }
        }
    }

    // Delegate rendering to HTML builder function
    return render_menu_html($tree, $menu, $current_uri);
}

function render_menu_html($tree, $flat_menu, $current_uri, $parent_path = '') {
    $html = "<ul>\n";

    foreach ($tree as $key => $value) {
        $is_folder = is_array($value); // if it's an array, it's a folder with children
        $classes = [];
        $label = '';
        $href = '#';

        if ($is_folder) {
            // Try to find the label of the folder (if defined)
            $folder_label = $flat_menu[$key] ?? null;

            // Also check if there's an index.html file for this folder
            $index_key = $key . 'index.html';
            $has_index = isset($flat_menu[$index_key]);

            // If neither label nor index exists, skip this folder (it's empty)
            if ($folder_label === null && !$has_index) {
                continue;
            }

            // Use the folder label if available
            if ($folder_label !== null) {
                $label = $folder_label;
            }

            // Highlight folder if current URI is inside it
            if (str_starts_with($current_uri, $key)) {
                $classes[] = 'active-parent';
            }

            // Start folder list item
            $class_attr = $classes ? ' class="' . implode(' ', $classes) . '"' : '';
            $html .= "  <li$class_attr><a href=\"#\">" . htmlspecialchars($label) . "</a>\n";
            $html .= "    <ul>\n";

            // Optionally add index.html as first child item
            if ($has_index) {
                $index_label = $flat_menu[$index_key];
                $active = ($current_uri === $index_key) ? 'active' : '';
                $active_class = $active ? ' class="' . $active . '"' : '';
                $html .= "      <li$active_class><a href=\"/" . htmlspecialchars($index_key) . "\">" . htmlspecialchars($index_label) . "</a></li>\n";
            }

            // Copy subitems and remove index + self to avoid duplication
            $subitems = $value;
            unset($subitems[$index_key]); // don't repeat index.html
            unset($subitems[$key]);       // don't repeat folder as page

            if (!empty($subitems)) {
                // Recursively render subitems, but remove outer <ul> layer from recursion
                $html .= substr(render_menu_html($subitems, $flat_menu, $current_uri, $key), 5, -6);
            }

            $html .= "    </ul>\n";
            $html .= "  </li>\n";

        } else {
            // Regular flat menu entry (no nesting)
            $label = $value;
            $href = $key;
            $active = ($href === $current_uri) ? 'active' : '';
            $class_attr = $active ? ' class="' . $active . '"' : '';
            $html .= "  <li$class_attr><a href=\"/" . htmlspecialchars($href) . "\">" . htmlspecialchars($label) . "</a></li>\n";
        }
    }

    $html .= "</ul>\n";
    return $html;
}

?>
