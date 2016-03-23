<?php

namespace Bonweb\Laracart;

/**
 * Created by PhpStorm.
 * 
 * User: nimda
 * Date: 5/20/15
 * Time: 2:06 PM
 *
 * @property integer $id 
 * @property string $title 
 * @property string $slug 
 * @property string $description 
 * @property string $status 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property \Carbon\Carbon $deleted_at 
 * @property integer $_lft
 * @property integer $_rgt 
 * @property integer $parent_id 
 * @property-read Category $parent 
 * @property-read \Illuminate\Database\Eloquent\Collection|Category[] $children 
 * @method static \Illuminate\Database\Query\Builder|\Category whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereTitle($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereSlug($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereLft($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereRgt($value)
 * @method static \Illuminate\Database\Query\Builder|\Category whereParentId($value)
 */


use Bonweb\Laradmin\Searchable;
use Illuminate\Database\Eloquent\SoftDeletingTrait;
use Cviebrock\EloquentSluggable\SluggableInterface;
use Cviebrock\EloquentSluggable\SluggableTrait;

class Category extends \Kalnoy\Nestedset\Node implements SluggableInterface {

    use Searchable;
    protected $searchable = [
        'columns' => [
            'title' => 1,
            'slug' => 1,
            'description' => 1,
        ],
    ];

    use SluggableTrait;
    use SoftDeletingTrait;

    protected $table = 'cart_categories';
    public $timestamps = true;
    protected $dates = ['deleted_at'];
    protected $guarded = array('id');
    protected $fillable = array('title', 'slug', 'description', 'status', 'parent_id');

    protected $sluggable = array(
        'build_from' => ['title'],
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

    public function products() {
        return $this->belongsToMany('Bonweb\Laracart\Product', 'cart_products_categories', 'category_id', 'product_id');
    }

    public function enabledProductsCount() {
        return $this->products()->enabled()->get()->count();
    }

    public function path()
    {
        $parent = $this->parent;
        return $parent ? $parent->path().' / '.$this->title : $this->title;
    }

    public function seo()
    {
        return $this->morphOne('Bonweb\Laradmin\Seo', 'seoble');
    }

    public static function scopeBasics($query) {
        return $query->withDepth()->having('depth', '=', 0);
    }

    public static function scopeLevel($query, $level) {
        return $query->withDepth()->having('depth', '=', $level);
    }

    public function scopeEnabled($query)
    {
        return $query->whereStatus('A');
    }

    public function scopeSorted($query, $type='asc')
    {
        return $query->orderBy('_lft', 'asc');
    }

    public static function cachedTree() {
        $t = new static;
        return $t->defaultOrder()->enabled()->remember(3600*24, 'site-categories')->get()->toTree();
    }

    public static function cachedRoot() {
        $t = new static;
        return $t->defaultOrder()->basics()->remember(3600*24, 'site-categories-root')->take(5)->get();
    }

}