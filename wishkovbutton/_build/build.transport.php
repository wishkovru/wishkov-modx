<?php
/**
 * Builds the WishkovButton transport package: category with the snippet, five system settings,
 * the core/components/wishkovbutton files (src, lexicon, docs) and the license/readme/changelog attributes.
 * Result: MODX_CORE_PATH/packages/wishkovbutton-<version>-<release>.transport.zip
 * Follows the docs.modx.com tutorial "Developing an Extra".
 */
$tstart = microtime(true);
set_time_limit(0);

require_once __DIR__ . '/build.config.php';

/* MODX 3: PSR-4 classes from core/vendor/autoload.php; MODX 2.8: core/model/modx/modx.class.php. */
if (is_file(MODX_CORE_PATH . 'vendor/autoload.php')) {
    require_once MODX_CORE_PATH . 'vendor/autoload.php';
}
if (class_exists('MODX\\Revolution\\modX')) {
    $modx = new \MODX\Revolution\modX();
    $wkClass = [
        'builder' => \MODX\Revolution\Transport\modPackageBuilder::class,
        'category' => \MODX\Revolution\modCategory::class,
        'snippet' => \MODX\Revolution\modSnippet::class,
        'setting' => \MODX\Revolution\modSystemSetting::class,
        'transport' => \xPDO\Transport\xPDOTransport::class,
    ];
} else {
    require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
    $modx = new modX();
    $wkClass = [
        'builder' => 'modPackageBuilder',
        'category' => 'modCategory',
        'snippet' => 'modSnippet',
        'setting' => 'modSystemSetting',
        'transport' => 'xPDOTransport',
    ];
}
$modx->initialize('mgr');
$modx->setLogLevel($modx::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');
$modx->log($modx::LOG_LEVEL_INFO, 'Packaging ' . PKG_NAME . ' ' . PKG_VERSION . '-' . PKG_RELEASE
    . ' with ' . $modx->getVersionData()['full_appname']);

if ($wkClass['builder'] === 'modPackageBuilder') {
    $modx->loadClass('transport.modPackageBuilder', '', false, true);
}
/** xPDOTransport attribute names, the same strings on MODX 2.8 and 3. */
$t = static function ($name) use ($wkClass) {
    return constant($wkClass['transport'] . '::' . $name);
};
$builder = new $wkClass['builder']($modx);
$builder->createPackage(PKG_NAME_LOWER, PKG_VERSION, PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER, false, true, '{core_path}components/' . PKG_NAME_LOWER . '/');

/* category with the snippet */
$category = $modx->newObject($wkClass['category']);
$category->set('id', 1);
$category->set('category', PKG_NAME);

$snippets = include $sources['data'] . 'transport.snippets.php';
if (!is_array($snippets) || !$snippets) {
    $modx->log($modx::LOG_LEVEL_FATAL, 'No snippets in transport.snippets.php');
}
$category->addMany($snippets);
$modx->log($modx::LOG_LEVEL_INFO, 'Packaged in ' . count($snippets) . ' snippet(s).');

$attr = [
    $t('UNIQUE_KEY') => 'category',
    $t('PRESERVE_KEYS') => false,
    $t('UPDATE_OBJECT') => true,
    $t('RELATED_OBJECTS') => true,
    $t('RELATED_OBJECT_ATTRIBUTES') => [
        'Snippets' => [
            $t('PRESERVE_KEYS') => false,
            $t('UPDATE_OBJECT') => true,
            $t('UNIQUE_KEY') => 'name',
        ],
    ],
];
$vehicle = $builder->createVehicle($category, $attr);

/* files: core/components/wishkovbutton (src, elements, lexicon, docs) */
$vehicle->resolve('file', [
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
]);
$builder->putVehicle($vehicle);
$modx->log($modx::LOG_LEVEL_INFO, 'Packaged in the category vehicle and the core files.');

/* system settings: the administrator's own values survive an update (UPDATE_OBJECT false) */
$settings = include $sources['data'] . 'transport.settings.php';
if (!is_array($settings) || !$settings) {
    $modx->log($modx::LOG_LEVEL_FATAL, 'No settings in transport.settings.php');
}
$settingAttr = [
    $t('UNIQUE_KEY') => 'key',
    $t('PRESERVE_KEYS') => true,
    $t('UPDATE_OBJECT') => false,
];
foreach ($settings as $setting) {
    $builder->putVehicle($builder->createVehicle($setting, $settingAttr));
}
$modx->log($modx::LOG_LEVEL_INFO, 'Packaged in ' . count($settings) . ' system setting(s).');

/* package attributes shown in Package Management */
$builder->setPackageAttributes([
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
    'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'),
]);
$modx->log($modx::LOG_LEVEL_INFO, 'Set package attributes (docs/license.txt, docs/readme.txt, docs/changelog.txt).');

$builder->pack();

$tend = microtime(true);
$modx->log($modx::LOG_LEVEL_INFO, sprintf('Package built: %s seconds', round($tend - $tstart, 3)));
