<?php
/**
 * Build configuration of the WishkovButton transport package.
 * Run inside a MODX installation: php _build/build.transport.php.
 * MODX_BASE_PATH in the environment points at the MODX root (default /var/www/html/): its config.core.php
 * defines MODX_CORE_PATH and MODX_CONFIG_KEY, core/config/config.inc.php defines the other paths.
 */
define('PKG_NAME', 'WishkovButton');
define('PKG_NAME_LOWER', 'wishkovbutton');
define('PKG_VERSION', '1.0.0');
define('PKG_RELEASE', 'pl');

$wkBase = rtrim(getenv('MODX_BASE_PATH') ?: '/var/www/html/', '/') . '/';
if (!is_file($wkBase . 'config.core.php')) {
    fwrite(STDERR, "No config.core.php in $wkBase: set MODX_BASE_PATH to the MODX root.\n");
    exit(1);
}
require_once $wkBase . 'config.core.php';

$root = dirname(__DIR__) . '/';
$sources = [
    'root' => $root,
    'build' => $root . '_build/',
    'data' => $root . '_build/data/',
    'properties' => $root . '_build/properties/',
    'source_core' => $root . 'core/components/' . PKG_NAME_LOWER,
    'snippets' => $root . 'core/components/' . PKG_NAME_LOWER . '/elements/snippets/',
    'docs' => $root . 'core/components/' . PKG_NAME_LOWER . '/docs/',
];
