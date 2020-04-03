## MyTh API manager
[![Latest Stable Version](https://poser.pugx.org/4myth/api/v/stable)](https://packagist.org/packages/4myth/api)
[![License](https://poser.pugx.org/4myth/api/license)](https://packagist.org/packages/4myth/api)
[![Total Downloads](https://poser.pugx.org/4myth/api/downloads)](https://packagist.org/packages/4myth/api)
#### For Laravel 5.5 and higher
This package allows you to sync your data with Laravel 5.5 and higher. Contains two sections of manger and clint

### Package Installation

Require this package in your composer.json and update composer.

```bash
composer require 4myth/api
```

### Laravel

**Laravel 5.5** uses Package Auto-Discovery, so doesn't require you to manually add the ServiceProvider/Facade. 

You can use the Alias for shorter code.

```php
use Myth\Api;

Api::name();
```

Finally you can publish the package:
```bash
php artisan vendor:publish --tag=myth-api
```
### Getting Started

## 1- Manager Side
* ### Config file:
    
     The settings you need to setup of connection with your clients. your **`name`** and your **`clients`**.


```php[
"name"    => "manager",
"clients" => [
      # Client name
      "client-name" => [
          "secret"   => "secret",
          "base_url" => "http://127.0.0.1/api/v1",
          "models"   => [
              App\User::class => [
                  "uri"         => "user",
                  "transformer" => App\UserApiTransformer::class,
              ],
          ],
          "options"  => [
              "http" => [],
          ],
      ],
  ],
]
```
Example multiple clients: 
```php
[
    "client-1" => [],
    "client-2" => [],
];
```
Example multiple models with clients:
```php
[
    "client-1" => [
        "models" => [
            App\User::class => [],
            Some\Name\SomeModel::class => [],
        ],
    ]  
];
```
#### Client config: 
The `key` of client array must be the client name in your application.

All options **array keys**: `secret`,`base_url`,`options`,`models`
1. `secret`: Client's authentication secret, which you can obtain from your client.
2. `base_url`: Client api url.
 Example: `http://127.0.0.1/api/v1`
3. `options`: Array of your client options. available options: `http`.
    * `http`: GuzzleHttp\Client options. See: http://docs.guzzlephp.org
4. `models`: Array of your models, will be able to `syc` with specific client.  
    * `uri`: The `url` or `prefix` of model at your client software.
    * `transformer`: This option will be used automatically when your system sync `send` model data to your client, **you must** make a new transformer for each client's model.
    
### Setup
1. Model.
2. Model transformer.

#### Model
Before you begin to use the package you must setup your model by use package `trait`.
 
```php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Myth\Api\Traits\HasApiManager;

class User extends Model
{
    use HasApiManager;

    // ....
}
```

#### Model transformer
first to make a new model **transformer** we have configure in our **config**, we can execute **artisan** command: 
```php
php artisan myth:make-api-transformer TransformerName
```
then we will find the transformer inside app directory `app\TransformerName.php`
#### Transformer file:
```php   
public function body(): array
{
    return [];
}
```
The **body** method must be return an array data that you will send when syncing this model with Client.

Example: 

```php 
public function body(): array
{
    return [ "name" => "MyTh", "password" => 123 ];
}
```
The client will receive in **body** request this array.


### Usage
#### Access to client:
to access your client inside your application you can use the `facade` package `Api`
```php
use Myth\Api;

$client = Api::client("client-name");
```
#### Access to client models:

```php
use Myth\Api;

$client = Api::client("client-name");

/** @var Myth\Api\ClientModelWrapper $model */
$ClientModelWrapper = $client->model(User::class);
$ClientModelWrapper = $client->model(User::find(1));

/** @var \Illuminate\Database\Eloquent\Model $model */
 $model = $ClientModelWrapper->model();
```

#### Send data to client
you can send your data with multiple ways:
```php
use Myth\Api;


// facade
$body = [ "name" => "MyTh", "password" => 123 ];
$response = Api::sendToClient("client-name", User::calss, $body);

// ----------------------

// client wrapper
$client = Api::client("client-name");

$user = User::find(123);
$response = $client->sendData($user);
dd($response);

$body = [ "name" => "Name", "password" => "123456789"];
$response = $client->sendData(User::class,$body);
dd($response);

// ----------------------

// by model directly
$user = User::find(2);
$response = $user->sendToClient("client-name");
dd($response);
```

#### Response
The response using class `ResponseInterface` package contains methods you can access easily in your code
1. `request`: \GuzzleHttp\Psr7\Response See: http://docs.guzzlephp.org
2. `response`: array client response
3. `client_id`: the unique id at client in database.


#### Client locale storage

```php
use Myth\Api;

// $data = Api::clientData("client-name", User::class, $sync = true)->get();
// $data = Api::clientData("client-name", User::class, $sync = false)->get();
// $data = Api::clientData("client-name", User::class, $sync = null)->get();
// dd($data);

// $client = Api::client('client-name');
// $data = $client->model(User::class)->data($sync = true)->get();
// $data = $client->model(User::class)->data($sync = false)->get();
// $data = $client->model(User::class)->data($sync = null)->get();
// dd($data);

// $data = User::clientData("client-name", $sync = true)->get();
// $data = User::clientData("client-name", $sync = false)->get();
// $data = User::clientData("client-name", $sync = null)->get();
// dd($data);
```
#### Mark your models
set must sync with client
```php
use Myth\Api;

// $model = Api::syncWithClient("client-name", $client_id = 4, $model = User::find(1));
// $model = Api::client("client-name")->syncModel($client_id = 5, $model = User::find(1));
// $model = User::find(1)->syncWithClient("client-name", $client_id = 3);
```
set synced with client

```php
use Myth\Api;

// $model = Api::syncedWithClient("client-name", $client_id = 4, $model = User::find(1));
// $model = Api::client("client-name")->syncedModel($client_id = 5, $model = User::find(1));
// $model = User::find(1)->syncedWithClient("client-name", $client_id = 3);
```
unsync model with client '`delete relation`'

```php
use Myth\Api;

// $model = Api::unsyncWithClient("client-name", $client_id = 1, $model = User::find(1));
// $model = Api::client("client-name")->unsyncModel($client_id = 1, $model = User::find(1));
// $model = User::find(1)->unsyncWithClient("client-name", $client_id = 1);
```