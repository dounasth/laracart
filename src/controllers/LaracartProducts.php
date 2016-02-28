<?php

use Bonweb\Laracart\Product;
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
        Debugbar::addMessage($tags);
        set_time_limit(1800);
        ini_set('memory_limit', '1024M');
        $category = Category::whereSlug($slug)->withDepth()->first();
        if ($category) {

            $product_ids = $category->products()->select('cart_products.id')->enabled()->lists('cart_products.id');
            $filters = Filter::current();
            $withTags = array_filter(explode(',', Input::get('with', $tags)));
            $tags = [];

            $cacheKey = "cat-{$slug}";
            if ($filters && $withTags) {
                $fil_prod_ids = Product::whereMeta($filters)->get()->lists('id');
                list(, , $tag_prod_ids) = $this->getProductIdsForTags($withTags);
                $product_ids = array_intersect($product_ids, $fil_prod_ids, $tag_prod_ids);
                $products = Product::whereIn('id', $product_ids);
                $products = $products->paginate(24);
                $cacheKey .= "-filters-".implode(',', $filters)."-tags-".implode(',', $withTags)."-prods-".implode(',', $product_ids);
            }
            elseif ($filters && !$withTags) {
                $fil_prod_ids = Product::whereMeta($filters)->get()->lists('id');
                $product_ids = array_intersect($product_ids, $fil_prod_ids);
                $products = $lproducts = Product::whereIn('id', $product_ids);
                $products = $products->paginate(24);
                $cacheKey .= "-filters-".implode(',', $filters)."-prods-".implode(',', $product_ids);
            }
            elseif (!$filters && $withTags) {
                list(, , $tag_prod_ids) = $this->getProductIdsForTags($withTags);
                $product_ids = array_intersect($product_ids, $tag_prod_ids);
                $products = Product::whereIn('id', $product_ids);
                $products = $products->paginate(24);
                $cacheKey = "-tags-".implode(',', $withTags)."-prods-".implode(',', $product_ids);
            }
            else {
                $products = $category->products()->enabled()->paginate(24);
            }

//            $tags = Cache::remember($cacheKey, Config::get('laracart::general.cache.category'), function() use ($product_ids, $products, $withTags)
//            {
                $tags = Tagged::tagsForProductsAndSlugs($product_ids, $withTags);
                //  remove tags that will not filter products and will create duplicates
                if ($tags) {
                    $c = $products->count();
                    $tags->filter(function($tag) use ($c) { return ($c != $tag->count); });
                }
//                return $tags;
//            });

            return View::make('laracart::site.category')
                    ->withCategory( $category )
                    ->withProducts( $products )
                    ->withTags( $tags )
                    ->with('product_ids', $product_ids );
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
