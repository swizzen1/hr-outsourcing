# HR Outsourcing Platform (Laravel 11)

Multi-tenant HR outsourcing platform with strict tenant isolation, Sanctum auth, and Blade UI.

## Quick Start

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan serve
```

Visit `http://localhost:8000/login` and use the demo credentials below.

## Setup

1) Install dependencies

```bash
composer install
```

2) Create environment file

```bash
cp .env.example .env
```

3) Generate app key

```bash
php artisan key:generate
```

4) Prepare SQLite database

```bash
mkdir -p database
[ -f database/database.sqlite ] || touch database/database.sqlite
```

5) Run migrations + seed demo data

```bash
php artisan migrate --seed
```

6) Start the dev server

```bash
php artisan serve
```

## Demo Users (seeded)

All seeded users use password `password`.

- Admin: `admin@platform.test` / `password`
- HR: `hr@platform.test` / `password`
- Company Admins:
  - `acme-corp_admin@platform.test` / `password`
  - `globex-inc_admin@platform.test` / `password`
- Employees:
  - `acme-corp_employee1@platform.test` / `password`
  - `acme-corp_employee2@platform.test` / `password`
  - `globex-inc_employee1@platform.test` / `password`
  - `globex-inc_employee2@platform.test` / `password`

## Notes

- SQLite is configured in `.env.example` by default.
- Sanctum is installed for API authentication.
- Absences enforce unique (user_id, date) at the DB level and the app returns 422 on duplicates.

## Manual Verification Checklist

1) Setup

```bash
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan serve
```

2) Routes to verify

- Public API: `GET /api/v1/public/vacancies`
- Auth API: `POST /api/v1/companies/{company}/vacancies` (Admin/HR/Company Admin)
- Employee: `/me/attendance`, `/me/leave-requests`
- Company approvals: `/companies/{company}/leave-requests`
- Absences: `/companies/{company}/absences`

3) Demo credentials

- Admin: `admin@platform.test` / `password`
- HR: `hr@platform.test` / `password`
- Company Admin: `acme-corp_admin@platform.test` / `password`
- Employee: `acme-corp_employee1@platform.test` / `password`

## Architecture & Tenant Isolation

- `company_id` is present on all tenant-owned tables (vacancies, positions, leave requests, absences, attendances).
- `EnsureCompanyAccess` middleware protects `/companies/{company}/...` routes and blocks cross-tenant access.
- Policies enforce role-based actions (Admin/HR global, Company Admin within company, Employee self-only).
- `CompanyScope` global scope filters tenant models by `company_id` for non HR/Admin users; HR/Admin bypass via policy `before` check.

## API Reference (v1)

### Public vacancies

```bash
curl http://localhost:8000/api/v1/public/vacancies
curl http://localhost:8000/api/v1/public/vacancies/1
```

### Auth vacancy create

```bash
curl -X POST http://localhost:8000/api/v1/companies/1/vacancies \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "QA Engineer",
    "description": "Own test plans and automation.",
    "location": "Remote",
    "employment_type": "contract",
    "status": "published",
    "expiration_date": "2026-02-15"
  }'
```

### Attendance check-in/out

```bash
curl -X POST http://localhost:8000/api/v1/me/attendance/check-in \
  -H "Authorization: Bearer <token>"
curl -X POST http://localhost:8000/api/v1/me/attendance/check-out \
  -H "Authorization: Bearer <token>"
```

### Leave requests

```bash
curl -X POST http://localhost:8000/api/v1/me/leave-requests \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "start_date": "2026-01-10",
    "end_date": "2026-01-12",
    "reason": "Medical appointment",
    "type": "sick"
  }'
curl http://localhost:8000/api/v1/me/leave-requests \
  -H "Authorization: Bearer <token>"
curl -X PATCH http://localhost:8000/api/v1/companies/1/leave-requests/10/approve \
  -H "Authorization: Bearer <token>"
curl -X PATCH http://localhost:8000/api/v1/companies/1/leave-requests/10/reject \
  -H "Authorization: Bearer <token>"
```

### Absences

