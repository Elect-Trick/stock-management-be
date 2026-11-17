<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/headers.php';
require_once __DIR__ . '/Database/DatabaseConfig.php';
require_once __DIR__ . '/Repository/StockRepository.php';
require_once __DIR__ . '/Controller/StockController.php';
require_once __DIR__ . '/Manager/StockManager.php';
require_once __DIR__ . '/Validator/StockInputValidator.php';
require_once __DIR__ . '/ExceptionHandler/ExceptionHandler.php';

use App\Controller\StockController;
use App\Database\DatabaseConfig;
use App\Entity\StockItem;
use App\Manager\StockManager;
use App\Repository\StockRepository;
use App\Validator\StockInputValidator;
$request = require __DIR__ . '/ServerConfig/RequestParser.php';

$db = DatabaseConfig::getPdoConnection();
$stockRepository = new StockRepository($db);
$stockManager = new StockManager($stockRepository);
$stockController = new StockController($stockManager);

switch (true) {
    case ($request['method'] === 'GET' && $request['path'] === '/stock'):
        echo json_encode($stockController->getStockItems());

        return;

    case ($request['method'] === 'GET' && sscanf($request['path'], '/stock/%d', $stockId) === 1):
        $validator = new StockInputValidator();

            $stockId = $validator->validateId($stockId);
            $stockItem = $stockController->findStockItemById($stockId);
            if (!$stockItem instanceof StockItem) {
                throw new InvalidArgumentException('Stock item not found', 404);
            }

            echo json_encode($stockItem);
            return;

    case ($request['method'] === 'GET' && sscanf($request['path'], '/stock/history/%d', $stockId) === 1):
      $validator = new StockInputValidator();
            $stockId = $validator->validateId($stockId);
            $stockItemHistory = $stockController->getStockItemHistory($stockId);
            echo json_encode($stockItemHistory);
            return;

    case ($request['method'] === 'DELETE' && sscanf($request['path'], '/stock/%d', $stockId) === 1):
        $validator = new StockInputValidator();
            $stockId = $validator->validateId($stockId);
            $stockItem = $stockController->findStockItemById($stockId);
            if (!$stockItem instanceof StockItem) {
                throw new InvalidArgumentException('Stock item not found', 404);
            }

            $stockItemDeleted = $stockController->deleteStockItem($stockId);
            echo json_encode(['deleted' => $stockItemDeleted]);
            return;



    case ($request['method'] === 'PUT' && sscanf($request['path'], '/stock/update-title/%d', $stockId) === 1):
        $validator = new StockInputValidator();
           $stockId = $validator->validateId($stockId);
            if ($stockId !== $request['data']['id']) {
                throw new InvalidArgumentException("Stock Item not found");
            }

           $title = $validator->validateTitle($request['data']);
            echo json_encode($stockController->updateStockItem($stockId, $title));
            return;

        case ($request['method'] === 'PUT' && sscanf($request['path'], '/stock/adjust-stock/%d', $stockId) === 1):
        $validator = new StockInputValidator();
//            try {

                $stockId = $validator->validateId($stockId);
                ["delta" => $delta, "reason" => $reason] = $validator->validateAdjustment($request['data']);

                $stockItem = $stockController->findStockItemById($stockId);
                if (!$stockItem instanceof StockItem) {
                    throw new InvalidArgumentException("Stock Item not found");
                }

                if (($stockItem->quantity + $delta) <= 0) {
                    throw new InvalidArgumentException("Adjustment is greater than current stock , please choose a smaller value", 422);
                }

                echo json_encode($stockController->adjustStock($stockId, $delta, $reason));
                return;

    case ($request['method'] === 'POST' && $request['path'] === '/stock/add-stock'):
        $validator = new StockInputValidator();
        $rawData = $request['data'];

            $title = $validator->validateTitle($rawData);
            $quantity =  $validator->validateQuantity($rawData);
            echo json_encode($stockController->addStockItem($title, $quantity));
            return;


    default:
            http_response_code(404);
            echo json_encode(['error' => 'Not found', 'path' => $request['path']]);
            return;
}


