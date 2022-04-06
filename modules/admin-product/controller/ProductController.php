<?php
/**
 * ProductController
 * @package admin-product
 * @version 0.0.1
 */

namespace AdminProduct\Controller;

use LibFormatter\Library\Formatter;
use LibForm\Library\Form;
use LibForm\Library\Combiner;
use LibPagination\Library\Paginator;
use Product\Model\Product;

class ProductController extends \Admin\Controller
{
    private function getParams(string $title): array{
        return [
            '_meta' => [
                'title' => $title,
                'menus' => ['product', 'all-product']
            ],
            'subtitle' => $title,
            'pages' => null
        ];
    }

    public function editAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_product && !$this->can_i->manage_product_all)
            return $this->show404();

        $product = (object)[
            'status'  => 2
        ];

        $id = $this->req->param->id;
        if($id){
            $cond = [
                'id' => $id,
                'status' => [1,2]
            ];
            if(!$this->can_i->manage_product_all)
                $cond['user'] = $this->user->id;
            $product = Product::getOne(['id'=>$id]);
            if(!$product)
                return $this->show404();
            $params = $this->getParams('Edit Product');
        }else{
            if(!$this->can_i->product_create)
                return $this->show404();
            $params = $this->getParams('Create New Product');
        }

        $form           = new Form('admin.product.edit');
        $params['form'] = $form;
        $params['prices'] = [];
        $params['fields'] = [];

        $fields = $form->getFields();
        foreach($fields as $fname => $field){
            if(substr($fname,0,6) === 'price-')
                $params['prices'][] = $fname;
            if(isset($field->position)){
                if(!isset($params['fields'][$field->position]))
                    $params['fields'][$field->position] = [];
                $params['fields'][$field->position][] = $fname;
            }
        }

        $params['statuses'] = [
            1 => 'Draft',
            2 => 'Published'
        ];

        $c_opts = [
            'price'      => [null,                      null, 'json'],
            'cover'      => [null,                      null, 'json'],
            'meta'       => [null,                      null, 'json'],
            'category'   => ['admin-product-category',  null, 'format', 'all', 'name', 'parent'],
            'collateral' => ['admin-product-collateral',null, 'format', 'all', 'name'],
            'brand'      => ['admin-product-brand',     null, 'format', 'all', 'name']
        ];

        $combiner = new Combiner($id, $c_opts, 'product');
        $product  = $combiner->prepare($product);

        $params['opts'] = $combiner->getOptions();

        if(!($valid = $form->validate($product)) || !$form->csrfTest('noob'))
            return $this->resp('product/edit', $params);

        $valid = $combiner->finalize($valid);

        $valid->price_min = null;
        $valid->price_max = null;
        if(isset($valid->price)){
            $prices = json_decode($valid->price);
            if($prices){
                foreach($prices as $price){
                    if(!$price)
                        continue;
                    if(is_null($valid->price_min) || $valid->price_min > $price)
                        $valid->price_min = $price;
                    if(is_null($valid->price_max) || $valid->price_max < $price)
                        $valid->price_max = $price;
                }
            }
        }

        if($id){
            if(!Product::set((array)$valid, ['id'=>$id]))
                deb(Product::lastError());
        }else{
            $valid->user = $this->user->id;
            if(!($id = Product::create((array)$valid)))
                deb(Product::lastError());
        }

        $combiner->save($id, $this->user->id);

        // add the log
        $this->addLog([
            'user'     => $this->user->id,
            'object'   => $id,
            'parent'   => 0,
            'method'   => isset($product->id) ? 2 : 1,
            'type'     => 'product',
            'original' => $product,
            'changes'  => $valid
        ]);

        $next = $this->router->to('adminProduct');
        $this->res->redirect($next);
    }

    public function indexAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->manage_product && !$this->can_i->manage_product_all)
            return $this->show404();

        $cond = $pcond = [];
        if($q = $this->req->getQuery('q'))
            $pcond['q'] = $cond['q'] = $q;

        if($status = $this->req->getQuery('status'))
            $pcond['status'] = $cond['status'] = $status;
        else
            $cond['status'] = ['__op', '>', 0];

        if(!$this->can_i->manage_product_all)
            $cond['user'] = $this->user->id;

        list($page, $rpp) = $this->req->getPager(25, 50);

        $products = Product::get($cond, $rpp, $page, ['name'=>true]) ?? [];
        if($products)
            $products = Formatter::formatMany('product', $products, ['user']);

        $params             = $this->getParams('Product');
        $params['products'] = $products;
        $params['form']     = new Form('admin.product.index');

        $params['form']->validate( (object)$this->req->get() );

        // pagination
        $params['total'] = $total = Product::count($cond);
        if($total > $rpp){
            $params['pages'] = new Paginator(
                $this->router->to('adminProduct'),
                $total,
                $page,
                $rpp,
                10,
                $pcond
            );
        }

        $this->resp('product/index', $params);
    }

    public function removeAction(){
        if(!$this->user->isLogin())
            return $this->loginFirst(1);
        if(!$this->can_i->remove_product)
            return $this->show404();

        $id      = $this->req->param->id;

        $cond = [
            'id' => $this->req->param->id
        ];
        if(!$this->can_i->manage_product_all)
            $cond['user'] = $this->user->id;

        $product = Product::getOne($cond);
        $next    = $this->router->to('adminProduct');
        $form    = new Form('admin.product.index');

        if(!$form->csrfTest('noob'))
            return $this->res->redirect($next);

        // add the log
        $this->addLog([
            'user'   => $this->user->id,
            'object' => $id,
            'parent' => 0,
            'method' => 3,
            'type'   => 'product',
            'original' => $product,
            'changes'  => null
        ]);

        $product_set = [
            'status' => 0,
            'slug'   => time() . '#' . $product->slug
        ];
        Product::set($product_set, ['id'=>$id]);

        $this->res->redirect($next);
    }
}
