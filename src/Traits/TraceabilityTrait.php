<?php

namespace BadChoice\Mojito\Traits;

use App\Models\Catalog\Product;
use BadChoice\Mojito\Models\Lot;
use BadChoice\Mojito\Models\SerialNumber;
use BadChoice\Mojito\Models\Traceability;
use Illuminate\Support\Facades\DB;

trait TraceabilityTrait
{

    public function lots()
    {
        return $this->hasMany(Lot::class, 'item_id')->groupBy('lot_number')->addSelect(DB::raw('SUM(quantity) as quantity'));
    }

    public function serialNumbers()
    {
        return $this->hasMany(SerialNumber::class, 'item_id');
    }

    public function lotQuantity($lotNumber)
    {
        return $this->lots()->where('lot_number', $lotNumber)->sum('quantity');
    }

    /*public function usesLots()
    {
        return $this->traceability == Traceability::TRACEABILITY_LOTS || $this->traceability == Traceability::TRACEABILITY_LOTS_WITH_SERIAL_NUMBERS;
    }

    public function usesSerialNumbers()
    {
        return $this->traceability == Traceability::TRACEABILITY_SERIAL_NUMBERS || $this->traceability == Traceability::TRACEABILITY_LOTS_WITH_SERIAL_NUMBERS;
    }

    public function usesLotsWithSerialNumbers()
    {
        return $this->traceability == Traceability::TRACEABILITY_LOTS_WITH_SERIAL_NUMBERS;
    }*/
}
