<?php

namespace BadChoice\Mojito\Models;

class Traceability
{
    const TRACEABILITY_NONE                     = 0;
    const TRACEABILITY_LOTS                     = 1;
    const TRACEABILITY_SERIAL_NUMBERS           = 2;
    const TRACEABILITY_LOTS_WITH_SERIAL_NUMBERS = 3;

    public static function all()
    {
        return collect([
            static::TRACEABILITY_NONE                       => __("admin.traceabilityNone"),
            static::TRACEABILITY_LOTS                       => __("admin.traceabilityLots"),
            static::TRACEABILITY_SERIAL_NUMBERS             => __("admin.traceabilitySerialNumbers"),
            static::TRACEABILITY_LOTS_WITH_SERIAL_NUMBERS   => __("admin.traceabilityLotsWithSerialNumbers"),
        ]);
    }

    public static function usesLots($traceability)
    {
        return $traceability == Traceability::TRACEABILITY_LOTS || $traceability == Traceability::TRACEABILITY_LOTS_WITH_SERIAL_NUMBERS;
    }

    public static function usesSerialNumbers($traceability)
    {
        return $traceability == Traceability::TRACEABILITY_SERIAL_NUMBERS || $traceability == Traceability::TRACEABILITY_LOTS_WITH_SERIAL_NUMBERS;
    }

}
