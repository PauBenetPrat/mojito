<?php

namespace BadChoice\Mojito\Models;

use App\Exceptions\DuplicatedSerialNumberException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use BadChoice\Grog\Traits\SyncTrait;

class Stock extends Model
{
    use SoftDeletes;
    use SyncTrait;

    protected $dates            = ['deleted_at'];
    protected $hidden           = ['created_at','updated_at','deleted_at'];
    protected $guarded          = [];

    protected static $rules = [
        'alert'     => 'integer',
        'quantity'  => 'numeric'
    ];

    public static function findWith($item_id, $warehouse_id)
    {
        return static::where('item_id', $item_id)
            ->where('warehouse_id', $warehouse_id)
            ->first();
    }

    //============================================================================
    // RELATIONSHIPS
    //============================================================================
    public function warehouse()
    {
        return $this->belongsTo(config('mojito.warehouseClass', 'Item'), 'warehouse_id');
    }

    public function item()
    {
        return $this->belongsTo(config('mojito.itemClass', 'Item'), 'item_id');
    }

    public function unit()
    {
        return $this->belongsTo('BadChoice\Mojito\Models\Unit');
    }

    public function serialNumbers(){
        return $this->hasMany('BadChoice\Mojito\Models\SerialNumber');
    }

    public function lots(){
        return $this->hasMany('BadChoice\Mojito\Models\Lot');
    }

    //============================================================================
    // SCOPES
    //============================================================================
    public function scopeByWarehouse($query, $id)
    {
        return $query->where('warehouse_id', '=', $id);
    }

    public function scopeInventoryAlert($query)
    {
        return $query->whereRaw('alert >= quantity');
    }

    public function scopeByItem($query, $id)
    {
        return $query->where('item_id', '=', $id);
    }

    //============================================================================
    // METHODS
    //============================================================================
    public function addLot($lotNumber, $quantity, $expirationDate = null)
    {
        $lot = $this->lots()->firstOrCreate([
            "lot_number"        => $lotNumber,
            "item_id"           => $this->item_id,
        ], [
            "expiration_date"   => $expirationDate,
        ]);
        $lot->increment("quantity", $quantity);
//        $this->increment('quantity', $quantity);
        return $lot;
    }

    public function addSerialNumbers($serialNumbers, $lotNumber = null)
    {
        $this->validateNotDuplicatedSerialNumbers($serialNumbers);
        $lot = $lotNumber ? $this->addLot($lotNumber, count($serialNumbers)) : null;
        $serialNumbers = $this->serialNumbers()->createMany(collect($serialNumbers)->map(function ($serialNumber) use ($lot) {
            return [
                "serial_number" => $serialNumber,
                "item_id"       => $this->item_id,
                "lot_id"        => $lot->id ?? null,
            ];
        })->toArray());
        /*if (! $lot) {
            $this->increment('quantity', $serialNumbers->count());
        }*/
        return $serialNumbers;
    }

    public function toRefill()
    {
        return $this->defaultQuantity - $this->quantity;
    }

    public static function softDelete($item_id, $warehouse_id)
    {
        static::findWith($item_id, $warehouse_id)->delete();
    }

    protected function validateNotDuplicatedSerialNumbers($serialNumbers)
    {
        if (count($serialNumbers) != count(array_unique($serialNumbers))) {
            throw new DuplicatedSerialNumberException;
        }
        if ($this->item->serialNumbers()->whereIn('serial_number', $serialNumbers)->count() > 0) {
            throw new DuplicatedSerialNumberException;
        }
    }
}
