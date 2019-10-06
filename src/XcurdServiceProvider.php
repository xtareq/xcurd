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
        
    }


    public function register()
    {

    }
}