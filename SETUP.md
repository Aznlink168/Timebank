# Timebank Project Setup Guide

## Overview

Timebank is a Laravel-based community platform where users can offer their skills and services, or request help from others. The platform enables time-based exchange of services within a community.

## Prerequisites

- PHP 8.1 or higher
- Composer
- Node.js and NPM
- SQLite (or MySQL/PostgreSQL)

## Installation Steps

### 1. Clone the Repository

```bash
git clone https://github.com/Aznlink168/Timebank.git
cd Timebank/timebank
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Configuration

```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Database Setup

The project is configured to use SQLite by default. If you want to use MySQL or PostgreSQL, update the `.env` file accordingly.

For SQLite (default):
```bash
# Create the database file
touch database/database.sqlite

# Run migrations
php artisan migrate
```

For MySQL/PostgreSQL:
Update these lines in `.env`:
```
DB_CONNECTION=mysql  # or pgsql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Then run:
```bash
php artisan migrate
```

### 5. Build Frontend Assets

```bash
npm run build
```

For development with hot reload:
```bash
npm run dev
```

### 6. Start the Application

```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## Key Features

### User Features
- User registration and authentication (via Laravel Jetstream)
- Profile management with skills and availability
- Create and browse service requests
- Activity hub to track assigned tasks and personal requests
- QR code scanning for service verification

### Admin Features
- User management
- Skills management
- Service categories management
- Service requests oversight
- Service assignments management

## Database Structure

The application includes the following main tables:

- **users** - User accounts with profiles
- **service_categories** - Categories for service requests
- **service_requests** - Service requests from users
- **skills** - Available skills in the system
- **user_skill** - Many-to-many relationship between users and skills
- **availability** - User availability schedules
- **availability_exceptions** - Exceptions to regular availability
- **service_assignments** - Assignments of volunteers to service requests
- **notifications** - User notifications
- **watchlist** - Service requests users are watching
- **priority_changes** - History of priority changes to requests

## Testing

Run the test suite:

```bash
php artisan test
```

## Technology Stack

- **Backend**: Laravel 10
- **Frontend**: Laravel Livewire, Tailwind CSS
- **Authentication**: Laravel Jetstream with Livewire stack
- **Database**: SQLite (default), MySQL, or PostgreSQL
- **Additional Features**: 
  - QR Code generation and scanning
  - Real-time updates with Livewire
  - Email/SMS notifications (Twilio SDK integrated)

## Routes

### Public Routes
- `/` - Welcome page
- `/login` - User login
- `/register` - User registration

### Authenticated User Routes
- `/dashboard` - User dashboard
- `/service-requests` - Browse service requests
- `/service-requests/create` - Create a new service request
- `/user/extended-profile` - Manage profile, skills, and availability
- `/user/activity-hub` - View personal activities and assignments
- `/qr-scanner` - QR code scanner for service verification

### Admin Routes (requires admin role)
- `/admin/dashboard` - Admin dashboard
- `/admin/users` - User management
- `/admin/skills` - Skills management
- `/admin/service-categories` - Service categories management
- `/admin/service-requests` - Service requests management
- `/admin/service-assignments` - Service assignments management

## Development

### Code Style

The project uses Laravel Pint for code formatting:

```bash
./vendor/bin/pint
```

### Linting

ESLint is available for JavaScript/Vue files if needed.

## Troubleshooting

### Database Issues

If you encounter migration errors, you can reset the database:

```bash
php artisan migrate:fresh
```

### Permission Issues

Ensure the storage and bootstrap/cache directories are writable:

```bash
chmod -R 775 storage bootstrap/cache
```

### Asset Issues

If styles or scripts are not loading, rebuild the assets:

```bash
npm run build
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests
5. Submit a pull request

## License

This project is open-sourced software licensed under the MIT license.
