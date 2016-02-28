<?php

namespace Bonweb\Laracart;

use Conner\Tagging\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;
use Illuminate\Support\Facades\Input;

class Filter extends Model implements SluggableInterface {

    use SluggableTrait;
    use SoftDeletingTrait;

    protected $table = 'cart_filters';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $guarded = array('id');
    protected $fillable = array('meta_id', 'title', 'slug', 'show_on_canonical', 'indexing', 'status');

    protected $sluggable = array(
        'build_from' => 'title',
        'save_to'    => 'slug',
        'max_length' => null,
        'method' => ['GreekSlugGenerator','get_slug'],
        'separator' => '-',
        'unique' => true,
        'include_trashed' => true,
        'on_update' => false,
        'reserved' => null,
        'use_cache' => false,
    );

    public function save(array $options = array())
    {
        if ($this->needsSlugging()) {
            $this->sluggify(true);
        }
        return parent::save($options);
    }

    public function seo()
    {
        return $this->morphOne('Bonweb\Laradmin\Seo', 'seoble');
    }

    public function meta() {
        return $this->hasOne('Bonweb\Laradmin\Meta', 'id', 'meta_id');
    }

    public static function forBlock($product_ids) {
        $filters = array();
        $request = Input::all();
        $all_filters = Filter::all();
        foreach($all_filters as $filter) {
            $filter_values = $filter->values($product_ids);
            if (count($filter_values)) {
                $filters[] = array(
                    'data' => $filter,
                    'values' => $filter_values,
                    'selected' => array_key_exists($filter->slug, $request) ? $request[$filter->slug] : ''
                );
            }
        }
        return $filters;
    }

    public static function allValues($slug) {
        $filter = Filter::findBySlug($slug);
        $values = array();
        if ($filter && $filter->meta) {
            $values = \Bonweb\Laradmin\ProductMeta::select('value', \DB::raw('count(*) as total'))->whereMetaId($filter->meta->id)
                    ->groupBy('value')
                    ->orderBy('total', 'desc')
                    ->take(20)
                    ->lists('value', 'total')
            ;
//            $values = $values->distinct();
        }
        return $values;
    }

    public function values($product_ids) {
        $values = array();
        if ($product_ids && $this->meta) {
            $values  = \Cache::remember($this->meta->id.md5(implode(',', $product_ids)), \Config::get('laracart::general.cache.product-meta'), function() use ($product_ids)
            {
                $values = \Bonweb\Laradmin\ProductMeta::select('value')->whereIn('xref_id', $product_ids)->whereMetaId($this->meta->id);
                $values = $values->distinct()->lists('value');
                return $values;
            });
        }
        sort($values, SORT_STRING);
        return $values;
    }

    public static function getLinkParams($filter_slug='', $filter_value=''){
        $current_filters = Filter::current();
        if (Input::get('with', false)) $current_filters['with'] = Input::get('with');
//        if (Input::get('page', false)) $current_filters['page'] = Input::get('page');
        if ($filter_slug && $filter_value) $current_filters[$filter_slug] = $filter_value;
        return $current_filters;
    }

    public static function makeLink($filter_slug='', $filter_value=''){
        $params = Filter::getLinkParams($filter_slug, $filter_value);
        return (fn_is_not_empty(http_build_query($params))) ? '?'.http_build_query($params) : '' ;
    }

    public static function removeFromLink($filter_slug){
        $params = Filter::getLinkParams();
        unset($params[$filter_slug]);
        return (fn_is_not_empty(http_build_query($params))) ? '?'.http_build_query($params) : '' ;
    }

    public static function makeCanonicalLink(){
        $current_filters = Filter::current(true);
        return (fn_is_not_empty(http_build_query($current_filters))) ? '?'.http_build_query($current_filters) : '' ;
    }

    public static function makeIndexFollow(){
        $index = 'index';
        $follow = 'follow';
        $current_filters = Filter::current(false, true);
        if (fn_is_not_empty($current_filters)) {
            foreach ($current_filters as $filter) {
                if (!$filter->indexing) $index = 'noindex';
                if (!$filter->follow) $follow = 'nofollow';
            }
        }
        return "$index $follow";
    }

    public static function current($onlyCanonical=false, $full=false){
        $current_filters = array();
        $request = Input::all();
        ksort($request);
        foreach ($request as $k => $v) {
            $filter = Filter::whereSlug($k)->first();
            if ($filter) {
                if (!$onlyCanonical || ($onlyCanonical && $filter->show_on_canonical)) {
                    if ($full) {
                        $current_filters[$k] = $filter;
                    }
                    else $current_filters[$k] = $v;
                }
            }
        }
        return $current_filters;
    }

    public static function currentAsText($category_title) {
        $current_filters = array($category_title);
        $request = Input::all();
        ksort($request);
        foreach ($request as $k => $v) {
            if ($k == 'with') {
                $v = array_unique(explode(',', $v));
                ksort($v);
                foreach ($v as $tag_slug) {
                    $tag = Tag::whereSlug($tag_slug)->first();
                    if ($tag) {
                        $current_filters[] = $tag->name;
                    }
                }
            }
            else {
                $filter = Filter::whereSlug($k)->first();
                if ($filter) {
                    if ($filter->seo && $filter->seo->title && stripos($filter->seo->title, '[text]') !== false) {
                        $current_filters[] = str_ireplace('[text]', $v, $filter->seo->title);
                    }
                    elseif ($filter->seo && $filter->seo->title && stripos($filter->seo->title, '[number]') !== false) {
                        $current_filters[] = str_ireplace('[number]', (int)$v, $filter->seo->title);
                    }
                    else $current_filters[] = $v;
                }
            }
        }
        $current_filters = array_unique(explode(' ', implode(' ', $current_filters)));
        $current_filters = implode(' ', $current_filters);
        return $current_filters;
    }

    public static function makeCategoryFilterUrl($category, $tag=null) {
        $url = route('site.cart.category.view', $category->slug);
        $filters = Filter::current();
        if ($tag) {
            if (fn_is_not_empty(Input::get('with', ''))) {
                $filters['with'] = Input::get('with', '');
            }
            if (isset($filters['with']) && fn_is_not_empty($filters['with'])) {
                $filters['with'] = implode(',', [$filters['with'], $tag->slug]);
            }
            else $filters['with'] = $tag->slug;

        }
        if (fn_is_not_empty(http_build_query($filters))) {
            $url .= '?'.http_build_query($filters);
        }
        return $url;
    }

}