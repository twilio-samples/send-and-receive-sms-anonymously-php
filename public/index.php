<?php

declare(strict_types=1);

use DI\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Factory\AppFactory;
use Twilio\TwiML\MessagingResponse;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();
$dotenv
    ->required(['MY_PHONE_NUMBER'])
    ->notEmpty();

/**
 * This regex checks if a string starts with a phone number in E.164 format followed by a
 * colon (':') and a space, followed by any amount of text, e.g.,:
 * "+16501231234: Here is my message to you.".
 */
const REGEX_MESSAGE_WITH_RECIPIENT = "/^(?<recipient>\+[1-9]\d{1,14}(?=:)): (?<message>.*)/";

$container = new Container();

AppFactory::setContainer($container);
$app = AppFactory::create();


$app->post('/', function (ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
    $twiml = new MessagingResponse();

    $sender = $request->getParsedBody()['From'] ?? "";
    $message = $request->getParsedBody()['Body'] ?? "";

    if ($sender === $_ENV['MY_PHONE_NUMBER']) {
        $match = preg_match(REGEX_MESSAGE_WITH_RECIPIENT, $message, $matches);
        if ($match) {
            $twiml->message($matches['message'], [
                'to' => $matches['recipient'],
            ]);
        } else {
            $twiml->message("To reply to someone, you need to specify the recipient's phone number in E.164 format followed by a colon (':') and a space before the message, e.g.,: \"+16501231234: Here is my message to you.\".", ['to' => $_ENV['MY_PHONE_NUMBER']]);
        }
        $response = $response->withHeader('Content-Type', 'application/xml');
        $response->getBody()->write($twiml->asXML());

        return $response;
    }

    $twiml->message("$sender: $message", [
        'to' => $_ENV['MY_PHONE_NUMBER'],
    ]);

    $response = $response->withHeader('Content-Type', 'application/xml');
    $response->getBody()->write($twiml->asXML());

    return $response;
});

$app->run();