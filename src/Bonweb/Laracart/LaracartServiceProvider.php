<?php namespace Bonweb\Laracart;

use Illuminate\Support\ServiceProvider;

class LaracartServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('bonweb/laracart');
        include_once __DIR__.'/../../events.php';
        include_once __DIR__.'/../../filters.php';
        include_once __DIR__.'/../../routes.php';
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		/*
		 * Register the service provider for the dependency.
		 */
		$this->app->register('Gloudemans\Shoppingcart\ShoppingcartServiceProvider');
		/*
         * Create aliases for the dependency.
         */
		$loader = \Illuminate\Foundation\AliasLoader::getInstance();
		$loader->alias('Cart', 'Gloudemans\Shoppingcart\Facades\Cart');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
