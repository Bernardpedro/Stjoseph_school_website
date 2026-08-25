# Saint Joseph TSS Nzuki

School website with a **Nuxt 3** frontend and a **CodeIgniter 4** API backend.

```
StJoseph_Nzuki/
├── backend/    # PHP API (CodeIgniter 4)
└── frontend/   # Nuxt 3 + Tailwind
```

## Requirements

- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL/MariaDB) with **PHP 8.2+**
- [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) 18+ (npm)
- Project path under XAMPP htdocs, e.g.  
  `C:\xampp\htdocs\beno\StJoseph_Nzuki`  
  (the Vite proxy expects this URL shape; change it in `frontend/nuxt.config.ts` if your path differs)

## 1. Database

1. Start **Apache** and **MySQL** in XAMPP.
2. Open phpMyAdmin → create a database named `stjoseph_db` (utf8mb4).

## 2. Backend (API)

```bash
cd backend
composer install
```

Create `backend/.env` (or copy from an existing local `.env` / `env.production.example` and adjust). Minimum local settings:

```ini
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost/beno/StJoseph_Nzuki/backend/'

database.default.hostname = localhost
database.default.database = stjoseph_db
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.port = 3306

JWT_SECRET = change-me-to-a-long-random-string
JWT_ACCESS_TTL = 604800
JWT_REFRESH_TTL = 604800
```

Optional for media uploads: set `CLOUDINARY_*` values in `.env`.

Run migrations and seed data:

```bash
cd backend
php spark migrate
php spark db:seed DatabaseSeeder
```

API base URL (browser / Postman):

`http://localhost/beno/StJoseph_Nzuki/backend/public/`

If Apache does not resolve `/backend` into `public/`, use:

`http://localhost/beno/StJoseph_Nzuki/backend/public/index.php`

### Default admin (after seeding)

| Field    | Value              |
|----------|--------------------|
| Email    | `tssnzuki@gmail.com` |
| Password | `admin@@nzuki2026` |

Change this password after first login on shared or production environments.

## 3. Frontend (Nuxt)

```bash
cd frontend
cp .env.example .env
npm install
npm run dev
```

Open: [http://localhost:3000](http://localhost:3000)

Local `.env` (from `.env.example`):

```env
NUXT_PUBLIC_API_BASE=http://localhost:3000
NUXT_PUBLIC_DEFAULT_LOCALE=en
```

In development, Nuxt proxies `/api` and `/uploads` to the XAMPP backend (see `vite.server.proxy` in `frontend/nuxt.config.ts`). Keep Apache running while you use `npm run dev`.

### Production-style static build

```bash
cd frontend
# Uses frontend/.env.production (API host for live site)
npm run generate
```

Output is in `frontend/.output/public` (or the configured generate directory).

## Quick start checklist

1. XAMPP Apache + MySQL on  
2. Database `stjoseph_db` created  
3. `composer install` + `.env` in `backend/`  
4. `php spark migrate` and `php spark db:seed DatabaseSeeder`  
5. `npm install` + `npm run dev` in `frontend/`  
6. Visit `http://localhost:3000` and log in with the seeded admin  

## Useful commands

| Where     | Command                         | Purpose                |
|-----------|---------------------------------|------------------------|
| backend   | `php spark migrate`             | Apply DB migrations    |
| backend   | `php spark db:seed DatabaseSeeder` | Seed admin + content |
| frontend  | `npm run dev`                   | Local UI (port 3000)   |
| frontend  | `npm run generate`              | Static production build|

## Notes

- Do not commit `.env` files, `node_modules/`, or `vendor/` (already gitignored).
- Do not commit large archives (`*.zip`) or accidental media dumps.
- Backend writable folders must be writable by Apache: `backend/writable/`.
