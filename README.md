# Gam/laravel-sat-catalogs

[![Latest Version on Packagist](https://img.shields.io/packagist/v/gam/laravel-sat-catalogs.svg?style=flat-square)](https://packagist.org/packages/gam/laravel-sat-catalogs)
[![Total Downloads](https://img.shields.io/packagist/dt/gam/laravel-sat-catalogs.svg?style=flat-square)](https://packagist.org/packages/gam/laravel-sat-catalogs)
![GitHub Actions](https://github.com/gam/laravel-sat-catalogs/actions/workflows/main.yml/badge.svg)

Simple package providing an Artisan command to update the SAT Catalogs.
You can access them through the  `Catalog` Facade.

## Installation

You can install the package via composer:

```bash
composer require gam/laravel-sat-catalogs
```

## Usage

1. Set a sqlite3 connection for the driver `catalogs`. You can change the driver name in
   `catalogs.php`.  
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
   php artisan catalogs:update
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
