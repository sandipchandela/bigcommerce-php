<?php
namespace App;

require '../vendor/autoload.php';

use Psr\Log\LoggerInterface;

class WebhookListener
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handleWebhook($payload)
    {
        // Process the webhook payload
        $this->logger->info("Received webhook payload", $payload);
    }
}
