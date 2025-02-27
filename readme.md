# Laravel InterfaceTyper

This is a Laravel package that aims to generate TypeScript interfaces based off the models with Laravel.

It can generate from either fillables or migrations currently, and also aims to automatically include relationships into
the interfaces by looking for the appropriate methods in your models.

Currently, this package has only been tested with MySQL.

# Installation

To install please run the following command.

```bash
composer require mpstr24/laravel-interface-typer
```

# Usage

For simple usage, running the below will generate the interfaces using your migrations, it will also apply a suffix of "Interface" to each. 

```bash
php artisan generate:interfaces
```

## Mode

You can toggle the mode of which to run, migrations or fillables by using --mode (-M).

```bash
php artisan generate:interfaces --mode=fillables
```

```bash
php artisan generate:interfaces --mode=migrations
```

## Model
You can also choose to generate an interface for a specific model only by using --model. By default, all models will be used.

For example:

```bash
php artisan generate:interfaces --model=User
```

The selection is also case-insensitive. As such, the below command will return the same as the above.

```bash
php artisan generate:interfaces --model=user
```


## Suffix

You can change or remove the suffix using --suffix (-S).

```bash
php artisan generate:interfaces --suffix=Interface
```

For no suffix, please run.

```bash
php artisan generate:interfaces --suffix=
```

# Roadmap

- Better Model discovery
- Better relationship discovery
- Finalising unknown types within mapping
- Adding of morphTo
- Configuration file for wider and customisable default settings
- Interface export options
- Testing to be implemented
