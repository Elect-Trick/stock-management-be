<?php
declare(strict_types=1);

namespace App\Entity;

use DateTime;


class StockItem implements \JsonSerializable
{

    public int $id;

    public string $title;

    public int $quantity;

    public int $is_deleted = 0;

   public ?string $created_at = null;

    public ?string $updated_at = null;


    /**
     * @throws \Exception
     */
    public function __construct(
        int $id,
        string $title,
        int $quantity,
        int $is_deleted = 0,
        ?string $createdAt = null,
        ?string $updatedAt = null,
    )
    {

        $this->id = $id ;
        $this->title = $title;
        $this->quantity = $quantity;
        $this->is_deleted = $is_deleted;
        $this->created_at = $createdAt;
        $this->updated_at = $updatedAt;
    }

    static public function fromEntity(\stdClass $entity): self
    {
        return new self(
            $entity->id ,
            $entity->title,
            $entity->quantity,
            $entity->is_deleted ,
                $entity->created_at ?? null,
            $entity->updated_at   ?? null,
        );
    }


    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ,
            $data['title'],
            $data['quantity'],
            $data['is_deleted'] ,
        $data['created_at'] ?? null,
            $data['updated_at'] ?? null,
        );
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'quantity' => $this->quantity,
            'isDeleted' => $this->is_deleted,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}