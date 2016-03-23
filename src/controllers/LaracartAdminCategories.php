<?php

use Bonweb\Laracart\Category;

class LaracartAdminCategories extends \LaradminBaseController
{

    public function listAll() {
        return View::make('laracart::categories.list')->withCategories( Category::defaultOrder()->get()->toTree() );
    }

    public function listTrashed() {
        return View::make('laracart::categories.list')->withCategories( Category::defaultOrder()->onlyTrashed()->get() );
    }

    public function update($id=0) {
        $category = Category::findOrNew($id);
        return View::make('laracart::categories.update')->withCategory( $category );
    }

    public function save($id=0) {

        if (Input::get('saveNew', 0)) {
            $category = Category::create(Input::get('category', []));
        }
        else {
            $category = Category::findOrNew($id);
            $category->fill(Input::get('category', []));
            $category->save();
        }

        $seo = Input::get('seo', []);
        if ($category->seo) {
            $category->seo->fill($seo)->save();
        }
        else {
            $seo = \Bonweb\Laradmin\Seo::create($seo);
            $category->seo()->save($seo);
        }

        if (Input::ajax()) {
            return View::make('laradmin::parts.messages')->withMessages( AlertMessage::success('Category saved') );
        }
        else return Redirect::route('cart.categories.update', [$category->id])->withMessage( AlertMessage::success('Category saved') );
    }

    public function delete($id) {
        $o = Category::withTrashed()->where('id','=',$id)->first();
        if ($o->id) {
            $message = AlertMessage::success("Category {$o->title} ({$o->id}) deleted");
            if ($o->trashed()) {
                $tree = $o->children()->withTrashed()->get()->toTree();
                $this->deleteCategoryProducts($tree);
                foreach ($tree as $c) {
                    $c->forceDelete();
                }
            }
            else {
                $o->forceDelete();
            }
            return Redirect::back()->withMessage( $message );
        }
        else {
            return Redirect::back()->withMessage( $message = AlertMessage::error("Category for deletion not found") );
        }
    }

    private function deleteCategoryProducts($tree){
        foreach($tree as $node) {
            $node->products()->delete();
            if ($node->children()->withTrashed()->get()) {
                $this->deleteCategoryProducts($node->children()->withTrashed()->get());
            }
        }
    }

    public function restoreTrashed($id) {
        $o = Category::withTrashed()->where('id','=',$id)->first();
        $o->restore();
        $message = AlertMessage::success("Category {$o->title} ({$o->id}) deleted");
        return Redirect::back()->withMessage( $message );
    }

    public function makeoldcats() {
        $file = public_path().'/oldcats.txt';
        $file = file_get_contents($file);
        $file = base64_decode($file);
        $file = unserialize($file);

        $nc = Category::findBySlug('imported');
        $root_id = (isset($nc->id) && $nc->id) ? $nc->id : 0 ;

        foreach ($file as $category) {
            niceprintr($category);
            $nc = new Category();
            $nc->id = $category['category_id']+10000;
            $nc->parent_id = ($category['sub'] > 0) ? $category['sub']+10000 : $root_id ;
            $nc->title = $category['category_name'];
            $nc->slug = $nc->description = '';
            $nc->status = 'A';
            $nc->save();
        }

        exit;
    }

    public function categoriesJson() {
        $data = array();
        $categories = Category::defaultOrder()->get();
        foreach ($categories as $category) {
            $data[] = array('id'=>$category->id, 'name'=>$category->path(), 'children'=>$category->getDescendantCount());
        }
        $headers = array(
            'Content-type'=> 'application/json; charset=utf-8',
//            'Cache-Control' => 'max-age='.Config::get('api::general.jsonCacheControl'),
        );
        return Response::json($data, 200, $headers, JSON_UNESCAPED_UNICODE);
    }

    public function finalCategoriesJson() {
        $data = array();
        $categories = Category::defaultOrder()->get();
        foreach ($categories as $category) {
            if ($category->getDescendantCount() == 0) {
                $data[] = array('id'=>$category->id, 'name'=>$category->path(), 'children'=>$category->getDescendantCount());
            }
        }
        $headers = array(
            'Content-type'=> 'application/json; charset=utf-8',
//            'Cache-Control' => 'max-age='.Config::get('api::general.jsonCacheControl'),
        );
        return Response::json($data, 200, $headers, JSON_UNESCAPED_UNICODE);
    }

    public function categoriesTree() {
        $data = array();
        $id = Input::get('id', 0);

        if ($id == 0) {
            $categories = Category::defaultOrder()->basics()->get();
        }
        else {
            $node = Category::defaultOrder()->withDepth()->findOrFail($id);
            $categories = Category::defaultOrder()->whereDescendantOf($node)->level($node->depth+1)->get();
        }

        foreach ($categories as $category) {
            $data[] = array('id'=>$category->id, 'name'=>$category->title, 'level'=>$category->depth, 'type'=>'default');
        }

        $headers = array(
            'Content-type'=> 'application/json; charset=utf-8',
//            'Cache-Control' => 'max-age='.Config::get('api::general.jsonCacheControl'),
        );
        return Response::json(array('nodes' => $data), 200, $headers, JSON_UNESCAPED_UNICODE);
    }

    public function savePositionedCategory() {
        $item = Input::get('item', false);
        $parent = Input::get('parent', 0);
        $sibling = Input::get('sibling', false);
        $pos = Input::get('pos', '');

        $category = Category::findOrFail($item);
        $neighbor = Category::findOrFail($sibling);

        $category->parent_id = $parent;
        $category->save();

        $category = Category::findOrFail($item);
        if ($neighbor) {
            if ($pos == 'before') {
                $category->before($neighbor);
            }
            elseif ($pos == 'after') {
                $category->after($neighbor);
            }
        }
        $category->save();

        return Response::json($category->toArray());
    }

}