```bash
curl -X POST http://localhost:8000/api/v1/companies/1/absences \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 5,
    "date": "2026-01-06",
    "reason": "Unplanned absence"
  }'
curl http://localhost:8000/api/v1/companies/1/absences \
  -H "Authorization: Bearer <token>"
```

## Running Tests

```bash
php artisan test
```

Relevant test classes:
- `tests/Feature/TenantIsolationTest.php`
- `tests/Feature/PublicVacanciesTest.php`
- `tests/Feature/VacancyCreateTest.php`
- `tests/Feature/AttendanceCheckTest.php`
- `tests/Feature/LeaveRequestsTest.php`
- `tests/Feature/AbsenceTest.php`

## Public Vacancies API

### List vacancies

```bash
curl http://localhost:8000/api/v1/public/vacancies
```

Example response:

```json
{
  "data": [
    {
      "id": 1,
      "title": "Backend Developer",
      "description": "Build and maintain APIs for internal platforms.",
      "location": "Remote",
      "employment_type": "full_time",
      "published_at": "2026-01-04T10:00:00Z",
      "expiration_date": "2026-02-04"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 10,
    "total": 1
  }
}
```

### Vacancy detail

```bash
curl http://localhost:8000/api/v1/public/vacancies/1
```

Example response:

```json
{
  "data": {
    "id": 1,
    "title": "Backend Developer",
    "description": "Build and maintain APIs for internal platforms.",
    "location": "Remote",
    "employment_type": "full_time",
    "published_at": "2026-01-04T10:00:00Z",
    "expiration_date": "2026-02-04"
  }
}
```

## Auth Vacancy Create API

### Create vacancy (requires Bearer token)

```bash
curl -X POST http://localhost:8000/api/v1/companies/1/vacancies \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "QA Engineer",
    "description": "Own test plans and automation.",
    "location": "Remote",
    "employment_type": "contract",
    "status": "published",
    "expiration_date": "2026-02-15"
  }'
```

Example response:

```json
{
  "data": {
    "id": 10,
    "title": "QA Engineer",
    "description": "Own test plans and automation.",
    "location": "Remote",
    "employment_type": "contract",
    "published_at": "2026-01-05T10:00:00Z",
    "expiration_date": "2026-02-15"
  }
}
```

## Attendance (Self-Service)

### Check-in

```bash
curl -X POST http://localhost:8000/api/v1/me/attendance/check-in \
  -H "Authorization: Bearer <token>"
```

Example response:

```json
{
  "data": {
    "date": "2026-01-05",
    "check_in_at": "2026-01-05T09:00:00Z",
    "check_out_at": null
  }
}
```

### Check-out

```bash
curl -X POST http://localhost:8000/api/v1/me/attendance/check-out \
  -H "Authorization: Bearer <token>"
```

Example response:

```json
{
  "data": {
    "date": "2026-01-05",
    "check_in_at": "2026-01-05T09:00:00Z",
    "check_out_at": "2026-01-05T17:00:00Z"
  }
}
```

## Leave Requests

### Create leave request (self)

```bash
curl -X POST http://localhost:8000/api/v1/me/leave-requests \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "start_date": "2026-01-10",
    "end_date": "2026-01-12",
    "reason": "Medical appointment",
    "type": "sick"
  }'
```

### List my leave requests

```bash
curl http://localhost:8000/api/v1/me/leave-requests \
  -H "Authorization: Bearer <token>"
```

### Approve leave request (Company Admin/HR/Admin)

```bash
curl -X PATCH http://localhost:8000/api/v1/companies/1/leave-requests/10/approve \
  -H "Authorization: Bearer <token>"
```

### Reject leave request (Company Admin/HR/Admin)

```bash
curl -X PATCH http://localhost:8000/api/v1/companies/1/leave-requests/10/reject \
  -H "Authorization: Bearer <token>"
```

## Absences (HR / Company Admin)

### Create absence

```bash
curl -X POST http://localhost:8000/api/v1/companies/1/absences \
  -H "Authorization: Bearer <token>" \
  -H "Content-Type: application/json" \
  -d '{
    "user_id": 5,
    "date": "2026-01-06",
    "reason": "Unplanned absence"
  }'
```

### List absences

```bash
curl http://localhost:8000/api/v1/companies/1/absences \
  -H "Authorization: Bearer <token>"
```
