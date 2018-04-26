<?php

namespace BadChoice\Mojito\Traits;

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
}
