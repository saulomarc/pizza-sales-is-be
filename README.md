# Laravel Application with Sail

This is a Laravel application configured to run using [Laravel Sail](https://laravel.com/docs/sail), a lightweight command-line interface for interacting with Laravel's default Docker development environment.

## Prerequisites

Before running this application, ensure you have the following installed:

- [Docker Desktop](https://www.docker.com/products/docker-desktop)
- [PHP](https://www.php.net/) (only needed if you're not using Sail to create the project)
- Composer (for installing dependencies if needed)
- Optional: [Laravel installer](https://laravel.com/docs/installation)

## Getting Started

Follow the steps below to set up and run the Laravel application using Sail.

### 1. Clone the Repository

```bash
git clone https://github.com/saulomarc/pizza-sales-is-be.git
cd pizza-sales-is-be
```

### 2. Copy .env File
```bash
cp .env.example .env
```

### 3. Install Dependencies and launch sail
```bash
composer install
./vendor/bin/sail up -d
```

### 4. Generate Application Key
```bash
./vendor/bin/sail artisan key:generate
```

### 5. Run Migration
```bash
./vendor/bin/sail artisan migrate
```

### 6. Generate JWT Secret
```bash
./vendor/bin/sail artisan jwt:secret
```