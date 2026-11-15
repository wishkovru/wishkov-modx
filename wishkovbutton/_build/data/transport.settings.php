<?php
/**
 * System settings of the transport package. Included by build.transport.php ($modx, $wkClass are in scope).
 * Key, value, xtype, namespace, area; descriptions come from lexicon setting_<key> and setting_<key>_desc.
 */
$settings = [];

$definitions = [
    'wishkovbutton.theme' => ['fill', 'textfield'],
    'wishkovbutton.size' => ['m', 'textfield'],
    'wishkovbutton.label' => ['В вишлист', 'textfield'],
    'wishkovbutton.icon_only' => ['0', 'combo-boolean'],
    'wishkovbutton.margin' => ['8', 'textfield'],
];

foreach ($definitions as $key => $def) {
    $settings[$key] = $modx->newObject($wkClass['setting']);
    $settings[$key]->fromArray([
        'key' => $key,
        'value' => $def[0],
        'xtype' => $def[1],
        'namespace' => 'wishkovbutton',
        'area' => 'wishkovbutton',
    ], '', true, true);
}

return $settings;
