<?php
declare(strict_types=1);

namespace App\Manager;

use App\Entity\StockItem;
use App\Repository\StockRepository;

class StockManager
{

    private StockRepository $stockRepository;
    function __construct(StockRepository $stockRepository)
    {

        $this->stockRepository = $stockRepository;
    }

    public function getStockItems(): array
    {
        return $this->stockRepository->getStockItems();
    }

    public function getStockItemHistory(int $id): array
    {
        return $this->stockRepository->getStockItemHistory($id);
    }

    public function findStockItemById(int $stockId): ?StockItem
    {
        return $this->stockRepository->findStockItemById($stockId);
    }

    /** soft delete */
    public function deleteStockItem(int $stockId): bool
    {
      return $this->stockRepository->deleteStockItem($stockId);
    }

    public function updateStockItem(int $stockId, string $title): bool
    {
        return $this->stockRepository->updateStockItem($stockId, $title);
    }

    public function adjustStock(int $stockId, int $delta, string $reason): ?StockItem
    {

        return $this->stockRepository->adjustStock($stockId, $delta, $reason);
    }

    public function addStockItem(string $title, int $quantity): StockItem
    {
        return $this->stockRepository->addStockItem($title,$quantity);
    }

}