<?php

declare(strict_types=1);

namespace App\Validator;


use http\Exception\InvalidArgumentException;

class StockInputValidator
{
    private const MAX_TEXT_LENGTH = 255;
    private const MAX_DELTA = 1_000_000;

    /**
     * Validates and normalizes the title for creating or updating a stock item.
     * @throws InvalidArgumentException
     */
    public function validateTitle(array $data): string
    {
        if (!isset($data['title']) || !is_string($data['title'])) {
            throw new InvalidArgumentException('Title is required', 422);
        }

        if(empty($data['title'])) {
            throw new InvalidArgumentException('Title is required', 422);
        }
        if (!is_string($data['title'])) {
            throw new InvalidArgumentException('Title must be a string', 422);
        }

        $title = trim($data['title']);

        if ($title === '') {
            throw new InvalidArgumentException('Title is required', 422);
        }

        if (mb_strlen($title) > self::MAX_TEXT_LENGTH) {
            throw new InvalidArgumentException('Title must be 255 characters or fewer', 422);
        }

        return $title;
    }

    /**
     * Validates and normalizes the payload for adjusting stock quantity.
     *
     * @return array{delta: int, reason: string}
     * @throws InvalidArgumentException
     */
    public function validateAdjustment(array $data): array
    {
        if (!isset($data['delta']) || $data['delta'] === '') {
            throw new InvalidArgumentException('Quantity change is required', 422);
        }

        $rawDelta = $data['delta'];

        if (is_bool($rawDelta) || filter_var($rawDelta, FILTER_VALIDATE_INT) === false) {
            throw new InvalidArgumentException('Quantity change must be a whole number', 422);
        }

        $delta = (int) $rawDelta;

        if ($delta === 0) {
            throw new InvalidArgumentException('Quantity change cannot be zero', 422);
        }

        if ($delta < -self::MAX_DELTA || $delta > self::MAX_DELTA) {
            throw new InvalidArgumentException('Quantity change is out of allowed range', 422);
        }

        if (!isset($data['reason']) || !is_string($data['reason'])) {
            throw new InvalidArgumentException('Reason is required', 422);
        }

        $reason = trim($data['reason']);

        if ($reason === '') {
            throw new InvalidArgumentException('Reason is required', 422);
        }

        if (mb_strlen($reason) > self::MAX_TEXT_LENGTH) {
            throw new InvalidArgumentException('Reason must be 255 characters or fewer', 422);
        }

        return [
            'delta'  => $delta,
            'reason' => $reason,
        ];
    }

    /**
     * Validates that a route/payload ID is a positive integer.
     * @throws InvalidArgumentException
     */
    public function validateId(mixed $id): int
    {
        if (is_bool($id) || filter_var($id, FILTER_VALIDATE_INT) === false || (int) $id <= 0) {
            throw new InvalidArgumentException('Invalid stock item ID', 404);
        }

        return (int) $id;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function validateQuantity(array $data): int
    {
        if (!isset($data['quantity']) || $data['quantity'] === '') {
            throw new InvalidArgumentException('Quantity change is required', 422);
        }

        $rawQuantity = $data['quantity'];

        if (is_bool($rawQuantity) || filter_var($rawQuantity, FILTER_VALIDATE_INT) === false) {
            throw new InvalidArgumentException('Quantity change must be a whole number', 422);
        }

        $quantity = (int) $rawQuantity;

        if ($quantity === 0) {
            throw new InvalidArgumentException('Quantity change cannot be zero', 422);
        }

        if ($quantity < -self::MAX_DELTA || $quantity > self::MAX_DELTA) {
            throw new InvalidArgumentException('Quantity change is out of allowed range', 422);
        }

        return $quantity;
    }
}