<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\StockItem;
use App\Manager\StockManager;

class StockController
{

    private StockManager $stockManager;

    public function __construct(StockManager $stockManager)
    {
        $this->stockManager = $stockManager;
    }

    /** @return array | StockItem[] */
    public function getStockItems(): array
    {
        return $this->stockManager->getStockItems();
    }

    public function getStockItemHistory(int $id): array
    {

        return $this->stockManager->getStockItemHistory($id);
    }

    public function findStockItemById(int $stockId): ?StockItem
    {
        return $this->stockManager->findStockItemById($stockId);
    }

    public function deleteStockItem(int $stockId): bool
    {

        return $this->stockManager->deleteStockItem($stockId);
    }

    public function updateStockItem(int $stockId, string $title): bool
    {
        return $this->stockManager->updateStockItem($stockId, $title);
    }

    public function adjustStock(int $stockId, int $delta, string $reason): ?StockItem
    {
        return $this->stockManager->adjustStock($stockId, $delta, $reason);
    }

    public function addStockItem(string $title, int $quantity): StockItem
    {
        return $this->stockManager->addStockItem($title,$quantity);
    }
}