# Device Management System

A Laravel 12 application for managing devices with health lifecycle tracking, built with Filament admin panel and Meilisearch integration.

## Features

- **Device Management**: Complete CRUD operations for devices
- **Health Lifecycle Tracking**: Automatic calculation of device health based on deployment date and lifecycle
- **Search & Filtering**: Laravel Scout with Meilisearch for advanced search and filtering
- **Dashboard**: Overview of device health statistics
- **Admin Panel**: Modern Filament-based interface

## Requirements

- PHP 8.2+
- Laravel 12
- Composer
- Meilisearch server
- MySQL/PostgreSQL/SQLite

## Installation

1. **Clone the repository**
   ```bash
   git clone https://github.
   com/cyberpunk3351/inventory
   cd inventory
   ```

2. **Docker**
   ```bash
   docker compose up --build
   docker exec -it -u www-data inventory_app bash
   composer install
   ```

3. **Environment configuration**
   ```bash
   cp .env.local .env
   ```

4. **Configure environment variables**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=inventory_db
   DB_PORT=3306
   DB_DATABASE=laravel
   DB_USERNAME=root
   DB_PASSWORD=root

   SCOUT_DRIVER=meilisearch
   MEILISEARCH_HOST=http://localhost:7700
   MEILISEARCH_KEY=masterKey
   ```

5. **Generate application key**
   ```bash
   php artisan key:generate
   ```

6. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed --class=DeviceSeeder
   ```

7. **Configure search indexes**
   ```bash
   php artisan scout:import "App\Models\Device"
   ```

8. **Create admin user**
   ```bash
   php artisan make:filament-user
   ```

## Running the Application

1. **Access the application**
    - Admin Panel: `http://localhost:5435/admin`
    - Login with the credentials created in step 9

## Device Health Calculation

The system automatically calculates device health based on the following criteria:

- **Perfect**: 0-25% of lifecycle passed
- **Good**: 26-50% of lifecycle passed
- **Fair**: 51-75% of lifecycle passed
- **Poor**: 76%+ of lifecycle passed
- **N/A**: Device not yet deployed

## Project Structure

```
app/
├── Filament/
│   ├── Resources/
│   │   └── DeviceResource.php
│   └── Widgets/
│       └── DeviceHealthOverview.php
├── Models/
│   └── Device.php
└── Providers/
    └── Filament/
        └── AdminPanelProvider.php

database/
├── factories/
│   └── DeviceFactory.php
├── migrations/
│   └── create_devices_table.php
└── seeders/
    └── DeviceSeeder.php
```

## Key Features

### Device Management
- Create, read, update, and delete devices
- Fields: Name, First Deployed At, Health Lifecycle (value + unit)
- Automatic health calculation and display

### Dashboard
- Overview cards showing device counts by health status
- Real-time statistics
- Visual health indicators

### Search & Filtering
- Full-text search on device names
- Filter by health status (Perfect, Good, Fair, Poor)
- Powered by Meilisearch for fast, relevant results

### Health Lifecycle
- Flexible lifecycle units (days, months, years)
- Automatic percentage calculation
- Color-coded health badges

## Development

### Resetting Search Index
```bash
php artisan scout:flush "App\Models\Device"
php artisan scout:import "App\Models\Device"
```

### Updating Sample Data
```bash
php artisan db:seed --class=DeviceSeeder
```

## Configuration

### Scout Configuration
The search functionality is configured in `config/scout.php` with Meilisearch-specific settings for filterable and searchable attributes.

### Filament Configuration
Admin panel settings are configured in `app/Providers/Filament/AdminPanelProvider.php`.

