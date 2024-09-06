<?php

use Apple\Client\Apple;
use Apple\Client\Integrations\AppleId\Request\Bootstrap;
use Apple\Client\Integrations\Idmsa\Request\Appleauth\Auth;
use Apple\Client\Integrations\Idmsa\Request\Appleauth\AuthorizeSing;
use Apple\Client\Integrations\Idmsa\Response\AuthResponse;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;

require_once __DIR__ . '/../../vendor/autoload.php';


$filesystemAdapter = new FilesystemAdapter();
$cache = new Psr16Cache($filesystemAdapter);

$Apple = new Apple($cache,'client_id');

$Apple->getAppleIdConnector()->getRepositories()->add('account','licade_2015@163.com');


$response = $Apple->authorizeSing('licade_2015@163.com','AtA3FH2sBfrtSv6s');
var_dump('authorizeSing',$response->json());

$response = $Apple->auth();
var_dump('phone list ',$response->getTrustedPhoneNumbers()->toArray());

$response = $Apple->sendSecurityCode();
var_dump('sendSecurityCode',$response->json());

$response = $Apple->sendPhoneSecurityCode(1);
var_dump('sendPhoneSecurityCode',$response->json());

die();
$response = $AppleIdConnector->send(new Bootstrap());
var_dump('Bootstrap',$response->json());

$response = $IdmsaConnector->send(new AuthorizeSing('licade_2015@163.com','AtA3FH2sBfrtSv6s'));
var_dump('AuthorizeSing',$response->json());


/**
 * @var AuthResponse $response
 */
$response = $IdmsaConnector->send(new Auth());
var_dump('Auth',$response->authorizeSing());

var_dump($IdmsaConnector->getHeaderStore()->all());
