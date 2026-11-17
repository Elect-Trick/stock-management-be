<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\StockItem;
use PDO;
use PDOException;

class StockRepository
{

    private PDO $PDO;

    function __construct(PDO $PDO)
    {

        $this->PDO = $PDO;
    }

    public function addStockItem(string $title, int $quantity): StockItem
    {
        $this->PDO->beginTransaction();
        try {
            $statement = $this->PDO->prepare(
                'INSERT INTO stock_item (title, quantity) VALUES (:title, :quantity)'
            );

            $statement->execute([
                ':title'    => $title,
                ':quantity' => $quantity,
            ]);

            $id = (int) $this->PDO->lastInsertId();

            $select = $this->PDO->prepare(
                'SELECT id, title, quantity, is_deleted, created_at, updated_at
               FROM stock_item
              WHERE id = :id'
            );
            $select->execute([':id' => $id]);
            $row = $select->fetch(PDO::FETCH_OBJ);

            $this->PDO->commit();
        } catch (PDOException $e) {
            $this->PDO->rollBack();
            throw $e;
        }
        return StockItem::fromEntity($row);
    }

    public function getStockItems(): array
    {
        $statement = $this->PDO->prepare('SELECT * FROM stock_item WHERE is_deleted = :is_deleted ORDER BY created_at DESC');
        $statement->execute([
            'is_deleted' => false
        ]);

        return $statement->fetchAll(PDO::FETCH_OBJ);

    }

    public function getStockItemHistory(int $id): array
    {
        $statement = $this->PDO->prepare('
        SELECT * FROM stock_item_history 
        WHERE stock_item_id = :id 
        ORDER BY created_at DESC');

        $statement->execute([
            ':id' => $id
        ]);
        return $statement->fetchAll();
    }

    public function deleteStockItem(int $id): bool
    {
        $statement = $this->PDO->prepare('UPDATE stock_item set is_deleted = :is_deleted where id = :id');
        return $statement->execute([
            ':id' => $id,
            ':is_deleted' => true
        ]);
    }

    public function findStockItemById(int $id): ?StockItem
    {
        $statement = $this->PDO->prepare('
                SELECT * from stock_item 
                where id = :id 
                and is_deleted = :is_deleted');
        $statement->execute([
            ':id' => $id,
            ':is_deleted' => false,
        ]);

        $row = $statement->fetch(PDO::FETCH_OBJ);

        if(!$row)
            return null;

        return StockItem::fromEntity($row);
    }

    public function updateStockItem(int $stockId, string $title): bool
    {
        $statement = $this->PDO->prepare('UPDATE stock_item set title = :title where id = :id');
        return $statement->execute([
            ':id' => $stockId,
            ':title' => $title,
        ]);
    }

    public function adjustStock(int $stockId, int $delta, string $reason): ?StockItem
    {


        try {
            $this->PDO->beginTransaction();

            $stockItemUpdate = $this->PDO->prepare('UPDATE stock_item set quantity = (quantity + (:delta)) where id = :id');
            $stockItemUpdate->execute([
                ':delta' => $delta,
                ':id' => $stockId
            ]);

            $historyrecord = $this->PDO->prepare('INSERT INTO stock_item_history (delta,reason,stock_item_id) VALUES (:delta, :reason, :stockId)');
            $historyrecord->execute([
                ':delta' => $delta,
                ':reason' => $reason,
                ':stockId' => $stockId,

            ]);

            $select = $this->PDO->prepare(
                'SELECT id, title, quantity, is_deleted, created_at, updated_at
               FROM stock_item
              WHERE id = :stockId'
            );
            $select->execute([':stockId' => $stockId]);
            $row = $select->fetch(PDO::FETCH_OBJ);


            $this->PDO->commit();
            return StockItem::fromEntity($row);


        }catch (\PDOException $ex) {

            $this->PDO->rollBack();
            throw new \PDOException('Could not update stock item, something went wrong');
        }

    }


}