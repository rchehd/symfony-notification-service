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
cp .env .env.dev
```

The default values in `.env` are pre-configured to work with the Docker setup. No changes are needed to get started.

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

- **Application**: https://localhost
- **API Documentation (Swagger UI)**: https://localhost/api/docs
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

You can also fetch a single log entry by its ID: `GET /api/notification_logs/{id}`.

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
