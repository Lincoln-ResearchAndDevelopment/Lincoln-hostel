# Plask Deployment Guide — Lincoln Hostel SMS

## Quick Start (Single Project)

### 1. Link Repository
1. Log in to [Plask](https://plask.ai) with your account
2. Go to **Dashboard → New Project**
3. Select **GitHub** as source
4. Connect your repo: `Lincoln-ResearchAndDevelopment/Lincoln-hostel`
5. Select branch: `local-backup-2026-07-17-production` (or your preferred branch)

### 2. Environment Variables
Set these in the Plask dashboard under **Settings → Environment Variables**:

```
APP_NAME=LincHostel
APP_ENV=production
APP_DEBUG=false
APP_KEY=          <-- Generate with: php artisan key:generate --show
APP_URL=https://your-app-name.plask.app
APP_TIMEZONE=Africa/Lagos

DB_CONNECTION=mysql
DB_HOST=          <-- Plask will provide this
DB_PORT=3306
DB_DATABASE=      <-- Plask will provide this
DB_USERNAME=      <-- Plask will provide this
DB_PASSWORD=      <-- Plask will provide this

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@lincoln.edu.ng
MAIL_FROM_NAME=LincHostel

SESSION_DRIVER=file
SESSION_SECURE_COOKIE=true
SESSION_LIFETIME=120

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
LOG_CHANNEL=daily
LOG_LEVEL=warning

FILESYSTEM_DISK=local

CORS_ALLOWED_ORIGINS=https://your-app-name.plask.app
SANCTUM_STATEFUL_DOMAINS=your-app-name.plask.app
SESSION_DOMAIN=.plask.app
```

### 3. Build Configuration
If Plask auto-detects, it will use the Dockerfile. If manual config is needed:
- **Build Command:** `composer install --no-dev --optimize-autoloader && npm ci && npm run build`
- **Start Command:** `php artisan serve --host=0.0.0.0 --port=$PORT`
- **OR** use the included Dockerfile + docker-compose.yml

### 4. Post-Deploy Commands (Run via Plask Console/SSH)
```bash
# Generate app key if not set
php artisan key:generate

# Run migrations
php artisan migrate --force

# Seed initial data (optional - only first deploy)
php artisan db:seed --force

# Create storage symlink
php artisan storage:link

# Cache routes, config, views
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Verify Deployment
```bash
# Check health endpoint
curl https://your-app-name.plask.app/api/health

# Expected response:
# {"status":"ok","app":"LincHostel","environment":"production","debug":false,"database":"ok"}
```

---

## Hosting Multiple Projects on One Plask Account

### Can I host multiple projects?
**Yes.** Plask allows multiple projects per account. Each project:
- Gets its own subdomain (e.g., `app1.plask.app`, `app2.plask.app`)
- Has its own environment variables
- Has its own database
- Can link to different repos or different branches of the same repo

### How to Add a Second Project
1. From Plask Dashboard, click **New Project**
2. Give it a unique name
3. Link to your second GitHub repo (e.g., `Tchris-Hub/lINCOLN_hostel_SMS`)
4. Configure environment variables independently
5. Deploy

### How to Deploy from a Specific Branch
1. When creating/editing a project in Plask
2. Under **Source/Branch**, select your branch:
   - `main` — stable production
   - `local-backup-2026-07-17-production` — latest production-ready
   - `local-backup-2026-07-17` — with UI changes (amount labels removed)
   - `chris-2.0v` — has newer features from Tchris-Hub
3. Each branch can be deployed as a separate Plask project for testing

### Branch Recommendations for Lincoln Hostel

| Branch | Repo | Purpose |
|--------|------|---------|
| `local-backup-2026-07-17-production` | Lincoln-Research | **(New)** Production-ready with security hardening |
| `main` | Lincoln-Research | Original stable (March 2026) |
| `chris-2.0v` | Lincoln-Research | New features from Tchris-Hub (May 2026) |
| `main` | Tchris-Hub | Latest development with bed management, email tracking |

---

## Important Security Notes

1. **Change all default passwords** after first deploy:
   - SuperAdmin: `superadmin@linchostel.com` / `SuperAdmin@2025`
   - Admin: `admin@linchostel.com` / `admin12345`
   - Student test: `STU1001` / `student12345`

2. **Enable 2FA** if Plask supports it for your account

3. **Set up backups** in Plask for the database

4. **Monitor logs** via Plask dashboard
