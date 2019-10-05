<?php
namespace Tareq\Xcurd;

use Illuminate\Support\ServiceProvider;

class XcurdServiceProvider extends ServiceProvider
{

    public function boot()
    {

        if ($this->app->runningInConsole()) {
	        $this->commands([
	            Commands\Xcurd::class,
	        ]);
    	}

    	$this->app->bind('Tareq\Xcurd\Helpers\Xform', function() {
		    return new Xform();
		});

		$this->app->bind('Tareq\Xcurd\Helpers\Xtable', function() {
		    return new Xtable();
		});
        
    }


    public function register()
    {

    }
}