<?php

namespace Wzije\IndoArea\Models;

use Illuminate\Database\Eloquent\Model;


class Village extends Model
{

    protected $connection = 'sqlite_indo_area';

    protected $table = 'reg_villages';
}
