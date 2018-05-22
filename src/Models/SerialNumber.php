<?php

namespace BadChoice\Mojito\Models;

use BadChoice\Mojito\Traits\TraceabilityTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SerialNumber extends Model
{
    use SoftDeletes;

    protected $table    = "serial_numbers";
    protected $guarded  = ['id'];

    public function stock()
    {
        return $this->hasOne(Stock::class);
    }
}
