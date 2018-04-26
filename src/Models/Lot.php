<?php

namespace BadChoice\Mojito\Models;

use BadChoice\Mojito\Traits\TraceabilityTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lot extends Model
{
    use TraceabilityTrait;
    use SoftDeletes;

    protected $table    = "lots";
    protected $guarded  = ['id'];

    public function stocks()
    {
        return $this->belongsTo(Stock::class);
    }

    protected function joinWithStocks($query)
    {
        if ($this->alreadyJoinedWith($query, 'stocks')) {
            return $query;
        }
        return $query->join('stocks', 'stocks.id', '=', 'lots.stock_id');
    }
}
