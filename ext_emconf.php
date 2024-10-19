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
    'author' => 'Franz Holzinger',
    'author_email' => 'franz@ttproducts.de',
    'author_company' => '',
    'version' => '0.10.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-13.4.99'
        ],
        'suggests' => [
            'tt_products' => '2.15.0-3.9.99',
            'typo3db_legacy' => '1.0.0-1.99.99',
        ],
        'conflicts' => [
        ],
    ],
];
