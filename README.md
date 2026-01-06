# HR Outsourcing Platform (Laravel 11)

A multi-tenant HR outsourcing platform built with **Laravel 11**, featuring strict tenant isolation, **Sanctum** authentication, and a **Blade** UI.

---

## ✨ Features

- Multi-tenant architecture with strong data isolation  
- Role-based access control (Admin, HR, Company Admin, Employee)  
- Sanctum API authentication (Bearer tokens)  
- Public and protected API endpoints  
- Seeded demo data for instant testing  

---

## 🚀 Quick Start

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan serve
```

Open:  
👉 `http://localhost:8000/login` and log in using the demo credentials below.

---

## ⚙️ Setup

### 1. Install dependencies
```bash
composer install
```

### 2. Create environment file
```bash
cp .env.example .env
```

### 3. Generate application key
```bash
php artisan key:generate
```

### 4. Prepare SQLite database
```bash
mkdir -p database
[ -f database/database.sqlite ] || touch database/database.sqlite
```

### 5. Run migrations and seed demo data
```bash
php artisan migrate --seed
```

### 6. Start the development server
```bash
php artisan serve
```

---

## 👤 Demo Users (Seeded)

All demo accounts use the password: **`password`**

### Admin
- `admin@platform.test`

### HR
- `hr@platform.test`

### Company Admins
- `acme-corp_admin@platform.test`
- `globex-inc_admin@platform.test`

### Employees
- `acme-corp_employee1@platform.test`
- `acme-corp_employee2@platform.test`
- `globex-inc_employee1@platform.test`
- `globex-inc_employee2@platform.test`

---

## 📝 Notes

- SQLite is configured by default in `.env.example`.  
- API authentication is handled using **Laravel Sanctum**.  
- Absences enforce a unique constraint on `(user_id, date)` at the database level; duplicates return **422 Unprocessable Entity**.

---

## 🔐 API Authentication (Bearer Token)

Use the login endpoint to retrieve a token, then include it in the `Authorization` header for protected routes.

### Login Endpoint
```
POST /api/v1/auth/login
```

**Body parameters**
- `email` (required)
- `password` (required)
- `device_name` (optional, defaults to `api`)

**Example request**
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@platform.test",
    "password": "password",
    "device_name": "local"
  }'
```

**Example response**
```json
{
  "token": "YOUR_TOKEN",
  "token_type": "Bearer"
}
```

**Using the token**
```bash
curl http://localhost:8000/api/v1/companies/1/vacancies \
  -H "Authorization: Bearer YOUR_TOKEN"
```

---

## ✅ Manual Verification Checklist

### 1. Setup
```bash
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan serve
```

### 2. Routes to verify
- **Public API:** `GET /api/v1/public/vacancies`  
- **Public API:** `GET /api/v1/public/vacancies/1`  
- **Authenticated API:** `POST /api/v1/companies/{company}/vacancies` (Admin, HR, Company Admin)

### 3. Test credentials
- Admin: `admin@platform.test` / `password`  
- HR: `hr@platform.test` / `password`  
- Company Admin: `acme-corp_admin@platform.test` / `password`  
- Employee: `acme-corp_employee1@platform.test` / `password`  

---

## 🏗 Architecture & Tenant Isolation

- Every tenant-owned table includes `company_id` (e.g., vacancies, positions, leave requests, absences, attendances).  
- `EnsureCompanyAccess` middleware protects `/companies/{company}/...` routes and prevents cross-tenant access.  
- Authorization policies enforce role-based permissions:  
  - **Admin/HR:** global access  
  - **Company Admin:** access within their company  
  - **Employee:** self-only access  
- `CompanyScope` global scope automatically filters records by `company_id` for non-Admin/HR users.  
- Admin/HR users bypass tenant restrictions via policy `before` checks.

---

## 📚 API Reference (v1)

### Authentication
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@platform.test",
    "password": "password",
    "device_name": "local"
  }'
```

### Public Vacancies
```bash
curl http://localhost:8000/api/v1/public/vacancies
curl http://localhost:8000/api/v1/public/vacancies/1
```

### Create Vacancy (Authenticated)
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

---

## 🧪 Running Tests

```bash
php artisan test
```
