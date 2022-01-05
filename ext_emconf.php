<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "mbi_products_categories".
 *
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'Shop Categories',
    'description' => 'Hierarchical categories for tt_products. Works with tt_products and other tables.',
    'category' => 'misc',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'author' => 'Franz Holzinger',
    'author_email' => 'franz@ttproducts.de',
    'author_company' => '',
    'version' => '0.7.12',
    'constraints' => [
        'depends' => [
            'div2007' => '1.10.0-0.0.0',
            'php' => '7.3.0-7.4.99',
            'typo3' => '9.5.0-11.5.99',
        ],
        'suggests' => [
            'tt_products' => '2.9.20-3.99.99',
            'typo3db_legacy' => '1.0.0-1.99',
        ],
        'conflicts' => [
        ],
    ],
];

