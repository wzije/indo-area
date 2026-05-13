# IndoArea - Laravel Indonesia Regional Data Library

IndoArea is a self-contained Laravel package providing comprehensive Indonesian regional administrative data (BPS standard). It utilizes an internal offline SQLite database, making it exceptionally fast, lightweight, and perfect for high-traffic environments (data pooling) without putting any stress on your primary database (e.g., MySQL).

## Laravel Compatibility

This package is designed to support modern Laravel ecosystems and supports the following framework versions:

| Laravel Version  | PHP Version | Status    |
| :--------------- | :---------- | :-------- |
| **Laravel 10.x** | `^8.3`      | Supported |
| **Laravel 11.x** | `^8.3`      | Supported |
| **Laravel 12.x** | `^8.3`      | Supported |
| **Laravel 13.x** | `^8.3`      | Supported |

## Features

- **Zero Configuration:** Automatically injects and registers the internal SQLite connection into core Laravel.
- **No Database Migration Needed:** Zero impact on your main application database. No messy imports of 80,000+ village rows into your production tables.
- **BPS Standard Code:** Uses pure numeric regional identification codes without dots (e.g., `3273` for Bandung City).
- **Built-in API Endpoints:** Ships with pre-configured routes for seamless asynchronous chaining dropdown selectors.

## Installation

You can easily install this package into any Laravel project via Composer:

```bash
composer require wzije/indo-area
```

## Available API Endpoints

The library automatically registers the following REST API routes under the `api/indo-area` prefix:

| Method  | Endpoint                                      | Description                      | JSON Response Keys                       |
| :------ | :-------------------------------------------- | :------------------------------- | :--------------------------------------- |
| **GET** | `/api/indo-area/provinces`                    | Get all provinces                | `province_code`, `province_name`         |
| **GET** | `/api/indo-area/provinces/{code}/cities`      | Get cities by province code      | `city_code`, `city_name`                 |
| **GET** | `/api/indo-area/cities/{code}/subdistricts`   | Get subdistricts by city code    | `sub_district_code`, `sub_district_name` |
| **GET** | `/api/indo-area/subdistricts/{code}/villages` | Get villages by subdistrict code | `village_code`, `village_name`           |

---

## Usage Guide

### Backend Eloquent Model Queries

You can instantly use the Eloquent models bundled with this package. The models are pre-configured to query the internal SQLite file out-of-the-box.

#### 1. Fetch All Provinces

```php
use Wzije\IndoArea\Models\Province;

$provinces = Province::all();
```

#### 2. Fetch Cities Belonging to a Province

```php
use Wzije\IndoArea\Models\Province;

province = Province::find(provinceId);
cities = province->cities;
```

#### 3. Fetch Subdistricts and Chained Relationships

```php
use Wzije\IndoArea\Models\City;

city = City::find(cityId);

// Retrieve all subdistricts under this city along with their respective villages
subdistricts =city->subdistricts()->with('villages')->get();
```

---

## Frontend Integration (Chained Cascading Dropdowns)

Here is a complete vanilla JavaScript example to implement dynamic, reactive select dropdown elements inside your views using the package endpoints:

### 1. HTML Select Inputs Structure

```html
<select
  id="region-province"
  data-selected="{{ old('province', \$currentProvince) }}"
>
  <option value="">Select Province</option>
</select>

<select
  id="region-city"
  data-selected="{{ old('city', \$currentCity) }}"
  disabled
>
  <option value="">Select City</option>
</select>

<select
  id="region-subdistrict"
  data-selected="{{ old('sub_district', \$currentSubDistrict) }}"
  disabled
>
  <option value="">Select Subdistrict</option>
</select>

<select
  id="region-village"
  data-selected="{{ old('village', \$currentVillage) }}"
  disabled
>
  <option value="">Select Village</option>
</select>
```

### 2. Cascading Script

Add this script directly to your view template or global asset stack:

