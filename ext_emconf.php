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
    'version' => '0.8.1',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-12.4.99'
        ],
        'suggests' => [
            'tt_products' => '2.14.1-3.9.99',
            'typo3db_legacy' => '1.0.0-1.99.99',
        ],
        'conflicts' => [
        ],
    ],
];
