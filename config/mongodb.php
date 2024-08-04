<?php

require 'vendor/autoload.php';

use MongoDB\Client;

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
