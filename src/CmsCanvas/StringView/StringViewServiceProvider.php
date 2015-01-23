<?php namespace CmsCanvas\StringView;

use Illuminate\Support\ServiceProvider;
use StringView;

class StringViewServiceProvider extends ServiceProvider {

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
		//
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bind('stringview', 'CmsCanvas\StringView\StringView');
        
        // This removes the need to add a facade in the config\app
        // $this->app->booting(function()
        // {
        //     $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        //     $loader->alias('StringView', 'CmsCanvas\StringView\Facades\StringView');
        // });
        StringView::extend(function($value) {
            return preg_replace('/\@define(.+)/', '<?php ${1}; ?>', $value);
        });

        StringView::extend(function($value, $compiler) {
            return preg_replace('/\@entries\s*\(\'?([a-zA-Z0-9_]+)\'?\s*,\s*(.+)\)/', '<?php $${1} = Content::entries(${2}); ?>', $value);
        });
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