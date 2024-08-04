# BigCommerce PHP App

## Overview

This is a basic BigCommerce app built with PHP 8.3 that handles installation, uninstallation, and various callbacks.

## Prerequisites

- PHP 8.3
- Composer
- MongoDB
- Ngrok (for local testing)

## Setup

1. **Clone the repository:**

   ```bash
   git clone https://github.com/sandipchandela/bigcommerce-php.git
   cd bigcommerce-php
   ```
   
2. **Install dependencies:**

   ```bash
   composer install
   ```
3. **Configure environment variables:**

Create a .env file in the root directory with the following content:
	```bash
	APP_HOSTNAME=<your_ngrok_hostname>
	DB_URI=<your_mongo_db_uri>
	BC_CLIENT_SECRET=<your_bigcommerce_client_secret>
	SCOPES=store_v2_content,store_v2_information,store_v2_settings
	```

4. **local server with Ngrok**
   ```bash
   ngrok http 80
   ```
   
## Routes
- Auth Callback URL: /bigcommerce/install
- Load Callback URL: /bigcommerce/load
- Uninstall Callback URL: /bigcommerce/uninstall
- Webhook Listener URL: /bigcommerce/webhook

## Testing
Run PHPUnit tests:

	```bash
	./vendor/bin/phpunit tests -vvv
	```
