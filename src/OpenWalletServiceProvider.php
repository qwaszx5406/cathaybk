<?php

namespace Cathaybk\Api;

use Illuminate\Support\ServiceProvider;

class OpenWalletServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
		
		$configPath = __DIR__ . '/../config/cathaybk.php';
        $this->mergeConfigFrom($configPath, 'cathaybk');
		
		$this->app->singleton('OpenWallet', function () {
            return new OpenWallet();
        });
		
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
		$source = realpath($raw = __DIR__ . '/../config/cathaybk.php') ?: $raw;
        $this->publishes([
            $source => config_path('cathaybk.php'),
        ]);
    }
}
