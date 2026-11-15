<?php
/**
 * Snippet list of the transport package. Included by build.transport.php ($modx, $sources, $wkClass are in scope).
 */

/** Snippet source without the PHP tags (MODX stores snippet code without them). */
function wishkovbuttonSnippetContent($file)
{
    $content = file_get_contents($file);
    $content = preg_replace('/^<\?php\s*/', '', $content);
    $content = preg_replace('/\?>\s*$/', '', $content);
    return trim($content);
}

$snippets = [];

$snippets[1] = $modx->newObject($wkClass['snippet']);
$snippets[1]->fromArray([
    'id' => 1,
    'name' => 'WishkovButton',
    'description' => 'The "В вишлист" button for the current resource: saves the page to a wishlist on wishkov.ru.',
    'snippet' => wishkovbuttonSnippetContent($sources['snippets'] . 'snippet.wishkovbutton.php'),
], '', true, true);

$properties = include $sources['properties'] . 'properties.wishkovbutton.php';
$snippets[1]->setProperties($properties);
unset($properties);

return $snippets;
