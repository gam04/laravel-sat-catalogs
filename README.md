# Gam/laravel-sat-catalogs

[![Total Downloads](https://img.shields.io/packagist/dt/gam/laravel-sat-catalogs.svg?style=flat-square)](https://packagist.org/packages/gam/laravel-sat-catalogs)
![GitHub Workflow Status](https://img.shields.io/github/workflow/status/gam04/laravel-sat-catalogs/build?style=flat-square)
![GitHub](https://img.shields.io/github/license/gam04/laravel-sat-catalogs?style=flat-square)
![GitHub release (latest by date)](https://img.shields.io/github/v/release/gam04/laravel-sat-catalogs?style=flat-square)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/gam/laravel-sat-catalogs?style=flat-square)

Simple package providing an Artisan command to update the SAT Catalogs.
You can access them through the  `Catalog` Facade.

## Installation

You can install the package via composer:

```bash
composer require gam/laravel-sat-catalogs
```

## Usage

0. For lumen applications, register the provider in `bootstrap/app.php` file,
   add the following line:
   ```
   $app->register(\Gam\LaravelSatCatalogs\CatalogsServiceProvider::class);
   ```

1. Set a sqlite3 connection for the driver `catalogs`. You can change the driver name in
   `config/catalogs.php`.  
   Example:
   ```php
   <?php
   /* Custom driver for CFDI Catalogs */
        'catalogs' => [
            'driver' => 'sqlite',
            'url' => '',
            'database' => database_path('catalogs.sqlite3'),
            'prefix' => '',
            'foreign_key_constraints' => true,
        ],
   ```

2. Update the catalogs database
   ```shell
   php artisan catalogs:update --path={$MY_PATH}
   ```
3. Access to the catalogs using `Catalog` Facade: 
   ```php 
   # check if catalog exists
   Catalog::exists('cfdi_40_productos_servicios');
   
   # get a list of catalogs name
   Catalog::availables()
   
   # Get a Query Builder instance
   $ps = Catalog::of('cfdi_40_productos_servicios');
   echo $ps->find('10161511')->texto
   
   # Get the text value of certain row
   $monedaText = Catalog::textOf('cfdi_40_monedas', 'MXN');
   echo $monedaText; # Peso Méxicano
   ```

### Testing

```bash
composer dev:test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email antgam95@gmail.com instead of using the issue tracker.

## Credits

-   [Gamboa Aguirre](https://github.com/gam04)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
