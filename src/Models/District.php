<?php

namespace Wzije\IndoArea\Models;

use Illuminate\Database\Eloquent\Model;


class District extends Model
{

    protected $connection = 'sqlite_indo_area';

    protected $table = 'reg_districts';

    public function villages()
    {
        return $this->hasMany(Village::class, "district_id", "id");
    }
}
