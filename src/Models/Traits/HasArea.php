<?php

namespace Wzije\IndoArea\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Wzije\IndoArea\Models\District;
use Wzije\IndoArea\Models\Province;
use Wzije\IndoArea\Models\Regency;
use Wzije\IndoArea\Models\Village;

trait HasArea
{
    /**
     * Get the mapped column name for the given area type.
     */
    protected function getAreaColumn(string $type): string
    {
        // Default column names
        $defaults = [
            'province'     => 'province',
            'regency'      => 'regency',
            'district'     => 'district',
            'village'      => 'village',
        ];

        // If the model defines an areaColumnMap method, override defaults
        if (method_exists($this, 'areaColumnMap')) {
            return $this->areaColumnMap()[$type] ?? $defaults[$type];
        }

        return $defaults[$type];
    }

    public function province()
    {
        return $this->belongsTo(Province::class, $this->getAreaColumn('province'), 'id');
    }

    public function city()
    {
        return $this->belongsTo(Regency::class, $this->getAreaColumn('regency'), 'id');
    }

    public function subDistrict()
    {
        return $this->belongsTo(District::class, $this->getAreaColumn('district'), 'id');
    }

    public function village()
    {
        return $this->belongsTo(Village::class, $this->getAreaColumn('village'), 'id');
    }

    protected function fullAddress(): Attribute
    {
        return Attribute::get(function () {
            // Dynamically fetch values using the mapped column names

            $parts = array_filter([
                $this->village?->name ? 'Kel. ' . $this->village->name : null,
                $this->subDistrict?->name ? 'Kec. ' . $this->subDistrict->name : null,
                $this->city?->name,
                $this->province?->name,
            ]);

            return implode(', ', $parts);
        });
    }
}
