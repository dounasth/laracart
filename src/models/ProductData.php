<?php

namespace Bonweb\Laracart;


/**
 * ProductData
 */
class ProductData {

    public $title = '';
    public $slug = '';
    public $sku = '';
    public $short_description = '';
    public $full_description = '';
    public $status = '';
    public $price = '';
    public $list_price = '';
    public $main_image = '';

    public $affiliateUrl = '';

    public $categories;
    public $tags;

    public static function from($data)
    {
        $pdata = new ProductData();
        $pdata->title = $data['title'];
        $pdata->slug = $data['slug'];
        $pdata->sku = $data['sku'];
        $pdata->short_description = $data['short_description'];
        $pdata->full_description = $data['full_description'];
        $pdata->status = $data['status'];
        $pdata->price = $data['price'];
        $pdata->list_price = $data['list_price'];
        $pdata->main_image = $data['main_image'];
        $pdata->affiliateUrl = $data['affiliate_url'];

        if (isset($data['main_category'])) {
            $pdata->categories = array();
            $pdata->categories[$data['main_category']] = array( 'type'=>'M' );
        }
        if (isset($data['additional_categories'])) {
            foreach ($data['additional_categories'] as $additional) {
                $pdata->categories[$additional] = array(  'type'=>'A' );
            }
        }

        if ($data['tags']) {
            $pdata->tags = explode(',', $data['tags']);
        }

        return $pdata;
    }

}