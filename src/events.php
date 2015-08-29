<?php

Event::listen('admin.top-left-menu', function(){
    if ( Route::getCurrentRoute()->getPrefix() == 'admin' ) {
        $menu['products'] = array(
            'label' => 'Products',
            'href' => route('cart.products.list'),
            'icon' => 'fa-cogs',
        );
        $menu['categories'] = array(
            'label' => 'Categories',
            'href' => route('cart.categories.list'),
            'icon' => 'fa-cogs',
        );
        $menu['filters'] = array(
            'label' => 'Filters',
            'href' => route('cart.filters.list'),
            'icon' => 'fa-cogs',
        );
        $menu['dividerlc1'] = 'divider';
        $menu['settings'] = array(
            'label' => 'Settings',
            'href' => '#',
            'icon' => 'fa-cogs',
            'submenu' => array(
                array(
                    'label' => 'Product Meta Fields',
                    'href' => route('settings', ['laracart::product-meta']),
                    'icon' => 'fa-list-ul',
                ),
            )
        );
        return array(
            'ecommerce' => array(
                'label' => 'E-Commerce',
                'href' => '#',
                'icon' => 'fa-file-text-o',
                'submenu' => $menu
            ),
        );
    }
    else return [];
}, 999980);