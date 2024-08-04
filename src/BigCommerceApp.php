<?php

require 'vendor/autoload.php';

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;

class BigCommerceApp
{
    private $logger;
    private $collection;
    private $jwtVerifier;

    public function __construct(LoggerInterface $logger, JWTVerifier $jwtVerifier)
    {
        $this->logger = $logger;
        $this->collection = getMerchantsCollection();
        $this->jwtVerifier = $jwtVerifier;
    }

    public function handleInstallation($payload)
    {
        $storeHash = $payload['context'];
        $accessToken = $payload['access_token'];
        $storeName = $payload['store_name'];
        $id = $payload['user']['id'];
        
        $this->collection->insertOne([
            'store_hash' => $storeHash,
            'access_token' => $accessToken,
            'store_name' => $storeName,
            'id' => $id,
            'installation_time' => new MongoDB\BSON\UTCDateTime(),
            'deleted' => false
        ]);

        $this->logger->info("Installed: Store {$storeName} with ID {$id}");
    }

    public function handleUninstallation($storeHash)
    {
        $this->collection->updateOne(
            ['store_hash' => $storeHash],
            ['$set' => ['deleted' => true, 'uninstallation_time' => new MongoDB\BSON\UTCDateTime()]]
        );

        $this->logger->info("Uninstalled: Store with hash {$storeHash}");
    }

    public function handleLoad($storeHash, $signedPayloadJWT)
    {
        if (!$this->jwtVerifier->verify($signedPayloadJWT, $storeHash)) {
            http_response_code(403);
            echo "Forbidden: Invalid signature";
            exit;
        }

        // Logic to load the app interface
        echo "App loaded for store hash: {$storeHash}";
    }

    public function handleRemoveUser($storeHash)
    {
        $this->collection->updateOne(
            ['store_hash' => $storeHash],
            ['$set' => ['deleted' => true, 'removal_time' => new MongoDB\BSON\UTCDateTime()]]
        );

        $this->logger->info("Removed user from store with hash {$storeHash}");
    }
}
