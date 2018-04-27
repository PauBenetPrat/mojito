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

    protected function joinWithLots($query)
    {
        if ($this->alreadyJoinedWith($query, 'lots')) {
            return $query;
        }
        return $query->join('lots', 'lots.stock_id', '=', 'stocks.id');
    }

    public function scopeByLot($query, $lotId)
    {
        if (! $lotId) {
            return $query;
        }
        $query = $this->joinWithStocks($query);
        $query = $this->joinWithLots($query);
        return $query->where('lot_id', $lotId);
    }
}
