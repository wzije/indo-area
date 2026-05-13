<?php

namespace Wzije\IndoArea\Models;

use Illuminate\Database\Eloquent\Model;


class Country extends Model
{

    protected $connection = 'sqlite_indo_area';

    protected $table = 'counries';

    public function provinces()
    {
        return $this->hasMany(Province::class, 'province_country_code');
    }
}
