<?php

namespace NewJapanOrders\EmailActivation;

use Illuminate\Support\ServiceProvider as Provider;
use Illuminate\Support\Str;
use NewJapanOrders\EmailActivation\AccountManager;

class ServiceProvider extends Provider
{

    /** 
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {   
    }   

    /** 
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {   
        $this->registerManager();
        $this->registerCommand();
    }
    
    private function registerManager()
    {
        $this->app->singleton('issuer', function ($app) {
            return new AccountManager($app);
        }); 
    }

    private function registerCommand()
    {
        $this->app->singleton('command.njo.email-activation.migration-publish', function ($app) {
            return $app['NewJapanOrders\EmailActivation\Commands\MigrationPublishCommand'];
        }); 
        $this->commands('command.njo.email-activation.migration-publish');
    }
}
