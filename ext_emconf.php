<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "mbi_products_categories".
 *
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Shop Categories',
    'description' => 'Hierarchical categories for tt_products. Works with tt_products and other tables.',
    'category' => 'misc',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'author' => 'Franz Holzinger',
    'author_email' => 'franz@ttproducts.de',
    'author_company' => '',
    'version' => '0.7.7',
    'constraints' => array(
        'depends' => array(
            'div2007' => '1.10.0-0.0.0',
            'php' => '5.5.0-7.3.99',
            'typo3' => '6.2.0-9.5.99',
        ),
        'suggests' => array(
            'tt_products' => '2.9.4-3.99.99',
            'typo3db_legacy' => '1.0.0-1.1.99',
        ),
        'conflicts' => array(
        ),
    ),
);

