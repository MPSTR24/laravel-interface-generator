
# Laravel Interface Generator

This package is designed to automatically generate TypeScript interfaces from your Laravel models. It supports generating interfaces based on either model fillables or database migrations, it will also try to include relationships by analysing your model methods.

> **Note:** This package has been primarily tested with MySQL. SQLite and other drivers are a work in progress.

![GitHub Actions Workflow Status](https://img.shields.io/github/actions/workflow/status/mpstr24/laravel-interface-generator/run-tests.yml?branch=main)
![Packagist Version](https://img.shields.io/packagist/v/mpstr24/laravel-interface-generator)
![Packagist Downloads](https://img.shields.io/packagist/dt/mpstr24/laravel-interface-generator)

## Features

- Interface Generation: Automatically generate TypeScript interfaces from Laravel
    - Generates interfaces from your model's fillable attributes or from your model's database migration.
- Relationship Detection: Automatically includes relationships in your interfaces.
  - With the ability to disable this feature. 
- Customisable Suffix: Ability to customise or remove a suffix to each interface.
- Model Targeting: Ability to generate interfaces on all or specific models.
## Installation

Install the package via Composer:

```bash
composer require mpstr24/laravel-interface-typer
```



## Usage/Examples

Basic usage.
Run the following to generate interfaces from your migrations with the default suffix "Interface":

```bash
php artisan generate:interfaces
```

### Mode Options
You can choose between generating interfaces from migrations or fillables using the --mode (or -M) option.

- Fillables Mode:
```bash
php artisan generate:interfaces --mode=fillables
```

- Migrations Mode:

```bash
php artisan generate:interfaces --mode=migrations
```

### Suffix Options
Customise the suffix added to your generated interface names using the --suffix (or -S) option.

- Custom Suffix (default "Interface"):
```bash
php artisan generate:interfaces --suffix=Interface
```

- No Suffix:
```bash
php artisan generate:interfaces --suffix=
```

### Model Selection
To generate an interface for a specific model, please use the --model option. If not used or set to all, all models within the "app/Models" directory will be generated.

- Specific Model
```bash
php artisan generate:interfaces --model=TestUser
```

- All Models

```bash
php artisan generate:interfaces --model=all
```

Or alternatively.

```bash
php artisan generate:interfaces
```

### Relationship Toggling
You may not want to always generate relationships in your interfaces, to turn this off please use --relationships=False.

- Relationships enabled (leave blank or set "True")
```bash
php artisan generate:interfaces --relationships=True
```

- Relationships disabled

```bash
php artisan generate:interfaces --relationships=False
```

Or alternatively.

```bash
php artisan generate:interfaces
```

## Roadmap

- [ ] Better Model discovery
- [ ] Better relationship discovery
- [ ] Finalising unknown types within mapping
- [ ] Adding of morphTo
- [ ] Configuration file for wider and customisable default settings
- [ ] Interface export options
- [x] Testing to be implemented
- [ ] Testing to be improved
- [ ] SQLite testing
- [ ] Mapping separation for separate DB drivers


## License

[MIT](https://choosealicense.com/licenses/mit/)

