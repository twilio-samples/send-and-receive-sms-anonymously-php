<?php

declare(strict_types=1);

use DI\Container;
use Dotenv\Dotenv;
use MaskedNumber\MaskedNumber;
use Slim\Factory\AppFactory;
use Twilio\TwiML\MessagingResponse;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();
$dotenv
    ->required(['MY_PHONE_NUMBER'])
    ->notEmpty();

$container = new Container();

AppFactory::setContainer($container);
$app = AppFactory::create();
$app->post('/', new MaskedNumber(new MessagingResponse()));

$app->run();
