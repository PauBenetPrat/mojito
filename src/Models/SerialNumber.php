<?php

namespace BadChoice\Mojito\Models;

use BadChoice\Mojito\Traits\TraceabilityTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SerialNumber extends Model
{
    use SoftDeletes;
    use TraceabilityTrait;

    protected $table    = "serial_numbers";
    protected $guarded  = ['id'];

    public function stock()
    {
        return $this->hasOne(Stock::class);
    }

    protected function joinWithStocks($query)
    {
        if ($this->alreadyJoinedWith($query, 'stocks')) {
            return $query;
        }
        return $query->join('stocks', 'stocks.id', '=', 'serial_numbers.stock_id');
    }
}
