<?php
namespace App;

require '../vendor/autoload.php';

use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\ConsoleOutput;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\DB\Database;
use Bigcommerce\Api\Client as Bigcommerce;

class BigCommerceApp
{
    private $logger;
    private $db;
	
	private $clientId = 'xxxx';
	private $secretKey = 'xxxx';
	

    public function __construct(
		LoggerInterface $logger, 
		$useMongoDB = true
	) {
        $this->logger = $logger;
        if ($useMongoDB) {
            $this->db = getMerchantsCollection();
        } else {
            $this->db = new Database();
        }
    }

    public function handleInstallation($payload)
    {
		$object = new \stdClass();
		$object->client_id = $this->clientId;
		$object->client_secret = $this->secretKey;
		 
		$object->code = $payload['code'];
		$object->context = $payload['context'];
		$object->scope = $payload['scope'];
		
		list($context, $storeHash) = explode('/', $payload['context'], 2);
		 
		

		$authTokenResponse = Bigcommerce::getAuthToken($object);

		Bigcommerce::configure([
			'client_id' => $this->clientId,
			'auth_token' => $authTokenResponse->access_token,
			'store_hash' => $storeHash
		]);
	
	
        if ($this->db instanceof MongoDB\Collection) {
            $this->db->insertOne([
                'store_hash' => $storeHash,
                'access_token' => $authTokenResponse->access_token,
                'store_name' => $storeHash,
                'id' => rand(2,10).$storeHash,
                'installation_time' => new MongoDB\BSON\UTCDateTime(),
                'deleted' => false
            ]);
        } else {
            $this->db->insertMerchant(
				$storeHash, 
				$accessToken, 
				$storeHash, 
				rand(2,10).$storeHash
			);
        }

        $this->logger->info("Installed: Store {$storeHash} with ID {$id}");
    }

    public function handleUninstallation($storeHash)
    {
        if ($this->db instanceof MongoDB\Collection) {
            $this->db->updateOne(
                ['store_hash' => $storeHash],
                ['$set' => ['deleted' => true, 'uninstallation_time' => new MongoDB\BSON\UTCDateTime()]]
            );
        } else {
            $this->db->markUninstalled($storeHash);
        }

        $this->logger->info("Uninstalled: Store with hash {$storeHash}");
    }

    public function verifyStoreHash($signedPayloadJWT)
    {
        try {
            $secret = 'xxxxx';
            $decoded = JWT::decode(
				$signedPayloadJWT, 
				new Key($secret, 'HS256')
			);

            if (isset($decoded->store_hash)) {
                $this->logger->info("Verified: Store hash {$decoded->store_hash}");
                return true;
            }

            return false;
        } catch (\Exception $e) {
            $this->logger->error("JWT Verification failed: " . $e->getMessage());
            return false;
        }
    }

    public function loadApp($storeHash)
    {
        if ($this->db instanceof MongoDB\Collection) {
            $merchant = $this->db->findOne([
				'store_hash' => $storeHash,
				'deleted' => false
			]);
        } else {
            $merchant = $this->db->findMerchantByStoreHash($storeHash);
        }

        if ($merchant) {
            $this->logger->info("Loading app for Store hash {$storeHash}");
            return $merchant;
        }

        $this->logger->warning(
			"Store not found or marked as deleted for hash {$storeHash}"
		);
        return null;
    }
}
