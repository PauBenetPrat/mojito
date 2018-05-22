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
            static::TRACEABILITY_NONE                       => trans_choice("admin.none", 1),
            static::TRACEABILITY_LOTS                       => trans_choice("admin.lot", 2),
            static::TRACEABILITY_SERIAL_NUMBERS             => trans_choice("admin.serialNumber", 2),
            static::TRACEABILITY_LOTS_WITH_SERIAL_NUMBERS   => trans_choice("admin.lotsWithSerialNumbers", 1),
        ]);
    }
}
