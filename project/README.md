### Project Management under Biz Boss
##### manage your projects easily

[![Build Status](https://travis-ci.org/joemccann/dillinger.svg?branch=master)](https://travis-ci.org/joemccann/dillinger)

### Why you will choose it
Project Management is a mobile-ready
### Who develop
Author: Noushad Nipun & Samrat Khan
### Installation
Project Management requires [Laravel](https://laravel.com) v8+ to run and [PHP](https://php.net) v7.3+

#### Via Composer
```
composer require tritiyo/project
```

#### Extra Composer Entry

```
"autoload-dev": {
    "psr-4": {
        "Tritiyo\\Project\\":"vendor/tritiyo/project/src/"
    }
},
```

#### Register service provider to app.php under config directory

```
'providers' => [
    Tritiyo\Project\ProjectServiceProvider::class,
]
```

#### Migration for project table

```
php artisan migrate
```

#### Seeds for route data to DatabaseSeeder.php under database/seeders directory

```
public function run()
{
    $this->call(ProjectModuleSeeder::class)
}

```

#### Run seed
```
php artisan db:seed
```

