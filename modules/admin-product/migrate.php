<?php 

return [
    'LibUserPerm\\Model\\UserPerm' => [
        'data' => [
            'name' => [
                'product_create'      => ['group'=>'Product','about'=>'Allow user to create own product'],
                'manage_product'      => ['group'=>'Product','about'=>'Allow user to manage own products'],
                'manage_product_all'  => ['group'=>'Product','about'=>'Allow user to manage all products'],
                'remove_product'      => ['group'=>'Product','about'=>'Allow user to remove exists products']
            ]
        ]
    ]
];