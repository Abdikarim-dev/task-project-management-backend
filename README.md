# Task & Project Management — Backend API

Laravel 12 REST API for task and project management with role-based access control.

## Stack

- Laravel 12
- PHP 8.4+
- MySQL
- Laravel Sanctum

## Setup

```bash
cd server
composer install
cp .env.example .env
php artisan key:generate
```

Configure MySQL in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_project_management
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations and seed demo data:

```bash
php artisan migrate --seed
php artisan serve
```

## Demo Accounts

| Role  | Email             | Password  |
|-------|-------------------|-----------|
| Admin | admin@example.com | password  |
| Staff | sarah@example.com | password  |
| Staff | michael@example.com | password |
| Staff | emily@example.com | password |

## API Endpoints

### Authentication

| Method | Endpoint        | Auth | Description        |
|--------|-----------------|------|--------------------|
| POST   | /api/auth/login | No   | Login, returns token |
| POST   | /api/auth/logout| Yes  | Revoke current token |
| GET    | /api/auth/me    | Yes  | Current user profile |

### Dashboard

| Method | Endpoint       | Auth | Description                    |
|--------|----------------|------|--------------------------------|
| GET    | /api/dashboard | Yes  | Statistics and recent tasks    |

### Projects (Admin only)

| Method | Endpoint            | Description    |
|--------|---------------------|----------------|
| GET    | /api/projects       | List projects  |
| POST   | /api/projects       | Create project |
| GET    | /api/projects/{id}  | Show project   |
| PUT    | /api/projects/{id}  | Update project |
| DELETE | /api/projects/{id}  | Delete project |

### Tasks

| Method | Endpoint                  | Access                          |
|--------|---------------------------|---------------------------------|
| GET    | /api/tasks                | Admin: all / Staff: assigned    |
| POST   | /api/tasks                | Admin only                      |
| GET    | /api/tasks/{id}           | Admin or assigned staff         |
| PUT    | /api/tasks/{id}           | Admin only                      |
| PATCH  | /api/tasks/{id}/status    | Admin or assigned staff         |
| DELETE | /api/tasks/{id}           | Admin only                      |

## Authorization

- **Admin**: Full access to projects and tasks.
- **Staff**: View assigned tasks, update task status only, personal dashboard.

## Architecture

```
Controller → Service → Repository → Model
Form Request (validation)
Policy (authorization)
API Resource (response formatting)
```

## Response Format

```json
{
  "success": true,
  "message": "Project created successfully.",
  "data": { }
}
```

Send the Sanctum token as a Bearer token:

```
Authorization: Bearer {token}
```
