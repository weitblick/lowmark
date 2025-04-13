<?php

// ============ CUSTOM CODE LOEWENSTEIN ================

function has_prayers($content) { // NOT USED
    $has_prayers = false;

    // Ersetze "## Fürbitten" und speichere das Ergebnis
    $new_content = preg_replace('/## Fürbitten/', "## Fürbitten{#fuerbitten}", $content);

    // Prüfe, ob eine Ersetzung stattgefunden hat
    if ($new_content !== $content) {
        $has_prayers = true;
        $content = $new_content; // Aktualisiere den Inhalt
    }

    return ['has_prayers' => $has_prayers, 'content' => $content];

}

?>
