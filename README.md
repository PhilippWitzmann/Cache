# Cache

This library aims to provide basic functionality to work with (testable, interchangable) 
Cache implementations

## Installation

````bash
composer require philippwitzmann/cache
````

## Usage

### Create an instance

```php
$dateTimeHandler = new DateTimeHandler();
$arrayCache      = new ArrayCache($dateTimeHandler);
```

### Set Values
```php
$client = new Client([
    'scheme' => 'tcp',
    'host'   => '127.0.0.1',
    'port'   => 6379,
]);
$dateTimeHandler = new DateTimeHandler();
$arrayCache      = new ArrayCache($dateTimeHandler);
$redisCache      = new RedisCache($dateTimeHandler, $client);
$arrayCache->set('key', 'value', 600); // 600 is Lifetime In Seconds so 10Minutes.
$redisCache->set('key', 'value', 600); // 600 is Lifetime In Seconds so 10Minutes.

echo $arrayCache->get('key'); // Outputs: 'value'
```

## Running tests
```bash
php vendor/bin/phpunit --configuration config/phpunit.xml
```