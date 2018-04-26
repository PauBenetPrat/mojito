<?php

namespace BadChoice\Mojito\Traits;

trait TraceabilityTrait
{
    public function scopeByItem($query, $itemId)
    {
        $query = $this->joinWithStocks($query);
        $query = $this->joinWithItems($query);
        if (! $itemId) {
            return $query;
        }
        return $query->where('item_id', $itemId);
    }

    public function scopeByWarehouse($query, $warehouseId)
    {
        $query = $this->joinWithStocks($query);
        if (! $warehouseId) {
            return $query;
        }
        return $query->where('warehouse_id', $warehouseId);
    }

    abstract protected function joinWithStocks($query);

    private function joinWithItems($query)
    {
        if ($this->alreadyJoinedWith($query, config('mojito.itemsTable'))) {
            return $query;
        }
        return $query->join(config('mojito.itemsTable'), config('mojito.itemsTable') . '.id', '=', 'stocks.item_id');
    }

    public function alreadyJoinedWith($query, $table)
    {
        $query = $query->getQuery();
        return $query->from == $table || (collect($query->joins)->pluck('table')->contains($table));
    }
}
