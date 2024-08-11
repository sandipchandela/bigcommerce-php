<?php

require '../vendor/autoload.php';

use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Dotenv\Dotenv;
use App\BigCommerceApp;
use App\WebhookListener;

$output = new ConsoleOutput();
$logger = new ConsoleLogger($output);

// Load environment variables
$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');

$app = new BigCommerceApp($logger, false);
$webhookListener = new WebhookListener($logger);

$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'POST') {
    $payload = json_decode(file_get_contents('php://input'), true);

    if (strpos($requestUri, '/bigcommerce/install') !== false) {
        $app->handleInstallation($payload);
    } elseif (strpos($requestUri, '/bigcommerce/uninstall') !== false) {
        $storeHash = $payload['store_hash'];
        $app->handleUninstallation($storeHash);
    } elseif (strpos($requestUri, '/bigcommerce/remove-user') !== false) {
        $storeHash = $payload['store_hash'];
        $app->handleRemoveUser($storeHash);
    } elseif (strpos($requestUri, '/bigcommerce/webhook') !== false) {
        $webhookListener->handleWebhook($payload);
    }
} elseif ($requestMethod === 'GET' && strpos($requestUri, '/bigcommerce/load') !== false) {
    $storeHash = $_GET['store_hash'];
    $signedPayloadJWT = $_GET['signed_payload_jwt'];

    $app->handleLoad($storeHash, $signedPayloadJWT);
}else{
	include 'token-not-valid.php';
}
