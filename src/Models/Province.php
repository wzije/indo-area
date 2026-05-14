<?php

namespace Wzije\IndoArea\Models;

use Illuminate\Database\Eloquent\Model;


class Province extends Model
{

    protected $connection = 'sqlite_indo_area';

    protected $table = 'reg_provinces';

    public function cities()
    {
        return $this->hasMany(Regency::class, 'province_id', "id");
    }
}
