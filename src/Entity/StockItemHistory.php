<?php
declare(strict_types=1);

namespace App\Entity;
use DateTime;


class StockItemHistory
{
    public int $id;

    public int $delta;

    public StockItem $stockItem;


    public string $reason;

    public ?DateTime $createdAt = null;
    public ?DateTime $updatedAt = null;

    function __construct(
        int $id,
        int $delta,
        StockItem $stockItem,
        string $reason,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null
    )
    {
        $this->id = $id;
        $this->delta = $delta;
        $this->stockItem = $stockItem;
        $this->reason = $reason;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        
    }


}