<?php

use Bonweb\Laracart\Product;
use Bonweb\Laracart\Category;
use Bonweb\Laracart\ProductData;
use yajra\Datatables\Datatables;

class LaracartAdminProducts extends \LaradminBaseController
{

    public function listAll() {
        return View::make('laracart::products.list')->withProducts( Product::all() );
    }

    public function listTrashed() {
        return View::make('laracart::products.list')->withProducts( Product::onlyTrashed()->get() );
    }

    public function update($id=0) {
        $product = Product::findOrNew($id);
        return View::make('laracart::products.update')
                ->withProduct( $product )
                ->withCategories( Category::defaultOrder()->get()->toTree() );
    }

    public function save($id=0) {
        $data = ProductData::from(Input::get('product', []));

        if (Input::get('saveNew', 0)) {
            $product = new Product();
            $data->slug = '';
        }
        else {
            $product = Product::findOrNew($id);
        }

        $product->saveFromData($data);

        $meta = Input::get('meta', []);
        $product->saveMeta($meta);

        $seo = Input::get('seo', []);
        if ($product->seo) {
            $product->seo->fill($seo)->save();
        }
        else {
            $seo = \Bonweb\Laradmin\Seo::create($seo);
            $product->seo()->save($seo);
        }

        return Redirect::route('cart.products.update', [$product->id])->withMessage( AlertMessage::success('Product saved') );
    }

    public function delete($id) {
        $o = Product::withTrashed()->where('id','=',$id)->first();
        if ($o->id) {
            $message = AlertMessage::success("Product {$o->name} ({$o->id}) deleted");
            if ($o->trashed()) {
                $o->forceDelete();
            }
            else {
                $o->delete();
            }
            return Redirect::back()->withMessage( $message );
        }
        else {
            return Redirect::back()->withMessage( $message = AlertMessage::error("Product for deletion not found") );
        }
    }

    public function deleteMany($ids) {
        $ids = explode(',', $ids);

        foreach ($ids as $id) {
            $o = Product::withTrashed()->where('id','=',$id)->first();
            if ($o->id) {
//                if ($o->trashed()) {
                    $o->forceDelete();
//                }
//                else {
//                    $o->delete();
//                }
            }
        }
        $message = AlertMessage::success("Products deleted");
        return Redirect::back()->withMessage( $message );
    }

    public function restoreTrashed($id) {
        $o = Product::withTrashed()->where('id','=',$id)->first();
        $o->restore();
        $message = AlertMessage::success("Product {$o->name} ({$o->id}) deleted");
        return Redirect::back()->withMessage( $message );
    }

    public function index()
    {
        return View::make('laracart::products.list_copy');
    }

    /**
     * Process datatables ajax request.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function data()
    {
        return Datatables::of(Product::with(['prices', 'photos'])->select('*'))
            ->editColumn('photo', function ($product) {
                return '<img src="'.$product->mainPhoto()->path.'" height="100" border="0" />';
            })
            ->editColumn('action', function ($product) {
                return View::make('laracart::products.actions')->withProduct($product)->render();
            })
            ->make(true);
    }

}
