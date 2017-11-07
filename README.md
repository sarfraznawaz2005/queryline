# Laravel QueryLine

[![Code Climate](https://codeclimate.com/github/sarfraznawaz2005/queryline/badges/gpa.svg)](https://codeclimate.com/github/sarfraznawaz2005/queryline)
[![laravel 5.1](https://img.shields.io/badge/Laravel-5.1-brightgreen.svg?style=flat-square)](http://laravel.com)
[![laravel 5.2](https://img.shields.io/badge/Laravel-5.2-brightgreen.svg?style=flat-square)](http://laravel.com)
[![laravel 5.3](https://img.shields.io/badge/Laravel-5.3-brightgreen.svg?style=flat-square)](http://laravel.com)
[![downloads](https://poser.pugx.org/sarfraznawaz2005/queryline/downloads)](https://packagist.org/packages/sarfraznawaz2005/queryline)

## Introduction ##

QueryLine is a laravel package to show time graph against run queries on a page thereby allowing to see which are slow/fast queries running on the page.

## Screenshot ##

![Main Window](https://raw.github.com/sarfraznawaz2005/queryline/master/screen.png)

## Requirements ##

 - PHP >= 5.6
 - Laravel 5

## Installation ##

Install via composer

```
composer require sarfraznawaz2005/queryline
```

Add Service Provider to `config/app.php` in `providers` section
```php
Sarfraznawaz2005\QueryLine\ServiceProvider::class,
```

Run `php artisan vendor:publish` to publish package's config file. You should now have `queryline.php` file published in `app/config` folder.

## Config Options ##

 - `enabled` : Enable or disable QueryLine. By default it is disabled. If you are on local environment, you can also just add `QUERYLINE=true` to env file to enable it.
 - `querystring_name` : Whatever value for this config is set, you will be able to see all running quries by appending this value in your url as query string. Example: `http://www.yourapp.com/someurl?vvv`. Default value is `vvv`.
 
## Related Package ##

[QueryDumper](https://github.com/sarfraznawaz2005/querydumper)

## License ##

This code is published under the [MIT License](http://opensource.org/licenses/MIT).
This means you can do almost anything with it, as long as the copyright notice and the accompanying license file is left intact.
