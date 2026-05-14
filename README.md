# IndoArea - Laravel Indonesia Regional Data Library

IndoArea is a self-contained Laravel package providing comprehensive Indonesian regional administrative data (BPS standard). It utilizes an internal offline SQLite database, making it exceptionally fast, lightweight, and perfect for high-traffic environments without putting any stress on your primary database (e.g., MySQL).

## Laravel Compatibility

| Laravel Version  | PHP Version | Status    |
| :--------------- | :---------- | :-------- |
| **Laravel 10.x** | `^8.3`      | Supported |
| **Laravel 11.x** | `^8.3`      | Supported |
| **Laravel 12.x** | `^8.3`      | Supported |
| **Laravel 12.x** | `^8.3`      | Supported |

## Features

- **Zero Configuration:** Automatically injects and registers the internal SQLite connection.
- **No Database Migration Needed:** No messy imports of 80,000+ village rows into your production tables.
- **BPS Standard Code:** Uses pure numeric regional identification codes without dots.
- **HasArea Trait:** Easily link your models to regional data with built-in relationships and full address formatting.

## Installation

```bash
composer require wzije/indo-area
```

---

## Available API Endpoints

The library automatically registers the following REST API routes under the `api/indo-area` prefix:

| Method  | Endpoint                                    | Description                    | JSON Response Keys |
| ------- | ------------------------------------------- | ------------------------------ | ------------------ |
| **GET** | `/api/indo-area/provinces`                  | Get all provinces              | `id`, `name`       |
| **GET** | `/api/indo-area/provinces/{code}/regencies` | Get regencies by province code | `id`, `name`       |
| **GET** | `/api/indo-area/regencies/{code}/districts` | Get districts by regency code  | `id`, `name`       |
| **GET** | `/api/indo-area/districts/{code}/villages`  | Get villages by district code  | `id`, `name`       |

---

## Usage Guide

### 1. Linking Your Models (HasArea Trait)

You can use the `HasArea` trait in your models (e.g., `User` or `Address`) to instantly gain relationships to the regional data.

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Wzije\IndoArea\Models\Traits\HasArea;

class Address extends Model
{
    use HasArea;

    /**
     * Optional: Map your custom column names if they differ from defaults
     * Defaults: province, regency, district, village
     */
    public function areaColumnMap(): array
    {
        return [
            'province' => 'province_id',
            'regency'  => 'city_id',
            'district' => 'district_id',
            'village'  => 'village_id',
        ];
    }
}

```

**Benefits of HasArea:**

- **Relationships:** Access via `$model->province`, `$model->city`, `$model->subDistrict`, and `$model->village`.
- **Full Address:** Get a formatted string via `$model->full_address`.

---

### 2. Backend Eloquent Model Queries

#### Fetch All Provinces

```php
use Wzije\IndoArea\Models\Province;

$provinces = Province::all();

```

#### Fetch Regencies Belonging to a Province

```php
use Wzije\IndoArea\Models\Province;

$province = Province::find($provinceId);
$regencies = $province->regencies;

```

---

### 3. Frontend Integration (Chained Dropdowns)

#### HTML Structure

```html
<select
  id="region-province"
  data-selected="{{ old('province', $user->province_id) }}"
>
  <option value="">Select Province</option>
</select>

<select
  id="region-regency"
  data-selected="{{ old('regency', $user->regency_id) }}"
  disabled
>
  <option value="">Select Regency</option>
</select>

<select
  id="region-district"
  data-selected="{{ old('district', $user->district_id) }}"
  disabled
>
  <option value="">Select District</option>
</select>

<select
  id="region-village"
  data-selected="{{ old('village', $user->village_id) }}"
  disabled
>
  <option value="">Select Village</option>
</select>
```

#### JavaScript Logic

```javascript
document.addEventListener("DOMContentLoaded", function () {
  const provinceSelect = document.getElementById("region-province");
  const regencySelect = document.getElementById("region-regency");
  const districtSelect = document.getElementById("region-district");
  const villageSelect = document.getElementById("region-village");

  if (provinceSelect) loadProvinces();

  function loadProvinces() {
    fetch("/api/indo-area/provinces")
      .then((res) => res.json())
      .then((data) => {
        populateSelect(provinceSelect, data, "id", "name");
        if (provinceSelect.dataset.selected) {
          provinceSelect.value = provinceSelect.dataset.selected;
          loadRegencies(provinceSelect.value);
        }
      });

    provinceSelect.addEventListener("change", function () {
      resetSelect(regencySelect, "Select Regency");
      resetSelect(districtSelect, "Select District");
      resetSelect(villageSelect, "Select Village");
      if (this.value) loadRegencies(this.value);
    });
  }

  function loadRegencies(provinceCode) {
    enableSelect(regencySelect);
    fetch(`/api/indo-area/provinces/${provinceCode}/regencies`)
      .then((res) => res.json())
      .then((data) => {
        populateSelect(regencySelect, data, "id", "name");
        if (regencySelect.dataset.selected) {
          regencySelect.value = regencySelect.dataset.selected;
          regencySelect.dataset.selected = "";
          loadDistricts(regencySelect.value);
        }
      });

    regencySelect.addEventListener("change", function () {
      resetSelect(districtSelect, "Select District");
      resetSelect(villageSelect, "Select Village");
      if (this.value) loadDistricts(this.value);
    });
  }

  function loadDistricts(regencyCode) {
    enableSelect(districtSelect);
    fetch(`/api/indo-area/regencies/${regencyCode}/districts`)
      .then((res) => res.json())
      .then((data) => {
        populateSelect(districtSelect, data, "id", "name");
        if (districtSelect.dataset.selected) {
          districtSelect.value = districtSelect.dataset.selected;
          districtSelect.dataset.selected = "";
          loadVillages(districtSelect.value);
        }
      });

    districtSelect.addEventListener("change", function () {
      resetSelect(villageSelect, "Select Village");
      if (this.value) loadVillages(this.value);
    });
  }

  function loadVillages(districtCode) {
    if (!villageSelect) return;
    enableSelect(villageSelect);
    fetch(`/api/indo-area/districts/${districtCode}/villages`)
      .then((res) => res.json())
      .then((data) => {
        populateSelect(villageSelect, data, "id", "name");
        if (villageSelect.dataset.selected) {
          villageSelect.value = villageSelect.dataset.selected;
          villageSelect.dataset.selected = "";
        }
      });
  }

  function populateSelect(element, data, codeField, nameField) {
    const placeholder = element.options[0].text;
    element.innerHTML = `<option value="">${placeholder}</option>`;
    data.forEach((item) => {
      const option = document.createElement("option");
      option.value = item[codeField];
      option.textContent = item[nameField];
      element.appendChild(option);
    });
  }

  function enableSelect(element) {
    element.disabled = false;
    element.classList.remove("bg-gray-50");
  }

  function resetSelect(element, placeholder) {
    if (!element) return;
    element.innerHTML = `<option value="">${placeholder}</option>`;
    element.disabled = true;
    element.classList.add("bg-gray-50");
  }
});
```

---

## Available Models

- `Wzije\IndoArea\Models\Province`
- `Wzije\IndoArea\Models\Regency`
- `Wzije\IndoArea\Models\District`
- `Wzije\IndoArea\Models\Village`

## License

This library is open-sourced software licensed under the [MIT license](https://www.google.com/search?q=LICENSE).
