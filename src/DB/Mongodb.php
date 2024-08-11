<?php
namespace Config;

require '../vendor/autoload.php';

use MongoDB\Client;

class Mongodb {
	
	function getMongoDBClient(): Client
	{
		$uri = getenv('DB_URI');
		return new Client($uri);
	}

	function getMerchantsCollection()
	{
		$client = getMongoDBClient();
		return $client->selectCollection(
			'your_database_name', 
			'merchants'
		);
	}
}

