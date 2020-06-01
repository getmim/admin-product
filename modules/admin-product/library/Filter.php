<?php
/**
 * Filter
 * @package admin-product
 * @version 0.0.1
 */

namespace AdminProduct\Library;

use Product\Model\Product;

class Filter implements \Admin\Iface\ObjectFilter{

    static function filter(array $cond): ?array{
        $cnd = [];
        if(isset($cond['q']) && $cond['q'])
            $cnd['q'] = (string)$cond['q'];

        if(!\Mim::$app->can_i->manage_product_all)
            $cnd['user'] = \Mim::$app->user->id;
        
        $products = Product::get($cnd, 15, 1, ['name'=>true]);
        if(!$products)
            return [];

        $result = [];
        foreach($products as $product){
            $result[] = [
                'id'    => (int)$product->id,
                'label' => $product->name,
                'info'  => $product->name,
                'icon'  => NULL
            ];
        }

        return $result;
    }

    static function lastError(): ?string{
        return null;
    }
}