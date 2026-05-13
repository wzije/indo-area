# IndoArea - Laravel Indonesia Regional Data Library

IndoArea is a self-contained Laravel package providing comprehensive Indonesian regional administrative data (BPS standard). It utilizes an internal offline SQLite database, making it exceptionally fast, lightweight, and perfect for high-traffic environments (data pooling) without putting any stress on your primary database (e.g., MySQL).

## Laravel Compatibility

This package is designed to support modern Laravel ecosystems and supports the following framework versions:

| Laravel Version  | PHP Version | Status    |
| :--------------- | :---------- | :-------- |
| **Laravel 10.x** | `^8.2`      | Supported |
| **Laravel 11.x** | `^8.2`      | Supported |
| **Laravel 12.x** | `^8.2`      | Supported |
| **Laravel 13.x** | `^8.2`      | Supported |

## Features

- **Zero Configuration:** Automatically injects and registers the internal SQLite connection into core Laravel.
- **No Database Migration Needed:** Zero impact on your main application database. No messy imports of 80,000+ village rows into your production tables.
- **BPS Standard Code:** Uses pure numeric regional identification codes without dots (e.g., `3273` for Bandung City).

## Installation

You can easily install this package into any Laravel project via Composer:

```bash
composer require wzije/indo-area
```

_(Note: Ensure your package is published on Packagist.org and you have tagged a release version such as `v1.0.0` in your GitHub repository)._

## Usage Guide

You can instantly use the Eloquent models bundled with this package. The models are pre-configured to query the internal SQLite file out-of-the-box.

### 1. Fetch All Provinces

```php
use Wzije\IndoArea\Models\Province;

\$provinces = Province::all();
```

### 2. Fetch Cities Belonging to a Province

```php
use Wzije\IndoArea\Models\Province;

\(province = Province::find(\)provinceId);
\(cities =\)province->cities;
```

### 3. Fetch Subdistricts and Chained Relationships

```php
use Wzije\IndoArea\Models\City;

\(city = City::find(\)cityId);

// Retrieve all subdistricts under this city along with their respective villages
\(subdistricts =\)city->subdistricts()->with('villages')->get();
```

## Available Models

- `Wzije\IndoArea\Models\Country`
- `Wzije\IndoArea\Models\Province`
- `Wzije\IndoArea\Models\City`
- `Wzije\IndoArea\Models\Subdistrict`
- `Wzije\IndoArea\Models\Village`

## License

This library is open-sourced software licensed under the [MIT license](LICENSE).
