<?php

use Bonweb\Laracart\Product;
use Bonweb\Laracart\Category;
use Bonweb\Laracart\Filter;

class LaracartProducts extends \LaradminBaseController
{

    public function viewProduct($slug) {
        $product = Product::whereSlug($slug)->first();
        if ($product) {
            return View::make('laracart::site.product')->withProduct( $product );
        }
        else die('product not found');
    }

    public function viewCategory($slug) {
        $category = Category::whereSlug($slug)->first();
        $products = $category->products;
        $filters = Filter::current();
        if ($filters) {
            $cat_prod_ids = $category->products->lists('id');
            $fil_prod_ids = Product::whereMeta($filters)->get()->lists('id');
            $products = Product::whereIn('id', array_intersect($cat_prod_ids, $fil_prod_ids))->get();
        }
        if ($category) {
            return View::make('laracart::site.category')
                    ->withCategory( $category )
                    ->withProducts( $products );
        }
        else die('category not found');
    }

}
