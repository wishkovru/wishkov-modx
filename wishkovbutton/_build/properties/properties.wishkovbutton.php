<?php
/**
 * Properties of the WishkovButton snippet. Empty value means "use the system setting".
 * Descriptions are lexicon keys of the wishkovbutton:properties topic.
 */
$properties = [];

$properties[] = [
    'name' => 'theme',
    'desc' => 'wishkovbutton.prop_theme_desc',
    'type' => 'list',
    'options' => [
        ['text' => 'fill', 'value' => 'fill'],
        ['text' => 'outline', 'value' => 'outline'],
        ['text' => 'soft', 'value' => 'soft'],
    ],
    'value' => '',
    'lexicon' => 'wishkovbutton:properties',
];
$properties[] = [
    'name' => 'size',
    'desc' => 'wishkovbutton.prop_size_desc',
    'type' => 'list',
    'options' => [
        ['text' => 'm', 'value' => 'm'],
        ['text' => 's', 'value' => 's'],
    ],
    'value' => '',
    'lexicon' => 'wishkovbutton:properties',
];
$properties[] = [
    'name' => 'label',
    'desc' => 'wishkovbutton.prop_label_desc',
    'type' => 'textfield',
    'options' => '',
    'value' => '',
    'lexicon' => 'wishkovbutton:properties',
];
$properties[] = [
    'name' => 'iconOnly',
    'desc' => 'wishkovbutton.prop_iconOnly_desc',
    'type' => 'textfield',
    'options' => '',
    'value' => '',
    'lexicon' => 'wishkovbutton:properties',
];
$properties[] = [
    'name' => 'margin',
    'desc' => 'wishkovbutton.prop_margin_desc',
    'type' => 'textfield',
    'options' => '',
    'value' => '',
    'lexicon' => 'wishkovbutton:properties',
];
$properties[] = [
    'name' => 'url',
    'desc' => 'wishkovbutton.prop_url_desc',
    'type' => 'textfield',
    'options' => '',
    'value' => '',
    'lexicon' => 'wishkovbutton:properties',
];
$properties[] = [
    'name' => 'ref',
    'desc' => 'wishkovbutton.prop_ref_desc',
    'type' => 'textfield',
    'options' => '',
    'value' => '',
    'lexicon' => 'wishkovbutton:properties',
];

return $properties;
