<?php

Route::get('category/{slug}', array('as' => 'site.cart.category.view', 'uses' => 'LaracartProducts@viewCategory'));//->where('filters', '(.*)');
Route::get('product/{slug}', array('as' => 'site.cart.product.view', 'uses' => 'LaracartProducts@viewProduct'));

Route::group(array('before' => 'auth|auth.admin|init.admin', 'after' => '', 'prefix' => 'admin'), function() {

//    Route::get('commerce/products', array('as' => 'cart.products.list', 'uses' => 'LaracartAdminProducts@listAll'));
    Route::get('commerce/products', array('as' => 'cart.products.list', 'uses' => 'LaracartAdminProducts@index'));
    Route::get('commerce/products/data', array('as' => 'cart.products.data', 'uses' => 'LaracartAdminProducts@data'));
    Route::get('commerce/products/update/{id?}', array('as' => 'cart.products.update', 'uses' => 'LaracartAdminProducts@update'));
    Route::any('commerce/products/save/{id?}', array('as' => 'cart.products.save', 'uses' => 'LaracartAdminProducts@save'));
    Route::get('commerce/products/delete/{id}', array('as' => 'cart.products.delete', 'uses' => 'LaracartAdminProducts@delete'));
    Route::get('commerce/products/delete-many/{ids}', array('as' => 'cart.products.delete-many', 'uses' => 'LaracartAdminProducts@deleteMany'));
    Route::get('commerce/products/trashed', array('as' => 'cart.products.trashed', 'uses' => 'LaracartAdminProducts@listTrashed'));
    Route::get('commerce/products/restore/{id}', array('as' => 'cart.products.restore', 'uses' => 'LaracartAdminProducts@restoreTrashed'));

    Route::get('commerce/categories', array('as' => 'cart.categories.list', 'uses' => 'LaracartAdminCategories@listAll'));
    Route::get('commerce/categories/update/{id?}', array('as' => 'cart.categories.update', 'uses' => 'LaracartAdminCategories@update'));
    Route::any('commerce/categories/save/{id?}', array('as' => 'cart.categories.save', 'uses' => 'LaracartAdminCategories@save'));
    Route::get('commerce/categories/delete/{id}', array('as' => 'cart.categories.delete', 'uses' => 'LaracartAdminCategories@delete'));
    Route::get('commerce/categories/trashed', array('as' => 'cart.categories.trashed', 'uses' => 'LaracartAdminCategories@listTrashed'));
    Route::get('commerce/categories/restore/{id}', array('as' => 'cart.categories.restore', 'uses' => 'LaracartAdminCategories@restoreTrashed'));
    Route::get('commerce/categories/makeoldcats', array('as' => 'cart.categories.makeoldcats', 'uses' => 'LaracartAdminCategories@makeoldcats'));

    Route::get('commerce/filters', array('as' => 'cart.filters.list', 'uses' => 'LaracartAdminFilters@listAll'));
    Route::get('commerce/filters/update/{id?}', array('as' => 'cart.filters.update', 'uses' => 'LaracartAdminFilters@update'));
    Route::any('commerce/filters/save/{id?}', array('as' => 'cart.filters.save', 'uses' => 'LaracartAdminFilters@save'));
    Route::get('commerce/filters/delete/{id}', array('as' => 'cart.filters.delete', 'uses' => 'LaracartAdminFilters@delete'));
    Route::get('commerce/filters/delete-many/{ids}', array('as' => 'cart.filters.delete-many', 'uses' => 'LaracartAdminFilters@deleteMany'));
    Route::get('commerce/filters/trashed', array('as' => 'cart.filters.trashed', 'uses' => 'LaracartAdminFilters@listTrashed'));
    Route::get('commerce/filters/restore/{id}', array('as' => 'cart.filters.restore', 'uses' => 'LaracartAdminFilters@restoreTrashed'));

});

Route::any('json/categories', array('as' => 'json.categories', 'uses' => 'LaracartAdminCategories@categoriesJson'));

