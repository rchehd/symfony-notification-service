<?php

namespace App\Tests\Api;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

/**
 * Tests successful sending of notifications through the API.
 *
 * The test verifies whether the notifications are correctly routed to their respective channels
 * (email, SMS, log) and ensures that the expected number of messages is dispatched to the
 * asynchronous transport system.
 *
 * ARRANGE:
 * - A HTTP client is initialized.
 * - Mock JSON data is prepared with recipient details and three notification entries.
 *
 * ACT:
 * - A POST request is sent to the `/api/notifications` endpoint with the mock JSON data.
 *
 * ASSERT:
 * - Validates that the async transport queue contains exactly three entries corresponding to
 *   the three notifications defined in the mock data.
 */
class NotificationControllerTest extends ApiTestCase
{

    /**
     * Tests successful sending of notifications through the API.
     *
     * The test verifies whether the notifications are correctly routed to their respective channels
     * (email, SMS, log) and ensures that the expected number of messages is dispatched to the
     * asynchronous transport system.
     *
     * ARRANGE:
     * - A HTTP client is initialized.
     * - Mock JSON data is prepared with recipient details and three notification entries.
     *
     * ACT:
     * - A POST request is sent to the `/api/notifications` endpoint with the mock JSON data.
     *
     * ASSERT:
     * - Validates that the async transport queue contains exactly three entries corresponding to
     *   the three notifications defined in the mock data.
     */
    public function testSendNotificationSuccessfully(): void
    {
        // ARRANGE
        $client = static::createClient();

        $jsonData = [
            'recipient' => [
                'email'       => 'test@example.com',
                'phoneNumber' => '+15551234567',
                'username'    => 'test',
            ],
            'notifications' => [
                ['channel' => 'email', 'payload' => ['subject' => 'Test', 'textBody' => 'Body']],
                ['channel' => 'sms', 'payload' => ['message' => 'SMS Body']],
                ['channel' => 'log', 'payload' => ['logMessage' => 'Log message']],
            ],
        ];

        // ACT
        $client->request('POST', '/api/notifications', [
            'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
            'json'    => $jsonData,
        ]);

        // ASSERT
        /** @var InMemoryTransport $transport */
        $transport = self::getContainer()->get('messenger.transport.async');

        $this->assertCount(3, $transport->get());
    }

    /**
     * Tests if the API returns a validation error for an invalid payload.
     *
     * This test sends a POST request to the '/api/notifications' endpoint with
     * an invalid JSON payload where the field 'payload' is missing within the
     * notifications structure. Verifies whether the server responds with a
     * 422 Unprocessable Entity status code and appropriate error details.
     */
    public function testReturnsValidationErrorForInvalidPayload(): void
    {
        $client = static::createClient();

        // Invalid JSON: field 'payload' is missed
        $jsonData = [
            'recipient'     => ['email' => 'test@example.com'],
            'notifications' => [
                ['channel' => 'email'],
            ],
        ];

        $client->request('POST', '/api/notifications', [
            'headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json'],
            'json'    => $jsonData,
        ]);

        // Check the error.
        self::assertResponseStatusCodeSame(422);
        self::assertJsonContains(['detail' => 'notifications[0].payload: Field "payload" is required.']);
    }
}
