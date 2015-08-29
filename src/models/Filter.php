<?php

namespace Bonweb\Laracart;

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
    protected $fillable = array('meta_id', 'title', 'slug', 'status');

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
        $this->sluggify(true);
        return parent::save($options);
    }

    public function seo()
    {
        return $this->morphOne('Bonweb\Laradmin\Seo', 'seoble');
    }

    public function meta() {
        return $this->hasOne('Bonweb\Laradmin\Meta', 'id', 'meta_id');
    }

    public function values($product_ids) {
        $values = array();
        if ($product_ids && $this->meta) {
            $values = \Bonweb\Laradmin\ProductMeta::select('value')->whereIn('xref_id', $product_ids)->whereMetaId($this->meta->id)->distinct()->lists('value');
        }
        return $values;
    }

    public static function makeLink($filter_slug, $filter_value){
        $current_filters = Filter::current();
        $current_filters[$filter_slug] = $filter_value;
        return http_build_query($current_filters);
    }

    public static function current(){
        $current_filters = array();
        $request = Input::all();
        foreach ($request as $k => $v) {
            $filter = Filter::whereSlug($k)->first();
            if ($filter) {
                $current_filters[$k] = $v;
            }
        }
        return $current_filters;
    }

}