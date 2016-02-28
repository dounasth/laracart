<?php


class LaracartSitemapsController extends \LaradminBaseController
{

    protected $storage = null;

    public function categories()
    {
        $categories = \Bonweb\Laracart\Category::defaultOrder()->get();

        foreach ($categories as $category) {
            Sitemap::addTag(route('site.cart.category.view', $category->slug), '', 'daily', '0.8');
        }

        return Sitemap::xml();
    }

    public function tags()
    {
        set_time_limit(1800);
        ini_set('memory_limit', '1024M');

        $this->storage = \Bonweb\Laracart\Product::enabled()->lists('id');

        $tags = \Conner\Tagging\Tag::where('count', '>=', 200)->orderBy('count', 'desc')->get();
        foreach ($tags as $tag) {
            Sitemap::addTag(route('site.cart.tag.view', $tag->slug), '', 'daily', '0.8');
            $this->recursive([$tag]);
        }

        return Sitemap::xml();
    }

    public function recursive($withTags) {
        $withTagsArr = [];
        foreach ($withTags as $tag) {
            $withTagsArr[] = $tag->slug;
        }

        $tags = Cache::remember('sitemap-tags-'.implode(',', $withTagsArr), 60*24, function() use ($withTags) {
            list(, , $tag_prod_ids) = $this->getProductIdsForTags($withTags);
            $products = \Bonweb\Laracart\Product::whereIn('id', $tag_prod_ids);
            $tags = \Bonweb\Laracart\Product::tagsFor($products->get());
            return $tags;
        });

        foreach ($withTags as $tag) {
            unset($tags[$tag->slug]);
        }
        foreach ($tags as $tag) {
            Sitemap::addTag(route('site.cart.tag.view', implode(',', array_merge($withTagsArr, [$tag->slug]))), '', 'daily', '0.8');
            $tmp = $withTags;
            $tmp[] = $tag;
            if (count($tmp) <= 1) {
                $this->recursive($tmp);
            }
        }
    }

    private function getProductIdsForTags($withTags) {
        $product_ids = $this->storage;
        foreach ($withTags as $tag) {
//            $tag = \Conner\Tagging\Tag::whereSlug($slug)->first();
            $tags[] = $tag->name;
            $tagObjects[] = $tag;
            $product_ids = Cache::remember('sitemap-tag-'.$tag->slug, 60*24, function() use ($product_ids, $tag) {
                if (fn_is_empty($product_ids)) {
                    $product_ids = \Conner\Tagging\Tagged::where('tag_slug', '=', $tag->slug)->where('taggable_type', '=', 'Bonweb\Laracart\Product')->distinct()->lists('taggable_id');
                }
                else {
                    $product_ids = array_intersect(\Conner\Tagging\Tagged::where('tag_slug', '=', $tag->slug)->where('taggable_type', '=', 'Bonweb\Laracart\Product')->distinct()->lists('taggable_id'), $product_ids);
                }
                return $product_ids;
            });
        }
        return [$tags, $tagObjects, $product_ids];
    }

    public function coupons()
    {
        $xml = Cache::remember("couponsXml", Config::get("laraffiliate::general.cache.offers"), function() {
            $merchant_ids = Merchant::enabled()->lists('network_campaign_id');
            $merchant_ids = implode(',', array_filter($merchant_ids));

            $client = new \Bonweb\Laraffiliate\HttpClient();
            $cfurl = "http://affiliate.linkwi.se/feeds/v1.1/CD14098/columns-program_id,program_name,short_description,valid_from,end_date,coupon_code,is_coupon,title,descr,image_url,type,site_url,tracking_url,creative_id,category/catinc-0/catex-0/proginc-{$merchant_ids}/progex-0/select-coupon/couponfeed.xml";
            $res = $client->get($cfurl);
            return $res->xml()->asXML();
        });
        $xml = new SimpleXMLElement($xml);

        Sitemap::addTag(route('site.coupons.list'), date('Y-m-d H:i:s'), 'daily', '0.8');

        foreach ($xml->offer as $offer) {
            Sitemap::addTag(route('site.coupons.one', [$offer->creative_id]), date('Y-m-d H:i:s'), 'daily', '0.8');
        }

        return Sitemap::xml();
    }
}
