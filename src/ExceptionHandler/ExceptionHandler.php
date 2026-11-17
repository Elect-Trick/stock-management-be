<?php
declare(strict_types=1);

set_exception_handler(static function (\Throwable $e): void {
    error_log((string) $e);

    // Lets default to 500 and hide the server details
    $status  = 500;
    $payload = ['error' => 'Server error'];

    if ($e instanceof Exception) {
        $status  = (int)$e->getCode();
        $payload = ['error' => $e->getMessage()];
    }

    //Optional: surface detail in dev only
//    if ((getenv('ENVIRONMENT') ?: 'dev') !== 'prod') {
//        $payload['debug'] = [
//            'type'    => get_class($e),
//            'message' => $e->getMessage(),
//            'file'    => $e->getFile() . ':' . $e->getLine(),
//        ];
//    }

    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
});