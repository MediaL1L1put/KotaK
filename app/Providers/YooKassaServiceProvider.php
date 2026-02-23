<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class YooKassaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/yookassa.php', 'yookassa');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Добавляем динамические URL если они не указаны в .env
        if (!$this->app['config']->get('yookassa.return_url')) {
            $this->app['config']->set('yookassa.return_url', url('/payment/success'));
        }
        
        if (!$this->app['config']->get('yookassa.webhook_url')) {
            $this->app['config']->set('yookassa.webhook_url', url('/payment/webhook'));
        }
    }
}