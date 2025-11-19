# Academic Advisor Appointment System

A comprehensive web application for managing academic advisor appointments built with PHP, following Laravel-style MVC architecture and using Tailwind CSS for the frontend.

## Features

### For Students
- 🔍 Search and browse advisors by department
- 📅 View advisor availability
- 📝 Book appointments with preferred time slots
- 📋 View and manage appointment history
- ❌ Cancel appointments
- 🔔 Receive real-time notifications

### For Advisors
- 📊 Dashboard with appointment overview
- ⏰ Manage availability schedule
- ✅ Confirm or decline appointment requests
- 👥 View student information
- 📈 Track appointment statistics

### For Administrators
- 👤 User management (CRUD operations)
- 📤 CSV bulk user import
- 📊 System-wide reports and analytics
- 🔍 View all appointments
- 📝 Audit log tracking

## Technology Stack

- **Backend**: PHP 8.3+
- **Database**: SQLite (easily adaptable to MySQL/PostgreSQL)
- **Frontend**: Tailwind CSS (via CDN)
- **Architecture**: MVC (Model-View-Controller)
- **Authentication**: Session-based with role-based access control

## Requirements

- PHP 8.1 or higher
- PDO SQLite extension
- Web server (Apache, Nginx, or PHP built-in server)
- Composer (optional, for future extensions)

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/mrgargom/shiftleft-php-demo.git
cd shiftleft-php-demo
```

### 2. Database Setup

The database will be automatically created when you first run the application. To manually set up the database:

```bash
# Initialize the database
php -r "require 'config/database.php'; Database::getInstance();"
```

### 3. Seed Sample Data

```bash
php database/seeders/DatabaseSeeder.php
```

This will create:
- 1 Administrator account
- 3 Advisor accounts
- 3 Student accounts
- Sample availabilities
- Sample appointments

### 4. Configure Environment

Copy `.env.example` to `.env` and update settings if needed:

```bash
cp .env.example .env
```

### 5. Start the Server

Using PHP built-in server:

```bash
php -S localhost:8000
```

Or configure your web server to point to the project root directory.

### 6. Access the Application

Open your browser and navigate to:
```
http://localhost:8000
```

## Default Credentials

After seeding the database, you can login with:

| Role | Email | Password |
|------|-------|----------|
| Administrator | admin@example.com | password |
| Advisor | advisor@example.com | password |
| Student | student@example.com | password |

## Project Structure

```
shiftleft-php-demo/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Request handlers
│   │   │   ├── AuthController.php
│   │   │   ├── StudentController.php
│   │   │   ├── AdvisorController.php
│   │   │   └── AdminController.php
│   │   └── Middleware/        # Authentication & authorization
│   │       └── Auth.php
│   ├── Models/                # Data models
│   │   ├── User.php
│   │   ├── Student.php
│   │   ├── Advisor.php
│   │   ├── Administrator.php
│   │   ├── Appointment.php
│   │   ├── Availability.php
│   │   └── Notification.php
│   └── Services/              # Business logic
│       └── NotificationService.php
├── config/
│   └── database.php           # Database configuration
├── database/
│   ├── migrations/
│   │   └── create_tables.sql  # Database schema
│   └── seeders/
│       └── DatabaseSeeder.php # Sample data
├── resources/
│   └── views/                 # Blade-style templates
│       ├── layouts/
│       ├── auth/
│       ├── student/
│       ├── advisor/
│       └── admin/
├── routes/
│   └── web.php                # Application routes
├── .env                       # Environment configuration
└── index.php                  # Application entry point
```

## Database Schema

The application uses 12 main tables:

- `users` - Base authentication
- `students` - Student profiles
- `advisors` - Advisor profiles
- `administrators` - Admin profiles
- `appointments` - Appointment records
- `availabilities` - Advisor availability slots
- `notifications` - System notifications
- `jobs` - Queue jobs
- `failed_jobs` - Failed queue jobs
- `password_reset_tokens` - Password reset tokens
- `personal_access_tokens` - API tokens
- `audit_logs` - Admin action tracking

## Key Features Implementation

### Role-Based Access Control

The system implements three user roles:
- **Student**: Can search advisors, book appointments, view their appointments
- **Advisor**: Can manage availability, respond to appointment requests
- **Administrator**: Full system access, user management, reports

### Appointment Lifecycle

1. **Pending**: Student creates appointment request
2. **Confirmed**: Advisor accepts the request
3. **Declined**: Advisor rejects the request
4. **Cancelled**: Either party cancels the appointment
5. **Completed**: Appointment has been fulfilled

### Notification System

- Real-time in-app notifications
- Event-based triggers for:
  - Appointment creation
  - Appointment confirmation
  - Appointment decline
  - Appointment cancellation
  - Appointment reminders (ready for cron jobs)

### CSV Import Format

Administrators can import users via CSV with this format:

**For Students:**
```csv
role,name,email,password,student_id,major,year_level,gpa,phone
student,John Doe,john@example.com,password123,STU001,Computer Science,Junior,3.75,555-1234
```

**For Advisors:**
```csv
role,name,email,password,advisor_id,department,office_location,phone_number
advisor,Dr. Smith,smith@example.com,password123,ADV001,Engineering,Building A Room 101,555-5678
```

## API Routes

### Public Routes
- `GET /` - Home page
- `GET /login` - Login page
- `POST /login` - Login handler
- `GET /logout` - Logout

### Student Routes (Protected)
- `GET /student/dashboard` - Student dashboard
- `GET /student/advisors` - Search advisors
- `GET /student/appointments` - View appointments
- `GET /student/appointments/create` - Appointment booking form
- `POST /student/appointments/store` - Create appointment
- `POST /student/appointments/cancel` - Cancel appointment

### Advisor Routes (Protected)
- `GET /advisor/dashboard` - Advisor dashboard
- `GET /advisor/appointments` - View all appointments
- `GET /advisor/availability` - Manage availability
- `GET /advisor/availability/create` - Add availability form
- `POST /advisor/availability/store` - Save availability
- `POST /advisor/availability/delete` - Remove availability
- `POST /advisor/appointments/confirm` - Confirm appointment
- `POST /advisor/appointments/decline` - Decline appointment

### Admin Routes (Protected)
- `GET /admin/dashboard` - Admin dashboard
- `GET /admin/users` - User management
- `GET /admin/users/create` - Create user form
- `POST /admin/users/store` - Save new user
- `POST /admin/users/delete` - Delete user
- `POST /admin/users/import` - Import users from CSV
- `GET /admin/appointments` - View all appointments
- `GET /admin/reports` - Reports and analytics

## Security Features

- ✅ Password hashing (PHP's `password_hash`)
- ✅ Session-based authentication
- ✅ Role-based authorization
- ✅ SQL injection prevention (PDO prepared statements)
- ✅ XSS protection (HTML escaping)
- ✅ CSRF protection (ready for implementation)
- ✅ Input validation
- ✅ Audit logging for admin actions

## Development

### Adding New Features

1. **Create Model**: Add to `app/Models/`
2. **Create Controller**: Add to `app/Http/Controllers/`
3. **Add Routes**: Update `routes/web.php`
4. **Create Views**: Add to `resources/views/`

### Database Migrations

To modify the database schema, edit:
```
database/migrations/create_tables.sql
```

Then recreate the database:
```bash
rm database/academic_advisor.db
php -r "require 'config/database.php'; Database::getInstance();"
php database/seeders/DatabaseSeeder.php
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## License

MIT License

## Support

For issues and questions, please use the GitHub issue tracker.

---

Built with ❤️ following Laravel best practices
