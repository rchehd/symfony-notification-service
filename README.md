# Symfony Notification Service

This project is a standalone notification service built with Symfony. It's designed to accept requests and dispatch notifications to customers through various channels with support for provider failover, rate limiting, and usage tracking.

The service is fully containerized with Docker for easy setup and development.

## Features

**Multi-Channel Notifications**: Send notifications via different channels like Email, SMS, and Log. The architecture is easily extendable to support new channels (Push, Messengers, etc.).

**Provider Abstraction**: A core NotifierInterface provides a clean abstraction, allowing for multiple providers for the same channel.

**Failover Support**: If a primary notification provider fails, the service automatically attempts to send the message via a secondary provider. Provider priority is configurable.

**Asynchronous Processing**: All notifications are processed asynchronously using Symfony Messenger with a Doctrine transport, ensuring the API responds quickly without waiting for external services.

**Delayed Retries**: If all providers for a channel fail, Symfony Messenger will automatically retry sending the notification with an exponential backoff strategy.

**Configuration-Driven**: Channels can be easily enabled or disabled via a configuration file without changing any code.

**(Bonus) Rate Limiting**: Implements throttling to limit the number of notifications sent to a single recipient within a configured time window (e.g., 300 per hour).

**(Bonus) Usage Tracking**: Successfully sent notifications are logged to the database for tracking and auditing purposes.

**API with Documentation**: Endpoints are exposed via API Platform, providing automated interactive documentation (Swagger UI).

**Comprehensive Test Suite**: Includes Unit, Integration, and API tests to ensure code quality and prevent regressions.

## Technologies Used

- **Backend**: Symfony 7, PHP 8.3
- **API**: API Platform 3
- **Containerization**: Docker, Docker Compose
- **Web Server**: FrankenPHP
- **Database**: PostgreSQL
- **Asynchronous Queue**: Symfony Messenger with Doctrine Transport
- **Testing**: PHPUnit
- **Email Testing**: Mailpit
- **Static Analysis**: PHPStan

## 🚀 Getting Started

### Prerequisites

- Docker and Docker Compose
- make (optional, for convenience)
- A local copy of this repository

### Installation & Setup

#### 1. Clone the Repository

```bash
git clone git@github.com:rchehd/symfony-notification-service.git
cd symfony-notification-service
```

#### 2. Environment Configuration

Create a local environment file by copying the example:

```bash
cp .env .env.example
```

The default values in `.env` need to be set by yourself.

#### 3. Build and Run the Application

The provided Makefile automates the entire setup process. Simply run:

```bash
make up
```

This command will:
- Build and start all Docker containers (PHP, PostgreSQL, Worker, Mailpit)
- Install all composer dependencies
- Apply database migrations
- Clear the application cache

### Access Points

- **Application**: http://localhost
- **API Documentation (Swagger UI)**: http://localhost/api/docs
- **Mailpit Web UI**: http://localhost:8025

## 🛠️ Usage / API Endpoints

### 1. Send Notifications

This endpoint queues one or more notifications to be sent to a specific recipient.

- **Endpoint**: `POST /api/notifications`
- **Success Response**: `202 Accepted` - Indicates that the request has been accepted for processing
- **Error Response**: `422 Unprocessable Entity` - Indicates a validation error in the request body

#### Example JSON Request Body

This example demonstrates sending both an email and an SMS to a single recipient in one API call. The service will use the appropriate identifier from the recipient object for each channel.

```json
{
    "recipient": {
        "email": "test-user@example.com",
        "phoneNumber": "+15551234567",
        "username": "test_user_01"
    },
    "notifications": [
        {
            "channel": "email",
            "payload": {
                "subject": "Your Weekly Report",
                "textBody": "Hello! This is the plain text version of your weekly report.",
                "htmlBody": "<p>Hello! This is the <b>HTML</b> version of your weekly report.</p>"
            }
        },
        {
            "channel": "sms",
            "payload": {
                "message": "Your weekly report is now available. Please check your email."
            }
        },
        {
            "channel": "log",
            "payload": {
                "logMessage": "This is a test message for the log channel."
            }
        }
    ]
}
```

### 2. View Notification Logs

This endpoint retrieves a paginated list of all successfully sent notifications.

- **Endpoint**: `GET /api/notification_logs`
- **Success Response**: `200 OK`

## ⚙️ Configuration

### How to Enable or Disable Notification Channels

### How to Manage Notification Providers
You can control which notification providers are active and set their failover priority in a single configuration file.

1. Open the configuration file: config/packages/app_notification.yaml.

2. Modify the app.notification.providers parameter.

    1. To disable a provider: change its enabled status to false.

    2. To change failover order: modify the priority value. The higher the number, the higher the priority.

### Example Configuration

