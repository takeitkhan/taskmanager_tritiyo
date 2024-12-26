### Site Management under Biz Boss
##### manage your sites easily

[![Build Status](https://travis-ci.org/joemccann/dillinger.svg?branch=master)](https://travis-ci.org/joemccann/dillinger)

### Why you will choose it
Site Management is a mobile-ready
### Who develop
Author: Noushad Nipun & Samrat Khan
### Installation
Site Management requires [Laravel](https://laravel.com) v8+ to run and [PHP](https://php.net) v7.3+

#### Via Composer
```
composer require tritiyo/site
```

#### Extra Composer Entry

```
"autoload-dev": {
    "psr-4": {
        "Tritiyo\\Site\\":"vendor/tritiyo/site/src/"
    }
},
```

#### Register service provider to app.php under config directory

```
'providers' => [
    Tritiyo\Site\SiteServiceProvider::class,
]
```

#### Migration for site table

```
php artisan migrate
```


#### Seeds for route data to DatabaseSeeder.php under database/seeders directory

```
public function run()
{
    $this->call(SiteModuleSeeder::class)
}

```

#### Run seed
```
php artisan db:seed
```