```javascript
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const provinceSelect = document.getElementById('region-province');
        const citySelect = document.getElementById('region-city');
        const subdistrictSelect = document.getElementById('region-subdistrict');
        const villageSelect = document.getElementById('region-village');

        // Execute initial data pull on page load
        if (provinceSelect) {
            loadProvinces();
        }

        function loadProvinces() {
            fetch('/api/indo-area/provinces')
                .then(res => res.json())
                .then(data => {
                    populateSelect(provinceSelect, data, 'province_code', 'province_name');
                    if (provinceSelect.dataset.selected) {
                        provinceSelect.value = provinceSelect.dataset.selected;
                        loadCities(provinceSelect.value);
                    }
                }).catch(err => console.error('Error fetching provinces:', err));

            provinceSelect.addEventListener('change', function () {
                resetSelect(citySelect, 'Select City');
                resetSelect(subdistrictSelect, 'Select Subdistrict');
                resetSelect(villageSelect, 'Select Village');
                if (this.value) loadCities(this.value);
            });
        }

        function loadCities(provinceCode) {
            citySelect.disabled = false;
            citySelect.classList.remove('bg-gray-50');
            fetch(`/api/indo-area/provinces/${provinceCode}/cities`)
                .then(res => res.json())
                .then(data => {
                    populateSelect(citySelect, data, 'city_code', 'city_name');
                    if (citySelect.dataset.selected) {
                        citySelect.value = citySelect.dataset.selected;
                        citySelect.dataset.selected = ''; // Clear after initial restore
                        loadSubdistricts(citySelect.value);
                    }
                });

            citySelect.addEventListener('change', function () {
                resetSelect(subdistrictSelect, 'Select Subdistrict');
                resetSelect(villageSelect, 'Select Village');
                if (this.value) loadSubdistricts(this.value);
            });
        }

        function loadSubdistricts(cityCode) {
            subdistrictSelect.disabled = false;
            subdistrictSelect.classList.remove('bg-gray-50');
            fetch(`/api/indo-area/cities/${cityCode}/subdistricts`)
                .then(res => res.json())
                .then(data => {
                    populateSelect(subdistrictSelect, data, 'sub_district_code', 'sub_district_name');
                    if (subdistrictSelect.dataset.selected) {
                        subdistrictSelect.value = subdistrictSelect.dataset.selected;
                        subdistrictSelect.dataset.selected = '';
                        loadVillages(subdistrictSelect.value);
                    }
                });

            subdistrictSelect.addEventListener('change', function () {
                resetSelect(villageSelect, 'Select Village');
                if (this.value) loadVillages(this.value);
            });
        }

        function loadVillages(subdistrictCode) {
            if (!villageSelect) return;
            villageSelect.disabled = false;
            villageSelect.classList.remove('bg-gray-50');
            fetch(`/api/indo-area/subdistricts/${subdistrictCode}/villages`)
                .then(res => res.json())
                .then(data => {
                    populateSelect(villageSelect, data, 'village_code', 'village_name');
                    if (villageSelect.dataset.selected) {
                        villageSelect.value = villageSelect.dataset.selected;
                        villageSelect.dataset.selected = '';
                    }
                });
        }

        function populateSelect(element, data, codeField, nameField) {
            element.innerHTML = `<option value="">${element.options[0].text}</option>`;
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item[codeField];
                option.textContent = item[nameField];
                element.appendChild(option);
            });
        }

        function resetSelect(element, placeholder) {
            if (!element) return;
            element.innerHTML = `<option value="">${placeholder}</option>`;
            element.disabled = true;
            element.classList.add('bg-gray-50');
        }
    });
</script>
```

---

## Available Models

- `Wzije\IndoArea\Models\Country`
- `Wzije\IndoArea\Models\Province`
- `Wzije\IndoArea\Models\City`
- `Wzije\IndoArea\Models\SubDistrict`
- `Wzije\IndoArea\Models\Village`

## License

This library is open-sourced software licensed under the [MIT license](LICENSE).
