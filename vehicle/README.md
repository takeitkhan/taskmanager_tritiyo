### Vehicle Management under Biz Boss
##### manage your vehicles easily

[![Build Status](https://travis-ci.org/joemccann/dillinger.svg?branch=master)](https://travis-ci.org/joemccann/dillinger)

### Why you will choose it
Vehicle Management is a mobile-ready
### Who develop
Author: Noushad Nipun & Samrat Khan
### Installation
Vehicle Management requires [Laravel](https://laravel.com) v8+ to run and [PHP](https://php.net) v7.3+

#### Via Composer
```
composer require tritiyo/vehicle
```

#### Extra Composer Entry

```
"autoload-dev": {
    "psr-4": {
        "Tritiyo\\Vehicle\\":"vendor/tritiyo/vehicle/src/"
    }
},
```

#### Register service provider to app.php under config directory

```
'providers' => [
    Tritiyo\Vehicle\VehicleServiceProvider::class,
]
```

#### Migration for vehicle table

```
php artisan migrate
```


#### Seeds for route data to DatabaseSeeder.php under database/seeders directory

```
public function run()
{
    $this->call(VehicleModuleSeeder::class)
}

```

#### Run seed
```
php artisan db:seed
```


