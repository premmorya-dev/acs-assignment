# ACS Laravel REST API

## Project Overview

This Laravel API application supports two roles: **Admin** and **User**. Admin users can upload customer records using a CSV file. Normal users can view customers, update payment status, and send email or WhatsApp reminders for pending payments. Both roles can access reporting endpoints.

## Tech Stack Used

- PHP 8.2+
- Laravel 12
- MySQL (recommended for production)
- Laravel Sanctum for API token authentication
- Eloquent ORM for database operations
- Form Requests for validation
- API Resource classes for JSON responses
- Database queue driver and Jobs for async notifications
- Laravel Mail and HTTP client for emails and WhatsApp requests
- Migrations, factories, and seeders for reproducible data setup

## Folder Structure

- `app/Http/Controllers` — API controllers for authentication, customers, notifications, and reports
- `app/Http/Requests` — request validation classes for every API endpoint
- `app/Http/Resources` — JSON resource classes for consistent response formatting
- `app/Jobs` — queued jobs for notifications
- `app/Mail` — Mailable class for payment reminder emails
- `app/Models` — Eloquent models and relationships
- `app/Services` — reusable services for CSV import, reporting, and WhatsApp integration
- `database/migrations` — schema definitions for users, customers, communication logs, personal access tokens, and queues
- `database/factories` — dummy data factories
- `database/seeders` — seeded admin/user accounts and customers
- `routes/api.php` — API routes
- `routes/console.php` — artisan helper command definitions

## Setup Instructions

1. Clone the repository.
2. Install dependencies:
   ```bash
   composer install
   ```
3. Copy the environment file:
   ```bash
   cp .env.example .env
   ```
4. Configure database settings in `.env`:
   - `DB_CONNECTION=mysql`
   - `DB_HOST=127.0.0.1`
   - `DB_PORT=3306`
   - `DB_DATABASE=laravel`
   - `DB_USERNAME=root`
   - `DB_PASSWORD=`
5. Configure mail settings in `.env`:
   - `MAIL_MAILER=log`
   - `MAIL_FROM_ADDRESS="hello@example.com"`
   - `MAIL_FROM_NAME="${APP_NAME}"`
6. Configure queue settings:
   - `QUEUE_CONNECTION=database`
7. Configure WhatsApp placeholders:
   - `WHATSAPP_API_URL=https://example.com/whatsapp/send`
   - `WHATSAPP_API_TOKEN=your-whatsapp-token`
8. Generate the application key:
   ```bash
   php artisan key:generate
   ```
9. Run migrations and seed database:
   ```bash
   php artisan migrate --seed
   ```
10. Create the storage symlink:
    ```bash
    php artisan storage:link
    ```
11. Start the queue worker in a separate terminal:
    ```bash
    php artisan queue:work
    ```
12. Start the application server:
    ```bash
    php artisan serve
    ```

## Default Seeded Credentials

- **Admin**
  - Email: `admin@example.com`
  - Password: `password`
- **User**
  - Email: `user@example.com`
  - Password: `password`

## API Documentation

### Login

- `POST /api/login`
- Headers: `Accept: application/json`
- Body:
  ```json
  {
    "email": "admin@example.com",
    "password": "password"
  }
  ```
- Success:
  ```json
  {
    "success": true,
    "token": "...",
    "user": {
      "id": 1,
      "name": "Admin User",
      "email": "admin@example.com",
      "role": "admin"
    }
  }
  ```
- Error:
  ```json
  {
    "success": false,
    "message": "Invalid credentials."
  }
  ```

### Logout

- `POST /api/logout`
- Headers: `Accept: application/json`, `Authorization: Bearer {token}`
- Success:
  ```json
  {
    "success": true,
    "message": "Logged out successfully."
  }
  ```

### Upload Customer CSV (Admin only)

- `POST /api/admin/upload-csv`
- Headers: `Accept: application/json`, `Authorization: Bearer {admin_token}`
- Body: multipart/form-data with field `file`
- Success:
  ```json
  {
    "success": true,
    "total_records": 100,
    "inserted_records": 95,
    "duplicate_records": 5
  }
  ```

### List Customers

- `GET /api/customers`
- Headers: `Accept: application/json`, `Authorization: Bearer {token}`
- Query params: `name`, `email`, `phone_number`, `per_page`
- Success:
  ```json
  {
    "data": [
      {
        "id": 1,
        "name": "Customer One",
        "phone_number": "1234567890",
        "email": "customer1@example.com",
        "payment_amount": "100.00",
        "payment_status": "Pending"
      }
    ],
    "meta": {
      "current_page": 1,
      "last_page": 1,
      "per_page": 15,
      "total": 1
    },
    "links": {
      "first": "...",
      "last": "...",
      "prev": null,
      "next": null
    }
  }
  ```

### Update Payment Status

- `PUT /api/customer/{customer}/payment-status`
- Headers: `Accept: application/json`, `Authorization: Bearer {token}`
- Body:
  ```json
  {
    "payment_status": "Paid"
  }
  ```
- Success:
  ```json
  {
    "data": {
      "id": 1,
      "name": "Customer One",
      "phone_number": "1234567890",
      "email": "customer1@example.com",
      "payment_amount": "100.00",
      "payment_status": "Paid"
    }
  }
  ```
- Validation error:
  ```json
  {
    "success": false,
    "errors": {
      "payment_status": ["The selected payment_status is invalid."]
    }
  }
  ```

### Send Notification

- `POST /api/customer/{customer}/send-notification`
- Headers: `Accept: application/json`, `Authorization: Bearer {token}`
- Body:
  ```json
  {
    "type": "email"
  }
  ```
- Success:
  ```json
  {
    "message": "Notification sent successfully",
    "report": {
      "total_customers": 500,
      "paid_customers": 320,
      "pending_customers": 180,
      "emails_sent": 120,
      "whatsapp_sent": 90
    }
  }
  ```

### Reports Summary

- `GET /api/reports/summary`
- Headers: `Accept: application/json`, `Authorization: Bearer {token}`
- Success:
  ```json
  {
    "report": {
      "total_customers": 500,
      "paid_customers": 320,
      "pending_customers": 180,
      "emails_sent": 120,
      "whatsapp_sent": 90
    }
  }
  ```

## CSV Format Example (customer.csv file)

Expected CSV columns:

| Name | Phone Number | Email | Payment Amount |
| --- | --- | --- | --- |
| John Doe | 1234567890 | john@example.com | 120.00 |
| Jane Smith | 0987654321 | jane@example.com | 80.50 |

## Assumptions Made

- WhatsApp notifications treated as dummy
- Notifications are only sent for customers whose `payment_status` is `Pending`.
- CSV upload skips duplicate customer emails and counts them separately.
- Admin-only routes are protected with custom middleware.
- Validation uses Form Requests and returns clean JSON for API clients.


## Postman Collection

The Postman collection is available as `postman_collection.json`

## Extra Commands

- Seed the default admin user again:
  ```bash
  php artisan db:seed
  ```

## Notes

- API routes are defined in `routes/api.php`.
- The application uses the database queue driver and Laravel Mail for email notifications.
- The project keeps controllers thin, with business logic in services, jobs, and Form Requests.
