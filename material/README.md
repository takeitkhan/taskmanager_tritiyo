### Material Management under Biz Boss
##### manage your materials easily

[![Build Status](https://travis-ci.org/joemccann/dillinger.svg?branch=master)](https://travis-ci.org/joemccann/dillinger)

### Why you will choose it
Material Management is a mobile-ready
### Who develop
Author: Noushad Nipun & Samrat Khan
### Installation
Material Management requires [Laravel](https://laravel.com) v8+ to run and [PHP](https://php.net) v7.3+

#### Via Composer
```
composer require tritiyo/material
```

#### Extra Composer Entry

```
"autoload-dev": {
    "psr-4": {
        "Tritiyo\\Material\\":"vendor/tritiyo/material/src/"
    }
},
```

#### Register service provider to app.php under config directory

```
'providers' => [
    Tritiyo\Material\MaterialServiceProvider::class,
]
```

#### Migration for material table

```
php artisan migrate
```


#### Seeds for route data to DatabaseSeeder.php under database/seeders directory

```
public function run()
{
    $this->call(MaterialModuleSeeder::class)
}

```

#### Run seed
```
php artisan db:seed
```


