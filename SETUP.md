# Lodge POS - Setup Instructions

## 🚀 Quick Start

### 1. Install Dependencies

```bash
# PHP Dependencies
composer install

# Node.js Dependencies
npm install
```

### 2. Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Edit .env with your database settings
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lodge_pos
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Setup Database

```bash
# Run migrations
php artisan migrate

# Seed with demo data
php artisan db:seed
```

### 4. Build & Run

#### Development Mode (Hot Reload)
```bash
# Option 1: Use the dev script
./scripts/dev.sh

# Option 2: Manual
npm run dev
php artisan serve --port=8000
```

#### Production Build
```bash
# Build everything
./scripts/build.sh

# Start production server
php artisan serve --port=8000 --host=0.0.0.0
```

## 📁 Project Structure

```
lodge-pos/
├── app/                     # Laravel application code
├── bootstrap/              # Laravel bootstrap files
├── config/                 # Configuration files
├── database/               # Migrations and seeders
├── public/                 # Public web root
│   ├── assets/            # Built React assets
│   └── index.php          # Laravel entry point
├── resources/
│   ├── views/             # Blade templates
│   └── spa/               # React source code
│       └── src/
│           ├── components/
│           ├── contexts/
│           ├── lib/
│           └── pages/
├── routes/                 # Route definitions
├── scripts/               # Build and deployment scripts
├── storage/               # Logs, cache, uploads
└── vendor/                # Composer dependencies
```

## 🔧 Available Scripts

- `npm run dev` - Start React dev server with hot reload
- `npm run build` - Build React assets for production
- `npm run start` - Start both React dev server and Laravel
- `./scripts/build.sh` - Full production build
- `./scripts/dev.sh` - Development mode with both servers

## 🌐 Access

- **Application**: http://localhost:8000
- **API**: http://localhost:8000/api
- **React Dev Server**: http://localhost:5173 (when using npm run dev)

## 👥 Demo Users

| Role | Email | Password |
|------|-------|----------|
| Owner | owner@lodge.com | password123 |
| Admin | admin@lodge.com | password123 |
| Manager | manager@lodge.com | password123 |
| Receptionist | reception@lodge.com | password123 |
| Facility | facility@lodge.com | password123 |

## 🏨 Features

- **Dashboard** - Overview with stats and recent activity
- **Bookings** - Manage reservations and check-ins/outs
- **Rooms** - Room management and status tracking
- **Staff** - Employee management and attendance
- **Billing** - Invoices, payments, and financial reports
- **Inventory** - Supply tracking and stock management
- **Reports** - Analytics and exportable reports
- **Tasks** - Maintenance and housekeeping tasks

## 🛠 Tech Stack

- **Backend**: Laravel 12 (PHP 8.2)
- **Frontend**: React 18 + TypeScript + Tailwind CSS
- **Database**: MySQL
- **Build Tool**: Vite
- **Auth**: Custom token-based authentication

## 📝 Notes

- The React SPA is served by Laravel through the `/` route
- All API routes are prefixed with `/api`
- Frontend assets are built to `public/assets/`
- The application uses a single unified codebase for both frontend and backend
