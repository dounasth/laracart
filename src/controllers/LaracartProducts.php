<?php

use Bonweb\Laracart\Product;
use Bonweb\Laracart\ProductsCategories;
use Bonweb\Laracart\Category;
use Bonweb\Laracart\Filter;
use Conner\Tagging\Tag;
use Conner\Tagging\Tagged;

class LaracartProducts extends \LaradminBaseController
{

    public function viewProduct($slug) {
        $product = Product::enabled()->whereSlug($slug)->first();
        if ($product) {
            return View::make('laracart::site.product')->withProduct( $product );
        }
        else return Redirect::route('home', [], 301);
    }

    public function affiliateRedirect($slug) {
        $product = Product::enabled()->whereSlug($slug)->first();
        if ($product) {
            return Redirect::to($product->getAffiliateUrl());
        }
        else return Redirect::route('home', [], 301);
    }

    public function viewCategory($slug, $tags=false) {
        set_time_limit(1800);
        ini_set('memory_limit', '1024M');
        $category = Category::whereSlug($slug)->withDepth()->with('seo')->remember(Config::get('laracart::general.cache.category'))->first();

        if ($category) {

            $allcats = [$category->id];

//            if ($category->depth >= 0) {
                $allcats = array_merge($allcats, $category->descendants()->remember(Config::get('laracart::general.cache.category'))->lists('id'));
                $product_ids = Product::getIdsForCategories($allcats);

//            }
//            else {
//                $product_ids = $category->products()->select('cart_products.id')->enabled()->lists('cart_products.id');
//            }

            $product_ids_q[] = 'select product_id from cart_products_categories where category_id in ('.implode(',', $allcats).') AND product_id IN (SELECT id FROM cart_products WHERE status = \'A\')';

            $filters = Filter::current2();
            $withTags = array_filter(explode(',', Input::get('with', $tags)));
            $tags = [];

            if ($filters && $withTags) {
                $product_ids_q = array_merge($product_ids_q, \Bonweb\Laradmin\ProductMeta::productsWithMeta($filters));
                $product_ids_q = array_merge($product_ids_q, Tagged::getProductIdsQueryForTags($withTags));
            }
            elseif ($filters && !$withTags) {
                $product_ids_q = array_merge($product_ids_q, \Bonweb\Laradmin\ProductMeta::productsWithMeta($filters));
            }
            elseif (!$filters && $withTags) {
                $product_ids_q = array_merge($product_ids_q, Tagged::getProductIdsQueryForTags($withTags));
            }
            else { }

            $products = Product::with('seo', 'photos', 'affiliateUrl', 'prices', 'metas', 'imported', 'imported.merchant');

            foreach ($product_ids_q as $q) {
                $products->whereRaw("cart_products.id IN ($q)");
            }

            if (Input::get('orderBy', '') == 'price') {
                $products = $products
                    ->join('cart_product_prices', 'cart_products.id', '=', 'cart_product_prices.product_id')
                    ->orderBy('price', Input::get('orderType', 'asc'));
            }
            $products = $products->orderBy('cart_products.id', 'desc')//->with('seo', 'photos', 'affiliateUrl', 'prices', 'metas', 'imported', 'imported.merchant')
                ->remember(Config::get('laracart::general.cache.category'))
                ->paginate(24);

            $tags = Tagged::tagsForProductsAndSlugs($product_ids_q, $withTags);
            //  remove tags that will not filter products and will create duplicates
            if ($tags) {
                $c = $products->count();
                $tags->filter(function($tag) use ($c) { return ($c != $tag->count); });
            }

//            if (Debugbar::isEnabled()) {
//                return 'asd';
//            }

            return View::make('laracart::site.category')
                    ->withCategory( $category )
                    ->withProducts( $products )
                    ->withTags( $tags )
                    ->with('product_ids', $product_ids_q );
        }
        else return Redirect::route('home', [], 301);
    }

    public function viewTags($slugs) {
        set_time_limit(1800);
        ini_set('memory_limit', '1024M');
        $slugs = explode(',', $slugs);
        list($tags, $tagObjects, $product_ids) = $this->getProductIdsForTags($slugs);

        if (fn_is_not_empty($tags)) {
            $enabled_ids = Product::enabled()->lists('id');
            $product_ids = array_intersect($product_ids, $enabled_ids);
//            $products = Product::whereIn('id', $product_ids);
//            $products = $products->paginate(24);
            $products = $this->paginate($product_ids, 24);

            if ($products->count() == 0 && count($slugs) > 1) {
                array_pop($slugs);
                return Redirect::route('site.cart.tag.view', [implode(',', $slugs)], 301);
            }

            $mtags = Cache::remember("tagged-".implode(',', $slugs), Config::get('laracart::general.cache.category'), function() use ($products, $product_ids, $slugs) {
                $mtags = Tagged::tagsForProductsAndSlugs($product_ids, $slugs);
                //  remove tags that will not filter products and will create duplicates
                if ($mtags) {
                    $c = $products->count();
                    $mtags->filter(function($tag) use ($c) { return ($c != $tag->count); });
                }
                return $mtags;
            });

            return View::make('laracart::site.tag')
                    ->withTags( $tagObjects )
                    ->withMtags( $mtags )
                    ->withSlugs( implode(',', $slugs) )
                    ->withProducts( $products  );
        }
        else return Redirect::route('home', [], 301);
    }

    private function getProductIdsForTags($slugs) {
        $product_ids = array();
        $tags = array();
        $tagObjects = array();
        foreach ($slugs as $slug) {
            $tag = Tag::whereSlug($slug)->first();
            if ($tag) {
                $tags[] = $tag->name;
                $tagObjects[] = $tag;
                $product_ids = Cache::remember('tag-'.$slug.'pids', Config::get('laracart::general.cache.category'), function() use ($product_ids, $tag) {
                    if (fn_is_empty($product_ids)) {
                        $product_ids = Tagged::where('tag_slug', '=', $tag->slug)
                            ->where('taggable_type', '=', 'Bonweb\Laracart\Product')
                            ->distinct()->lists('taggable_id');
                    }
                    else {
                        $product_ids = array_intersect(Tagged::where('tag_slug', '=', $tag->slug)
                            ->where('taggable_type', '=', 'Bonweb\Laracart\Product')
                            ->distinct()->lists('taggable_id'), $product_ids);
                    }
                    return $product_ids;
                });
            }
        }
        return [$tags, $tagObjects, $product_ids];
    }

    protected function paginate($product_ids, $perPage) {
        $count = count($product_ids);
        $pagination = Paginator::make($product_ids, $count, $perPage);

        $page = $pagination->getCurrentPage($count);
        $product_ids = array_slice($product_ids, ($page - 1) * $perPage, $perPage);

        $pagination = Paginator::make($product_ids, $count, $perPage);
        return $pagination;
    }
}
