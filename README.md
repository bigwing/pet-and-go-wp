# PetAndGo WP
PHP library for interacting with the PetAndGo.com API within a WordPress site.

[![PDS Skeleton](https://img.shields.io/badge/pds-skeleton-blue.svg?style=flat-square)](https://github.com/php-pds/skeleton)
[![GitHub license](https://img.shields.io/github/license/bigwing/pet-and-go-wp)](https://github.com/bigwing/pet-and-go-wp/blob/master/LICENSE)

## Installation
Install using Composer `composer require bigwing/pet-and-go-wp` and use the Composer autoloader.

## Configuration
The only required setting is your PetAndGo API key (`authkey`), which must be passed to the class on instantiation.
Common ways to do this:
- In `wp-config.php`, set a constant with `define( 'PET_AND_GO_AUTHKEY', 'your_auth_key' );`.
- Use dotEnv to set the key.
- Create a WP settings page and store the key there.

## Usage
You can use this inside a theme or plugin, but this package DOES NOT dictate front-end output.

- Use the Composer autoloader to ensure the files are loaded.
- Instantiate the class with `new PetAndGo\PetAndGo( PET_AND_GO_AUTHKEY );`.

Note: You may choose to assign it to a variable if you're going to use it right away,
but you can also create the class in your main `functions.php` file and use
`PetAndGo::get_instance();` in any templates to get the main instance.

### Getting pets list:
You can pass a species name to `get_adoptable_pets()` for a specific pet type. Currently, only "cat", "dog", or "all"
are supported.

```php
$pet_search = BigWing\PetAndGo\PetAndGo::get_instance();
$pets = $pet_search->get_adoptable_pets( 'dog' );
```
