<?php

namespace Bonweb\Laracart;

use Bonweb\Laradmin\Photo;
use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Lanz\Commentable\Commentable;
use Conner\Tagging\TaggableTrait;
use Mmanos\Metable\Metable;

/**
 * Product
 *
 * @property integer $id
 * @property string $sku
 * @property string $slug
 * @property string $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Price $prices
 * @property-read \Illuminate\Database\Eloquent\Collection|\Conner\Tagging\Tagged[] $tagged
 * @property-read \Illuminate\Database\Eloquent\Collection|\Lanz\Commentable\Comment[] $comments
 * @method static \Illuminate\Database\Query\Builder|\Product whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Product whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\Product whereSlug($value)
 * @method static \Illuminate\Database\Query\Builder|\Product whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Product whereUpdatedAt($value)
 * @method static \Product withAllTags($tagNames)
 * @method static \Product withAnyTag($tagNames)
 * @property \Carbon\Carbon $deleted_at
 * @property-read \Bonweb\Laracart\Description $descriptions
 * @method static \Illuminate\Database\Query\Builder|\Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Product whereSku($value)
 */
class Product extends \Eloquent implements SluggableInterface{

//    /**
//     * @var
//     */
//    public $price;
//    /**
//     * @var
//     */
//    public $list_price;
//    /**
//     * @var
//     */
//    public $title;
//    /**
//     * @var
//     */
//    public $full_description;
//    /**
//     * @var
//     */
//    public $short_description;

    use SluggableTrait;
    use SoftDeletingTrait;
    use TaggableTrait;
    use Commentable;

    use Metable;
    protected $meta_model = 'Bonweb\Laradmin\Meta';
    protected $metable_table = 'cart_product_meta';

    /**
     * @var bool
     */
    public $timestamps = true;
    /**
     * @var array
     */
    protected $dates = ['deleted_at'];
    /**
     * @var string
     */
    protected $table = 'cart_products';
    /**
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * @var array
     */
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

    protected $fillable = array('title', 'sku', 'slug', 'status');

    function __construct()
    {
        parent::__construct();
    }

    /**
     * @param mixed $id
     * @param array $columns
     * @return Product
     */
    public static function findOrNew($id, $columns = array('*'))
    {
        return parent::findOrNew($id, $columns);
    }

    public function saveFromData(ProductData $data, array $options = array())
    {
        $isNew = !($this->id > 0);

        $this->fill(fn_to_array($data));
        $saved = parent::save($options);
        if ($saved) {

            $this->categories()->sync($data->categories);

            $prices = $this->prices()->first();
            if (!$prices) {
                $prices = new Price(['product_id'=>$this->id]);
            }
            $prices->price = ($data->price);
            $prices->list_price = ($data->list_price);
            $this->prices()->save($prices);

            $descriptions = $this->descriptions()->first();
            if (!$descriptions) {
                $descriptions = new Description(['product_id'=>$this->id]);
            }
            $descriptions->full = $data->full_description;
            $descriptions->short = $data->short_description;
            $this->descriptions()->save($descriptions);

            $mainImage = \Input::file('files.main_image');
            if ($mainImage && $mainImage->isValid())
            {
                $filename = md5($mainImage->getClientOriginalName().time()) . '.' . $mainImage->getClientOriginalExtension();
                \Input::file('files.main_image')->move(public_path().'/uploads/products', $filename);
                $this->mainPhoto('/uploads/products' . '/' . $filename);
            }
            elseif ($data->main_image) {
                $this->mainPhoto($data->main_image);
            }

            if ($data->tags) {
                $this->retag($data->tags);
            }

            if ($data->affiliateUrl) {
                if ($this->affiliateUrl) {
                    $this->affiliateUrl->url = $data->affiliateUrl;
                    $this->affiliateUrl->save();
                }
                else {
                    $url = ProductUrl::create([
                        'product_id' => $this->id,
                        'url' => $data->affiliateUrl,
                    ]);
                    $this->affiliateUrl()->save($url);
                }
            }
        }

        if ($this->needsSlugging() || $isNew) {
            $this->resluggify();
        }

        return $this;
    }

    public function saveMeta($meta) {
        if (fn_is_not_empty($meta)) {
            $this->setMeta($meta);
        }
    }

    public function delete()
    {
        if ($this->trashed()) {
            $this->categories()->sync([]);
            $this->descriptions()->delete();
            $this->prices()->delete();
            $this->affiliateUrl()->delete();
        }
        parent::delete();
    }

    public function prices() {
        return $this->hasOne('Bonweb\Laracart\Price', 'product_id', 'id');
    }

    public function affiliateUrl() {
        return $this->hasOne('Bonweb\Laracart\ProductUrl', 'product_id', 'id');
    }

    public function descriptions() {
        return $this->hasOne('Bonweb\Laracart\Description', 'product_id', 'id');
    }

    public function photos()
    {
        return $this->morphMany('Bonweb\Laradmin\Photo', 'imageable');
    }

    /**
     * @param null $path
     * @return Photo
     */
    public function mainPhoto($path=null) {
        if ($path) {
            $link = $this->photos()->where('link_type', '=', 'M')->first();
            if (!$link) {
                $link = new Photo();
            }
            $link->path = $path;
            $link->link_type = 'M';
            $this->photos()->save($link);
        }
        else {
            $link = $this->photos()->where('link_type', '=', 'M')->first();
        }
        return $link;
    }

    public function categories() {
        return $this->belongsToMany('Bonweb\Laracart\Category', 'cart_products_categories', 'product_id', 'category_id')->withPivot('type');
    }

    /**
     * @param null $category_id
     * @return Category
     */
    public function mainCategory($category_id=null) {
        if ($category_id){
            $link = $this->categories()->wherePivot('type', '=', 'M')->wherePivot('product_id', '=', $this->id)->first()->pivot;
            if ($link) {
                $this->categories()->detach($link->category_id);
            }
            $this->categories()->attach([$category_id => ['type'=>'M']]);
        }
        $mainCat = $this->categories()->wherePivot('type', '=', 'M')->first();
        if (!$mainCat) {
            $mainCat = new Category();
        }
        return $mainCat;
    }

    /**
     * @param null $category_ids
     * @param string $delimiter
     * @param string $action
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function additionalCategories($category_ids=null, $delimiter=',', $action='sync') {
        if ($category_ids && !is_array($category_ids)){
            $category_ids = explode($delimiter, $category_ids);
        }
        if ($category_ids && is_array($category_ids)){
            $category_ids = array_flip($category_ids);
            foreach ($category_ids as $k => $v) {
                $category_ids[$k] = ['type'=>'A'];
            }
            $this->categories()->$action($category_ids);
        }
        return $this->categories()->wherePivot('type', '=', 'A')->get();
    }

    /**
     * @param $category_ids
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function addAdditionalCategory($category_ids){
        return $this->additionalCategories($category_ids, ',', $action='attach');
    }

    /**
     * @param $category_ids
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function removeAdditionalCategory($category_ids){
        return $this->additionalCategories($category_ids, ',', $action='detach');
    }

    public function tagNamesInline($delimiter=',') {
        return implode($delimiter, $this->tagNames());
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->title ;
    }

    public function seo()
    {
        return $this->morphOne('Bonweb\Laradmin\Seo', 'seoble');
    }


}