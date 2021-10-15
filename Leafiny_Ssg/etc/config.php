<?php

$config = [
    'model' => [
        'admin_cache' => [
            'cache' => [
                'ssg' => [
                    'name'        => 'SSG',
                    'description' => 'Static Site Generator',
                    'flush'       => '/admin/*/ssg/generate/',
                    'active'      => 'helper.ssg.enabled'
                ],
            ],
        ],
    ],

    'helper' => [
        'ssg' => [
            'class'     => Ssg_Helper_Data::class,
            'directory' => 'static',
            'generator' => [
                'entities'  => [
                     'category' => [
                         'identifier' => 'path_key',
                         'language'   => 'language',
                         'filters' => [
                             [
                                 'column' => 'status',
                                 'value'  => 1,
                             ],
                         ],
                     ],
                     'cms_page' => [
                         'identifier' => 'path_key',
                         'language'   => 'language',
                         'filters' => [
                             [
                                 'column' => 'status',
                                 'value'  => 1,
                             ],
                         ],
                     ],
                     'blog_post' => [
                         'identifier' => 'path_key',
                         'language'   => 'language',
                         'filters' => [
                             [
                                 'column' => 'status',
                                 'value'  => 1,
                             ],
                         ],
                     ],
                     'catalog_product' => [
                         'identifier' => 'path_key',
                         'language'   => 'language',
                         'filters' => [
                             [
                                 'column' => 'status',
                                 'value'  => 1,
                             ],
                         ],
                     ],
                ],
            ],
        ],
    ],

    'page' => [
        '/admin/*/ssg/generate/' => [
            'class'    => Ssg_Page_Generate::class,
            'template' => null,
        ],
    ],

    'events' => [
        'category_save_after' => [
            'generate_category' => 5000,
        ],
        'cms_page_save_after' => [
            'generate_cms_page' => 5000,
        ],
        'blog_post_save_after' => [
            'generate_blog_post' => 5000,
        ],
        'catalog_product_save_after' => [
            'generate_catalog_product' => 5000,
        ],
    ],

    'observer' => [
        'generate_category' => [
            'class'       => Ssg_Observer_Generate::class,
            'identifier'  => 'path_key',
            'language'    => 'language',
            'entity_type' => 'category',
        ],
        'generate_cms_page' => [
            'class'       => Ssg_Observer_Generate::class,
            'identifier'  => 'path_key',
            'language'    => 'language',
            'entity_type' => 'cms_page',
        ],
        'generate_blog_post' => [
            'class'       => Ssg_Observer_Generate::class,
            'identifier'  => 'path_key',
            'language'    => 'language',
            'entity_type' => 'blog_post',
        ],
        'generate_catalog_product' => [
            'class'       => Ssg_Observer_Generate::class,
            'identifier'  => 'path_key',
            'language'    => 'language',
            'entity_type' => 'catalog_product',
        ],
    ],
];
