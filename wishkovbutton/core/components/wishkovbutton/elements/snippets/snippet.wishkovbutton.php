<?php
/**
 * WishkovButton snippet: the "В вишлист" link for the current resource.
 * Properties: theme, size, label, iconOnly, margin, url, ref. Defaults come from the wishkovbutton.* system settings.
 *
 * @var modX $modx
 * @var array $scriptProperties
 */
$src = rtrim($modx->getOption('core_path', null, MODX_CORE_PATH), '/') . '/components/wishkovbutton/src/';
require_once $src . 'WishkovButton.php';
require_once $src . 'Snippet.php';

$system = [];
foreach (['theme' => 'fill', 'size' => 'm', 'label' => 'В вишлист', 'icon_only' => '0', 'margin' => '8'] as $key => $default) {
    $system[$key] = $modx->getOption('wishkovbutton.' . $key, null, $default);
}

$url = isset($scriptProperties['url']) ? trim((string) $scriptProperties['url']) : '';
// The page title goes only with the page's own address, never with a foreign &url.
$ownPage = !preg_match('#^https?://#i', $url);
if ($ownPage) {
    $url = $modx->resource ? (string) $modx->makeUrl($modx->resource->get('id'), '', '', 'full') : '';
}
if ($url === '') {
    return '';
}
$title = $ownPage && $modx->resource ? (string) $modx->resource->get('pagetitle') : '';
$ref = isset($scriptProperties['ref']) && $scriptProperties['ref'] !== '' ? (string) $scriptProperties['ref'] : 'modx';

return \Wishkov\Modx\Snippet::html(\Wishkov\Modx\Snippet::settings($scriptProperties, $system), $url, $title, $ref);
