<?php

return [
    '__name' => 'admin-product',
    '__version' => '0.2.1',
    '__git' => 'git@github.com:getmim/admin-product.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'http://iqbalfn.com/'
    ],
    '__files' => [
        'modules/admin-product' => ['install','update','remove'],
        'theme/admin/product' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [
            [
                'admin' => NULL
            ],
            [
                'product' => NULL
            ],
            [
                'lib-form' => NULL
            ],
            [
                'lib-pagination' => NULL
            ],
            [
                'lib-upload' => NULL
            ],
            [
                'admin-site-meta' => NULL
            ],
            [
                'lib-formatter' => NULL
            ]
        ],
        'optional' => []
    ],
    'autoload' => [
        'classes' => [
            'AdminProduct\\Controller' => [
                'type' => 'file',
                'base' => 'modules/admin-product/controller'
            ],
            'AdminProduct\\Library' => [
                'type' => 'file',
                'base' => 'modules/admin-product/library'
            ]
        ],
        'files' => []
    ],
    'routes' => [
        'admin' => [
            'adminProduct' => [
                'path' => [
                    'value' => '/product'
                ],
                'method' => 'GET',
                'handler' => 'AdminProduct\\Controller\\Product::index'
            ],
            'adminProductEdit' => [
                'path' => [
                    'value' => '/product/(:id)',
                    'params' => [
                        'id' => 'number'
                    ]
                ],
                'method' => 'GET|POST',
                'handler' => 'AdminProduct\\Controller\\Product::edit'
            ],
            'adminProductRemove' => [
                'path' => [
                    'value' => '/product/(:id)/remove',
                    'params' => [
                        'id' => 'number'
                    ]
                ],
                'method' => 'GET',
                'handler' => 'AdminProduct\\Controller\\Product::remove'
            ]
        ]
    ],
    'adminUi' => [
        'sidebarMenu' => [
            'items' => [
                'product' => [
                    'label' => 'Product',
                    'icon' => '<i class="fas fa-box-open"></i>',
                    'priority' => 0,
                    'filterable' => FALSE,
                    'children' => [
                        'all-product' => [
                            'label' => 'All Product',
                            'icon' => '<i></i>',
                            'route' => ['adminProduct'],
                            'perms' => 'manage_product'
                        ]
                    ]
                ]
            ]
        ]
    ],
    'admin' => [
        'objectFilter' => [
            'handlers' => [
                'product' => 'AdminProduct\\Library\\Filter'
            ]
        ]
    ],
    'libForm' => [
        'forms' => [
            'admin.product.edit' => [
                '@extends' => ['std-site-meta','std-cover'],
                'name' => [
                    'label' => 'Name',
                    'type' => 'text',
                    'rules' => [
                        'required' => TRUE
                    ]
                ],
                'slug' => [
                    'label' => 'Slug',
                    'type' => 'text',
                    'slugof' => 'name',
                    'rules' => [
                        'required' => TRUE,
                        'empty' => FALSE,
                        'unique' => [
                            'model' => 'Product\\Model\\Product',
                            'field' => 'slug',
                            'self' => [
                                'service' => 'req.param.id',
                                'field' => 'id'
                            ]
                        ]
                    ]
                ],
                'status' => [
                    'label' => 'Status',
                    'type' => 'select',
                    'rules' => [
                        'required' => true
                    ]
                ],
                'content' => [
                    'label' => 'Description',
                    'type' => 'summernote',
                    'rules' => [
                        'required' => true
                    ]
                ],
                'gallery' => [
                    'label' => 'Gallery',
                    'type' => 'image-list',
                    'form' => 'std-image',
                    'rules' => []
                ],
                'meta-schema' => [
                    'options' => [
                        'Product'          => 'Product',
                        'Vehicle'          => 'Vehicle',
                        'BusOrCoach'       => 'BusOrCoach',
                        'Car'              => 'Car',
                        'Motorcycle'       => 'Motorcycle',
                        'MotorizedBicycle' => 'MotorizedBicycle'
                    ]
                ]
            ],
            'admin.product.index' => [
                'q' => [
                    'label' => 'Search',
                    'type' => 'search',
                    'nolabel' => TRUE,
                    'rules' => []
                ],
                'status' => [
                    'label' => 'Status',
                    'type' => 'select',
                    'nolabel' => TRUE,
                    'options' => ['All','Draft','Published'],
                    'rules' => []
                ]
            ]
        ]
    ]
];
