<?php

use Bonweb\Laracart\Product;
use Bonweb\Laracart\ProductsCategories;
use Bonweb\Laracart\Category;
use Bonweb\Laracart\Filter;
use Conner\Tagging\Tag;
use Conner\Tagging\Tagged;

class LaracartUserController extends \LaradminBaseController
{
    public function productLists($id=null) {
        $lists = \Bonweb\Laracart\ProductList::forUser();
        $selected = ($id) ? \Bonweb\Laracart\ProductList::findOrFail($id) : false;
        return View::make('laracart::site.user.product-lists', compact('lists', 'selected'));
    }
}
