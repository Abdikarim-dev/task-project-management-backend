# Task & Project Management — Backend API

Laravel 12 REST API for task and project management with role-based access control. Demo data uses **Somali-themed names, organisations, and locations written in English** (e.g. Mogadishu Port Authority, Dahabshiil, Berbera Corridor).

## Stack

- Laravel 12
- PHP 8.2+
- MySQL
- Laravel Sanctum

---

## Setup

### Requirements

| Tool | Version |
|------|---------|
| PHP | 8.2+ |
| Composer | 2.x |
| MySQL | 8.x |

### Windows note

If XAMPP ships PHP 8.1, it is **too old** for this project. Either:

1. **Use a standalone PHP 8.2 install** (recommended): download from [windows.php.net](https://windows.php.net/download/) and add it to your PATH **before** XAMPP, e.g. `C:\php82`.
2. Or upgrade XAMPP to a bundle that includes PHP 8.2+.

Verify:

```bash
php -v        # should show 8.2 or higher
composer -V
```

### Install and run

```bash
cd server
composer install
cp .env.example .env
php artisan key:generate
```

Configure MySQL in `.env`:

```env
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=task_project_management
DB_USERNAME=root
DB_PASSWORD=
```

Create the database, migrate, and seed Somali-themed demo data:

```bash
# Create the database in MySQL first, then:
php artisan migrate:fresh --seed
php artisan serve
```

The API is available at `http://localhost:8000/api`.

### Fresh seed (reset demo data)

```bash
php artisan migrate:fresh --seed
```

This drops all tables, recreates the schema, and loads Somali-themed demo data (names, organisations, and cities written in English).

#### Users (4)

| Name | Role | Email | Job title |
|------|------|-------|-----------|
| Hassan Abdi | Admin | admin@aleelo.org | Platform Administrator |
| Amina Mohamed | Staff | staff@aleelo.org | Software Engineer |
| Ibrahim Hashi | Staff | ibrahim@example.com | Project Coordinator |
| Khadija Osman | Staff | khadija@example.com | Business Analyst |

All accounts use password `password`. Phone numbers use the `+252` Somalia country code.

#### Projects (6)

| Project | Client | Status | Tasks seeded |
|---------|--------|--------|--------------|
| Mogadishu Port Digital Platform | Mogadishu Port Authority | Active | Yes |
| Somali Mobile Money Gateway | Salaam Somali Bank | Planning | Yes |
| Diaspora Remittance Portal | Dahabshiil | Active | Yes |
| Berbera Corridor Logistics Hub | Somaliland Trade Commission | Completed | Yes |
| National Livestock Export System | Ministry of Livestock — Federal Republic of Somalia | On Hold | Yes |
| Zoobe Shop | Al Huda | Planning | **No** (empty — can be deleted in the UI) |

Each project has a fixed team; tasks are only assigned to staff on that project's team.

#### Tasks (30)

Ten Somali-context task templates are rotated across the first five projects, for example:

- Gather requirements from Mogadishu port stakeholders
- Integrate Hormuud EVC Plus payment callback
- Configure Somali Shilling (SOS) currency formatting
- Implement bilingual Somali and English UI labels
- Design Berbera corridor route mapping schema
- Document Salaam Somali Bank sandbox API
- Fix remittance status sync for Hargeisa branches
- Prepare Central Bank of Somalia compliance checklist

### Demo accounts

| Role | Name | Email | Password |
|------|------|-------|----------|
| Admin | Hassan Abdi | admin@aleelo.org | password |
| Staff | Amina Mohamed | staff@aleelo.org | password |
| Staff | Ibrahim Hashi | ibrahim@example.com | password |
| Staff | Khadija Osman | khadija@example.com | password |

---

## Database structure

This API uses **6 MySQL tables** — only what the application needs:

| Table | Purpose |
|-------|---------|
| `migrations` | Laravel schema version tracking |
| `users` | Admin and staff accounts |
| `projects` | Client projects |
| `project_user` | Project team membership (pivot) |
| `tasks` | Work items assigned to staff |
| `personal_access_tokens` | Sanctum API bearer tokens |

Unused Laravel boilerplate tables (`sessions`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `password_reset_tokens`) are **not** created. Migration `2026_07_06_000006_drop_unused_laravel_tables` removes them if they exist from an older install.

The API is token-based (Sanctum); sessions, database cache, and database queues are disabled by default (`SESSION_DRIVER=array`, `CACHE_STORE=file`, `QUEUE_CONNECTION=sync`).

### Entity relationship

```
users ─────┬──── project_user ──── projects
           │                           │
           └──── tasks.assigned_to     └── tasks.project_id
```

- A **user** has a role (`admin` or `staff`) and optional profile fields.
- A **project** belongs to many **users** through the `project_user` pivot (team members).
- A **task** belongs to one **project** and may be assigned to one **user** (staff on that project's team).

### Tables

#### `users`

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | Primary key |
| `name` | string | Full name |
| `email` | string | Unique login |
| `password` | string | Hashed |
| `role` | string | `admin` or `staff` |
| `is_suspended` | boolean | Blocks login when true |
| `job_title` | string | Nullable |
| `phone` | string | Nullable |
| `bio` | text | Nullable |
| `theme_preference` | string | `light` or `dark` (default `light`) |
| `email_verified_at` | timestamp | Nullable |
| `timestamps` | | `created_at`, `updated_at` |

#### `projects`

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | Primary key |
| `name` | string | e.g. Mogadishu Port Digital Platform |
| `client_name` | string | e.g. Mogadishu Port Authority |
| `description` | text | Nullable |
| `start_date` | date | |
| `due_date` | date | |
| `status` | string | `planning`, `active`, `completed`, `on_hold` |
| `timestamps` | | |
| `deleted_at` | timestamp | Soft delete |

#### `project_user` (pivot)

| Column | Type | Notes |
|--------|------|-------|
| `project_id` | FK | → `projects.id` |
| `user_id` | FK | → `users.id` |
| Unique | | `(project_id, user_id)` |

Defines which staff can be assigned tasks on a project.

#### `tasks`

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | Primary key |
| `project_id` | FK | → `projects.id` (cascade delete) |
| `assigned_to` | FK | → `users.id` (nullable, null on delete) |
| `title` | string | |
| `description` | text | Nullable |
| `priority` | string | `low`, `medium`, `high` |
| `status` | string | `to_do`, `in_progress`, `completed` |
| `due_date` | date | Nullable |
| `timestamps` | | |
| `deleted_at` | timestamp | Soft delete |

#### `personal_access_tokens`

Sanctum API tokens — one row per issued bearer token. Managed by Laravel Sanctum (no Eloquent model in this app).

### Removed / not used

The following default Laravel tables are intentionally omitted because this API does not use them:

| Table | Reason |
|-------|--------|
| `sessions` | Auth is token-based; the Blade BFF handles its own sessions |
| `password_reset_tokens` | No forgot-password flow |
| `cache` / `cache_locks` | Cache driver is `file` |
| `jobs` / `job_batches` / `failed_jobs` | Queue driver is `sync` |

Dropped automatically by `2026_07_06_000006_drop_unused_laravel_tables` when upgrading from an older schema.

### Business rules (enforced in services / validation)

- Projects can only be **deleted** when they have **zero tasks**.
- Task assignees must be **team members** of the task's project.
- Removing a user from a project team **unassigns** their tasks on that project.
- Suspended users cannot log in.

---

## API overview

Base URL: `/api`

All protected routes require a Sanctum bearer token:

```
Authorization: Bearer {token}
```

### Response envelope

```json
{
  "success": true,
  "message": "Project created successfully.",
  "data": { }
}
```

Errors return `success: false` with HTTP 4xx/5xx and a `message` (and validation `errors` when applicable).

List endpoints return paginated data:

```json
{
  "success": true,
  "message": "Tasks retrieved successfully.",
  "data": {
    "items": [ ],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 30,
      "last_page": 2
    }
  }
}
```

### Authentication

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| POST | `/api/auth/login` | No | Login with `email` + `password`; returns user + token |
| POST | `/api/auth/logout` | Yes | Revoke current token |
| GET | `/api/auth/me` | Yes | Current authenticated user |
| PATCH | `/api/auth/profile` | Yes | Update own profile (`name`, `email`, `theme_preference`, etc.) |

**Login example:**

```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@aleelo.org","password":"password"}'
```

### Dashboard

| Method | Endpoint | Auth | Description |
|--------|----------|------|-------------|
| GET | `/api/dashboard` | Yes | Role-aware stats, charts, recent activity, recent tasks/projects |

- **Admin** — organisation-wide metrics.
- **Staff** — metrics scoped to tasks assigned to the signed-in user.

### Projects (admin only)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/projects` | Paginated list (`search`, `status`, `per_page`) |
| POST | `/api/projects` | Create project + attach team members |
| GET | `/api/projects/{id}` | Project detail with team and tasks |
| PUT | `/api/projects/{id}` | Update project and team |
| DELETE | `/api/projects/{id}` | Delete only if `tasks_count === 0` |

`POST` / `PUT` body includes `team_member_ids` (array of user IDs).

### Tasks

| Method | Endpoint | Access | Description |
|--------|----------|--------|-------------|
| GET | `/api/tasks` | Admin: all · Staff: assigned | List with `search`, `status`, `priority`, `sort` |
| POST | `/api/tasks` | Admin | Create task (`assigned_to` must be on project team) |
| GET | `/api/tasks/{id}` | Admin or assignee | Task detail |
| PUT | `/api/tasks/{id}` | Admin | Update task |
| PATCH | `/api/tasks/{id}/status` | Admin or assignee | Update status only |
| DELETE | `/api/tasks/{id}` | Admin | Soft delete |

### Users (admin only, except profile)

| Method | Endpoint | Access | Description |
|--------|----------|--------|-------------|
| GET | `/api/users` | Admin | Paginated list (`search`, `role`) |
| POST | `/api/users` | Admin | Create staff user |
| GET | `/api/users/{id}` | Admin | User detail with stats, charts, projects |
| PUT | `/api/users/{id}` | Admin | Update user |
| PATCH | `/api/users/{id}/suspend` | Admin | Toggle suspend / reactivate |

---

## Authorization

| Role | Projects | Tasks | Users | Dashboard |
|------|----------|-------|-------|-----------|
| **Admin** | Full CRUD | Full CRUD | Full management | Organisation-wide |
| **Staff** | — | View/update own assigned tasks | — | Personal scope |

Policies (`ProjectPolicy`, `TaskPolicy`, `UserPolicy`) enforce access on each endpoint.

---

## Architecture

```
HTTP Request
    → Route (api.php)
    → Controller
    → Form Request (validation)
    → Policy (authorization)
    → Service (business logic)
    → Repository (queries)
    → Model (Eloquent)
    → API Resource (JSON shape)
    → ApiResponse envelope
```

---

## Related

- Frontend (Blade BFF): [`../client/README.md`](../client/README.md)
- UI screenshots: [`../client/docs/SCREENSHOTS.md`](../client/docs/SCREENSHOTS.md)

