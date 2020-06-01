# admin-product

## Instalasi

Jalankan perintah di bawah di folder aplikasi:

```
mim app install admin-product
```

## Price Config

Module ini tidak memiliki configurasi pricing. Tambahkan konfigurasi seperti di bawah
pada aplikasi untuk menentukan bagaimana pricing suatu produk diatur:

```php
return [
    'libForm' => [
        'forms' => [
            'admin.product.edit' => [
                // opsi 1
                'price-min' => [
                    'label' => 'Price Min',
                    'type' => 'number',
                    'rules' => [
                        'number' => [
                            'min' => 0
                        ]
                    ]
                ],
                'price-max' => [
                    'label' => 'Price Max',
                    'type' => 'number',
                    'rules' => [
                        'number' => [
                            'min' => 0
                        ]
                    ]
                ],

                // opsi 2
                'price-daily' => [
                    'label' => 'Price Daily',
                    'type' => 'number',
                    'rules' => [
                        'number' => [
                            'min' => 0
                        ]
                    ]
                ],
                'price-weekly' => [
                    'label' => 'Price Weekly',
                    'type' => 'number',
                    'rules' => [
                        'number' => [
                            'min' => 0
                        ]
                    ]
                ],
                'price-monthly' => [
                    'label' => 'Price Monthly',
                    'type' => 'number',
                    'rules' => [
                        'number' => [
                            'min' => 0
                        ]
                    ]
                ],
                'price-anually' => [
                    'label' => 'Price Anually',
                    'type' => 'number',
                    'rules' => [
                        'number' => [
                            'min' => 0
                        ]
                    ]
                ],

                // opsi 3
                'price' => [
                    'label' => 'Price',
                    'type' => 'number',
                    'rules' => [
                        'number' => [
                            'min' => 0
                        ]
                    ]
                ]
            ]
        ]
    ]
];
```

## Custom Field

Positions:

1. left-top-left
1. left-top-right
1. left-middle-top
1. left-middle-bottom
1. left-bottom-left
1. left-bottom-right
1. left-bottom-right-left
1. left-bottom-right-right
1. right