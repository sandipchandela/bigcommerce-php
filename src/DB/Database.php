<?php

namespace App\DB;

require '../vendor/autoload.php';

use \PDO;

class Database
{
    private $pdo;

    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        
		$host = getenv('DB_HOST') ?? 'localhost';
        $db   = getenv('DB_NAME') ?? 'bigcommerce-app';
        $user = getenv('DB_USER') ?? 'root';
        $pass = getenv('DB_PASS') ?? "";
        $charset = 'utf8mb4';
	
        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function insertMerchant($storeHash, $accessToken, $storeName, $id)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO `merchants` (
				`store_hash`, `access_token`, `store_name`,
				`user_id`, `installation_time`, `deleted`
			) 
            VALUES (
				:store_hash, :access_token, :store_name, 
				:user_id, :installation_time, :deleted
			)"
        );

        $stmt->execute([
            'store_hash' => $storeHash,
            'access_token' => $accessToken,
            'store_name' => $storeName,
            'user_id' => $id,
            'installation_time' => date('Y-m-d H:i:s'),
            'deleted' => false
        ]);
    }

    public function markUninstalled($storeHash)
    {
        $stmt = $this->pdo->prepare(
            "UPDATE merchants 
			SET deleted = :deleted, uninstallation_time = :uninstallation_time 
			WHERE store_hash = :store_hash"
        );

        $stmt->execute([
            'deleted' => true,
            'uninstallation_time' => date('Y-m-d H:i:s'),
            'store_hash' => $storeHash
        ]);
    }

    public function findMerchantByStoreHash($storeHash)
    {
        $stmt = $this->pdo->prepare("
			SELECT * FROM merchants 
			WHERE store_hash = :store_hash AND deleted = :deleted");
        $stmt->execute([
			'store_hash' => $storeHash, 
			'deleted' => false
		]);
        return $stmt->fetch();
    }
}
