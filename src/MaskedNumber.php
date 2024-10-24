<?php

declare(strict_types=1);

namespace MaskedNumber;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twilio\TwiML\MessagingResponse;

use function assert;
use function is_string;
use function preg_match;

final class MaskedNumber
{
    public function __construct(private readonly MessagingResponse $twiml)
    {
    }

    /**
     * This regex checks if a string starts with a phone number in E.164 format followed by a
     * colon (':') and a space, followed by any amount of text, e.g.,:
     * "+16501231234: Here is my message to you.".
     */
    public const string REGEX_MESSAGE_WITH_RECIPIENT = "/^(?<recipient>\+[1-9]\d{1,14}(?=:)): (?<message>.*)/";

    /**
     * This string provides the body of the message to reply to the user telling them that their response
     * was not formatted correctly.
     *
     * phpcs:disable Generic.Files.LineLength
     */
    public const string MSG_MALFORMED_REQUEST_BODY = "To reply to someone, you need to specify the recipient's phone number in E.164 format followed by a colon (':') and a space before the message, e.g.,: \"+16501231234: Here is my message to you.\".";
    // phpcs:enable

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $requestBody = $request->getParsedBody();

        $sender = $requestBody['From'] ?? "";
        assert(is_string($sender));

        $message = $requestBody['Body'] ?? "";
        assert(is_string($message));

        if ($sender === $_ENV['MY_PHONE_NUMBER']) {
            $match = preg_match(self::REGEX_MESSAGE_WITH_RECIPIENT, $message, $matches);
            $match ? $this->twiml->message($matches['message'], ['to' => $matches['recipient']])
                : $this->twiml->message(self::MSG_MALFORMED_REQUEST_BODY, ['to' => $_ENV['MY_PHONE_NUMBER']]);

            $newResponse = $response->withHeader('Content-Type', 'application/xml');
            $newResponse->getBody()->write($this->twiml->asXML());

            return $newResponse;
        }

        $this->twiml->message("$sender: $message", [
            'to' => $_ENV['MY_PHONE_NUMBER'],
        ]);

        $newResponse = $response->withHeader('Content-Type', 'application/xml');
        $newResponse->getBody()->write($this->twiml->asXML());

        return $newResponse;
    }
}
