<?php

declare(strict_types=1);

return [
    'host' => env('ELASTICSEARCH_HOST', 'http://elasticsearch:9200'),
    'index' => env('ELASTICSEARCH_INDEX', 'shop-products'),
    'api_key' => env('ELASTICSEARCH_API_KEY'),
    'timeout' => (int)env('ELASTICSEARCH_TIMEOUT', 10),
    'queue' => 'products.search',
    'queue_connection' => env('ELASTICSEARCH_QUEUE_CONNECTION', 'rabbitmq'),
];