```yaml
# config/packages/app_notification.yaml
parameters:
    app.notification.providers:
        email:
            symfony_mailer: { enabled: true, priority: 100, class: App\Notification\Domain\Service\Provider\Implementation\SymfonyMailerNotifier }
            # Fallback email provider (currently disabled)
            # aws_ses: { enabled: false, priority: 50, class: App\Notification\Domain\Service\Provider\Implementation\AwsSesNotifier }
        sms:
            # Primary SMS provider (currently enabled)
            twilio: { enabled: true, priority: 100, class: App\Notification\Domain\Service\Provider\Implementation\SMSNotifier }
            # Fallback SMS provider (currently disabled)
            # vonage: { enabled: false, priority: 50, class: App\Notification\Domain\Service\Provider\Implementation\VonageSmsNotifier }
        log:
            main_log: { enabled: true, priority: 100, class: App\Notification\Domain\Service\Provider\Implementation\LogNotifier }
```

#### Example JSON Response Body

```json
{
  "@context": "/api/contexts/NotificationLog",
  "@id": "/api/notification_logs",
  "@type": "hydra:Collection",
  "hydra:member": [
    {
      "@id": "/api/notification_logs/1",
      "@type": "NotificationLog",
      "id": 1,
      "recipientIdentifier": "test-user@example.com",
      "channel": "email",
      "providerClass": "App\\Notification\\Domain\\Service\\Provider\\Implementation\\EmailNotifier",
      "sentAt": "2025-09-20T22:00:00+00:00"
    }
  ],
  "hydra:totalItems": 1
}
```

You can also fetch a single log entries: `GET /api/notification_logs`.

## 🧪 Running Tests

The project includes a comprehensive test suite. To run all tests, use the following make command:

```bash
make 'run tests'
```

This will execute the entire test suite (Unit, Integration, and API tests) in the dedicated test environment.

## Makefile Commands

The Makefile provides several useful commands for development:

- `make up`: Build and start all containers
- `make down`: Stop and remove all containers
- `make stop`: Stop running containers without removing them
- `make rebuild`: Stop, remove, and rebuild all containers from scratch
- `make cc`: Clear the application cache
- `make logs`: Follow the logs of all running containers
- `make ssh`: Open a bash shell inside the frankenphp container
- `make 'run tests'`: Run the PHPUnit test suite
- `make 'worker logs'`: Check watcher logs

## How to Add a New Notification Channel (e.g., Push)

The service is designed to be easily extendable. To add a new notification channel, you need to create several components that define its data structure and logic.

### 1. Update the NotificationChannel Enum
   
Add a new case for your channel in src/Notification/Domain/Enum/NotificationChannel.php.
```php
enum NotificationChannel: string
{
  // ...
  case PUSH = 'push';
}
```


### 2. Create a Payload DTO
   
This class defines the data structure your API will expect for this channel. Create it in src/Notification/Application/DTO/Payload/Implementation/.
```php
// src/Notification/Application/DTO/Payload/Implementation/PushPayload.php
namespace App\Notification\Application\DTO\Payload\Implementation;

use App\Notification\Application\DTO\Payload\NotificationPayloadInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PushPayload implements NotificationPayloadInterface
{
#[Assert\NotBlank]
public string $title;

    #[Assert\NotBlank]
    public string $body;
    
    public ?string $deviceToken = null; // Example field for push notifications
}
```

### 3. Create Value Objects (Recipient & Content)
   
Create specific, validated Value Objects for the recipient's identifier and the message content in src/Notification/Domain/ValueObject/Implementation/.

Recipient:
```php
// src/Notification/Domain/ValueObject/Implementation/PushRecipient.php
class PushRecipient implements RecipientInterface { /* ... */ }
```

Content:
```php
// src/Notification/Domain/ValueObject/Implementation/PushContent.php
class PushContent implements MessageContentInterface { /* ... */ }
```
### 4. Create a Notification Command Class
   
Create a new notification class in src/Notification/Application/Notification/Implementation/ that ties the new Recipient and Content objects together.
```php
// src/Notification/Application/Notification/Implementation/PushNotification.php
namespace App\Notification\Application\Notification\Implementation;

class PushNotification implements NotificationInterface { /* ... */ }
```

### 5. Update the Factories
   
Teach your factories how to create the new objects.

RecipientFactory: Update the create method in RecipientFactory.php to handle the new channel and create the appropriate Recipient object (e.g., PushRecipient). You may also need to add a new field to RecipientDTO.php (e.g., pushToken).

NotificationFactory: Update the createFromRequest method in NotificationFactory.php to handle the new Payload and create the Notification command object (e.g., PushNotification).

### 6. Create the Provider Class
   
Create the new provider class in src/Notification/Domain/Service/Provider/Implementation/. This class must implement NotifierInterface.

```php

// src/Notification/Domain/Service/Provider/Implementation/MyNewPushNotifier.php
namespace App\Notification\Domain\Service\Provider\Implementation;
// ...

class MyNewPushNotifier implements NotifierInterface
{
    public function send(NotificationInterface $notification): void
    {
    // Add logic to send notification via the new service API
    }

    public function supports(NotificationChannel $channel): bool
    {
        return $channel === NotificationChannel::PUSH;
    }
}
```

### 7. Register the Service
  
Finally, open config/services.yaml and config/packages/app_notification.yaml to register your new provider and add it to the main configuration.
