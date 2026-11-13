<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Product;
use App\Observers\ProductObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(\Elastic\Elasticsearch\Client::class, static function (): \Elastic\Elasticsearch\Client {
            // Мы явно создаем Guzzle клиент, реализующий ClientInterface
            $guzzleClient = new \GuzzleHttp\Client([
                'timeout' => (int)config('elasticsearch.timeout', 10),
                'connect_timeout' => 2,
            ]);

            $builder = \Elastic\Elasticsearch\ClientBuilder::create()
                ->setHosts([(string)config('elasticsearch.host')])
                ->setHttpClient($guzzleClient); // Передаем его принудительно

            if (config('elasticsearch.api_key')) {
                $builder->setApiKey((string)config('elasticsearch.api_key'));
            }

            return $builder->build();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Product::observe(ProductObserver::class);
    }
}
