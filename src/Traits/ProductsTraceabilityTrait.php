<?php

namespace BadChoice\Mojito\Traits;

use App\Models\Catalog\Product;
use BadChoice\Mojito\Models\Traceability;

trait ProductsTraceabilityTrait
{
    public function usesLots()
    {
        return $this->traceability == Traceability::TRACEABILITY_LOTS || $this->traceability == Traceability::TRACEABILITY_LOTS_WITH_SERIAL_NUMBERS;
    }

    public function usesSerialNumbers()
    {
        return $this->traceability == Traceability::TRACEABILITY_SERIAL_NUMBERS || $this->traceability == Traceability::TRACEABILITY_LOTS_WITH_SERIAL_NUMBERS;
    }

    public function scopeSerialNumbers($query)
    {
        return $query->where('traceability', '=', Traceability::TRACEABILITY_SERIAL_NUMBERS)->orWhere('traceability', '=', Traceability::TRACEABILITY_LOTS_WITH_SERIAL_NUMBERS);
    }

    public function scopeLots($query)
    {
        return $query->where('traceability', '=', Traceability::TRACEABILITY_LOTS)->orWhere('traceability', '=', Traceability::TRACEABILITY_LOTS_WITH_SERIAL_NUMBERS);
    }
}
