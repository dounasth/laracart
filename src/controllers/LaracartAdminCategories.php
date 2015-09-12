<?php

use Bonweb\Laracart\Category;

class LaracartAdminCategories extends \LaradminBaseController
{

    public function listAll() {
//        $category = Category::findOrNew(8);
//        $category->title = 'asdasdasdasd';
//        $category->slug = 'asdasdasdasd';
//        $category->description = 'asdasdasdasdadfasdfsdfsfsdf';
//        $category->status = 'A';
//        $category->parent_id = 7;
//        $category->save();

        return View::make('laracart::categories.list')->withCategories( Category::get()->toTree() );
    }

    public function listTrashed() {
        return View::make('laracart::categories.list')->withCategories( Category::onlyTrashed()->get() );
    }

    public function update($id=0) {
        $category = Category::findOrNew($id);
        return View::make('laracart::categories.update')->withCategory( $category )->withCategories( Category::withTrashed()->get()->toTree() );
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
        return Redirect::route('cart.categories.update', [$category->id])->withMessage( AlertMessage::success('Category saved') );
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
        Category::truncate();
        $file = public_path().'/oldcats.txt';
        $file = file_get_contents($file);
        $file = base64_decode($file);
        $file = unserialize($file);

        foreach ($file as $category) {
            niceprintr($category);
            $nc = new Category();
            $nc->id = $category['category_id'];
            $nc->parent_id = $category['sub'];
            $nc->title = $category['category_name'];
            $nc->slug = $nc->description = '';
            $nc->status = 'A';
            $nc->save();
        }

        exit;
    }

    public function categoriesJson() {
        $data = array();
        $categories = Category::all();
        foreach ($categories as $category) {
            $data[] = array('id'=>$category->id, 'name'=>$category->path());
        }
        $headers = array(
            'Content-type'=> 'application/json; charset=utf-8',
//            'Cache-Control' => 'max-age='.Config::get('api::general.jsonCacheControl'),
        );
        return Response::json($data, 200, $headers, JSON_UNESCAPED_UNICODE);
    }

}
