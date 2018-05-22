<?php

namespace BadChoice\Mojito\Models;

use BadChoice\Mojito\Traits\TraceabilityTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lot extends Model
{
    use SoftDeletes;

    protected $table    = "lots";
    protected $guarded  = ['id'];

    public function stocks()
    {
        return $this->belongsTo(Stock::class);
    }
}
