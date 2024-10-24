<?php

declare(strict_types=1);

namespace MaskedNumberTest;

use MaskedNumber\MaskedNumber;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Response;
use Twilio\TwiML\MessagingResponse;

final class MaskedNumberTest extends TestCase
{
    private ServerRequestInterface&MockObject $request;

    public function setUp(): void
    {
        $this->request = $this->createMock(ServerRequestInterface::class);
    }

    #[TestWith(["+16501231234", "Here is the message body.", ""])]
    public function testCanHandleRequestToRelayMessageToTheUser(
        string $fromNumber,
        string $messageBody,
        string $myPhoneNumber
    ): void {
        $_ENV['MY_PHONE_NUMBER'] = $myPhoneNumber;

        $responseBody = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<Response><Message to="">+16501231234: Here is the message body.</Message></Response>

EOF;

        $this->setupRequest($messageBody, $fromNumber);

        $handler = new MaskedNumber(new MessagingResponse());
        $result  = $handler($this->request, new Response());

        $this->assertSame("application/xml", $result->getHeaderLine('Content-Type'));
        $this->assertSame($responseBody, (string) $result->getBody());
    }

    #[TestWith(["+16501231234", "+16501231234: Here is my message to you.", "+16501231234"])]
    public function testCanHandleRequestToRelayMessageFromTheUser(
        string $fromNumber,
        string $messageBody,
        string $myPhoneNumber
    ): void {
        $_ENV['MY_PHONE_NUMBER'] = $myPhoneNumber;

        $responseBody = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<Response><Message to="+16501231234">Here is my message to you.</Message></Response>

EOF;

        $this->setupRequest($messageBody, $fromNumber);

        $handler = new MaskedNumber(new MessagingResponse());
        $result  = $handler($this->request, new Response());

        $this->assertSame("application/xml", $result->getHeaderLine('Content-Type'));
        $this->assertSame($responseBody, (string) $result->getBody());
    }

    #[TestWith(["+16501231234", "Here is my message to you.", "+16501231234"])]
    public function testCanHandleMalformedRequestsToRelayMessageFromTheUser(
        string $fromNumber,
        string $messageBody,
        string $myPhoneNumber
    ): void {
        $_ENV['MY_PHONE_NUMBER'] = $myPhoneNumber;

        /** phpcs:disable Generic.Files.LineLength */
        $responseBody = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<Response><Message to="+16501231234">To reply to someone, you need to specify the recipient's phone number in E.164 format followed by a colon (':') and a space before the message, e.g.,: "+16501231234: Here is my message to you.".</Message></Response>

EOF;
        /** phpcs:enable */

        $this->setupRequest($messageBody, $fromNumber);

        $handler = new MaskedNumber(new MessagingResponse());
        $result  = $handler($this->request, new Response());

        $this->assertSame("application/xml", $result->getHeaderLine('Content-Type'));
        $this->assertSame($responseBody, (string) $result->getBody());
    }

    private function setupRequest(string $messageBody, string $fromNumber): void
    {
        $this->request
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn([
                "Body" => $messageBody,
                "From" => $fromNumber,
            ]);
    }
}
