<?php

use Bonweb\Laracart\Filter;
use Bonweb\Laracart\Category;

class LaracartAdminFilters extends \LaradminBaseController
{

    public function listAll() {
        return View::make('laracart::filters.list')->withFilters( Filter::all() );
    }

    public function listTrashed() {
        return View::make('laracart::filters.list')->withFilters( Filter::onlyTrashed()->get() );
    }

    public function update($id=0) {
        $filter = Filter::findOrNew($id);
        return View::make('laracart::filters.update')
                ->withFilter( $filter )
                ->withCategories( Category::get()->toTree() );
    }

    public function save($id=0) {
        if (Input::get('saveNew', 0)) {
            $filter = new Filter();
            $filter->slug = '';
        }
        else {
            $filter = Filter::findOrNew($id);
        }

        $data = Input::get('filter', []);
        $filter->fill($data);
        $filter->save();

        $seo = Input::get('seo', []);
        $seo = \Bonweb\Laradmin\Seo::create($seo);
        $filter->seo()->save($seo);

        return Redirect::route('cart.filters.update', [$filter->id])->withMessage( AlertMessage::success('Filter saved') );
    }

    public function delete($id) {
        $o = Filter::withTrashed()->where('id','=',$id)->first();
        if ($o->id) {
            $message = AlertMessage::success("Filter {$o->name} ({$o->id}) deleted");
            if ($o->trashed()) {
                $o->forceDelete();
            }
            else {
                $o->delete();
            }
            return Redirect::back()->withMessage( $message );
        }
        else {
            return Redirect::back()->withMessage( $message = AlertMessage::error("Filter for deletion not found") );
        }
    }

    public function deleteMany($ids) {
        $ids = explode(',', $ids);

        foreach ($ids as $id) {
            $o = Filter::withTrashed()->where('id','=',$id)->first();
            if ($o->id) {
//                if ($o->trashed()) {
                    $o->forceDelete();
//                }
//                else {
//                    $o->delete();
//                }
            }
        }
        $message = AlertMessage::success("Filters deleted");
        return Redirect::back()->withMessage( $message );
    }

    public function restoreTrashed($id) {
        $o = Filter::withTrashed()->where('id','=',$id)->first();
        $o->restore();
        $message = AlertMessage::success("Filter {$o->name} ({$o->id}) deleted");
        return Redirect::back()->withMessage( $message );
    }

}
