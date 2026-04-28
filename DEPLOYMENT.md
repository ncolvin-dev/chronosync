# ChronoSync — Deployment & Developer Guide

## Overview

ChronoSync is a Laravel 12 / PHP 8.2 volunteer coordination platform backed by PostgreSQL. It manages volunteers, facilities, recurring meeting schedules, credential tracking, SMS notifications, and automated volunteer matching.

---

## Tech Stack

- **Backend**: Laravel 12, PHP 8.2
- **Database**: PostgreSQL
- **Frontend**: Blade templates, Tailwind CSS, Vite
- **SMS**: Twilio (configurable, also supports AWS SNS and custom providers)
- **Auth**: Session-based with role-based access control (RBAC)
- **Primary Keys**: ULIDs throughout

---

## Team Branching Workflow

1. **Always pull from `main` before starting any new work**
2. **Do your development work on your own feature branch** (e.g., `chris`, `nick`, `sara`)
3. **Before pushing, merge `main` into your branch** to stay current with the team
4. **Push to your feature branch** — never push directly to `main`
5. **Open a Pull Request** on GitHub from your branch → `main` for team review
6. **After PR is merged**, update your local `main` with `git pull origin main`

### Daily Development Routine

```bash
# Start of day — get latest from main
git checkout main
git pull origin main

# Switch to your branch and merge in main's latest
git checkout your-branch
git merge main

# Do your work, then push
git add .
git commit -m "Your commit message"
git push origin your-branch
```

---

## Local Development Setup

### First-Time Setup (new machine)

```bash
# 1. Clone the repo
git clone https://github.com/ncolvin-dev/chronosync.git
cd chronosync

# 2. Install PHP dependencies
composer install

# 3. Install Node dependencies
npm install

# 4. Set up environment file
cp .env.example .env
php artisan key:generate

# 5. Configure .env (see Environment Variables section below)

# 6. Run database migrations
php artisan migrate

# 7. Seed test data
php artisan db:seed

# 8. Start the dev servers (two terminals)
php artisan serve       # Terminal 1 — Laravel backend
npm run dev             # Terminal 2 — Vite/Tailwind frontend
```

### Updating an Existing Local Setup

```bash
git pull origin main
composer install
npm install
php artisan migrate
# Restart php artisan serve and npm run dev
```

### Resetting to a Clean Database

```bash
php artisan migrate:fresh --seed
```

---

## Environment Variables

Key `.env` settings for local development:

```env
APP_NAME=ChronoSync
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=chronosync_local
DB_USERNAME=your_pg_username
DB_PASSWORD=your_pg_password

# SMS (leave blank to disable SMS in local dev)
SMS_PROVIDER=twilio
TWILIO_ACCOUNT_SID=
TWILIO_AUTH_TOKEN=
TWILIO_FROM_NUMBER=
```

---

## Test Accounts

All accounts below are created by `php artisan db:seed`. Use `php artisan migrate:fresh --seed` to reset the database to this state at any time.

### Admin

| Name | Email | Password | Roles |
|------|-------|----------|-------|
| Admin | admin@example.com | `AdminPass123!` | admin, coordinator |

### Coordinators

| Name | Email | Password | Roles |
|------|-------|----------|-------|
| Coordinator | coord@example.com | `CoordPass123!` | coordinator |
| Kevin Harris | kevin.harris@example.com | `password123` | coordinator |
| Nicole Clark | nicole.clark@example.com | `password123` | coordinator |
| Brian Lewis | brian.lewis@example.com | `password123` | coordinator |
| Rachel Walker | rachel.walker@example.com | `password123` | coordinator |

### Dual-Role Users (Volunteer + Coordinator)

| Name | Email | Password | Roles |
|------|-------|----------|-------|
| Steven Young | steven.young@example.com | `password123` | volunteer, coordinator |
| Stephanie King | stephanie.king@example.com | `password123` | volunteer, coordinator |

### Volunteers

| Name | Email | Password |
|------|-------|----------|
| John Smith | john@example.com | `SecurePass123!` |
| Sarah Johnson | sarah@example.com | `password123` |
| Marcus Williams | marcus@example.com | `password123` |
| Emily Chen | emily@example.com | `password123` |
| Robert Davis | robert@example.com | `password123` |
| Lisa Martinez | lisa@example.com | `password123` |
| James Wilson | james@example.com | `password123` |
| Amanda Thompson | amanda@example.com | `password123` |
| Carlos Garcia | carlos@example.com | `password123` |
| Patricia Brown | patricia@example.com | `password123` |
| David Anderson | david.anderson@example.com | `password123` |
| Jennifer White | jennifer.white@example.com | `password123` |
| Christopher Miller | christopher.miller@example.com | `password123` |
| Michelle Taylor | michelle.taylor@example.com | `password123` |
| Daniel Thomas | daniel.thomas@example.com | `password123` |
| Jessica Moore | jessica.moore@example.com | `password123` |
| Matthew Jackson | matthew.jackson@example.com | `password123` |
| Ashley Martin | ashley.martin@example.com | `password123` |
| Ryan Lee | ryan.lee@example.com | `password123` |
| Lauren Perez | lauren.perez@example.com | `password123` |

---

## Implemented Features

The following features are fully implemented and available for testing:

**Volunteer Management**
- Create, view, edit, and deactivate volunteers
- Credential tracking per volunteer (expiration alerts)
- Availability scheduling (days/times a volunteer is available)
- Soft delete (deactivate rather than permanently remove)

**Facility Management**
- Create, view, edit, toggle status, and delete facilities
- Multiple recurring meeting slots per facility
- Search and filter facilities by name, city, and status

**Recurring Meeting System**
- Meetings defined by pattern (e.g., "Every 1st Tuesday at 7:00 PM")
- Up to 5 volunteers assignable per meeting occurrence
- Per-occurrence assignment tracking with status workflow: pending → confirmed / declined / cancelled
- Deactivate/reactivate individual meeting slots
- Automatic next-occurrence calculation

**Volunteer Matching**
- Matching algorithm scores volunteers by availability, credentials, and proximity
- Auto-assign and suggestion modes via API
- Manual coordinator override with required reason field

**SMS Notifications**
- Configurable SMS provider (Twilio, AWS SNS, or custom)
- SMS sent on assignment and cancellation events
- SMS log for audit trail
- SMS configuration managed in admin settings

**Audit Logging**
- All create/update/delete actions logged with actor, entity, and change details

**Role-Based Access Control**
- Roles: `admin`, `coordinator`, `volunteer`, `viewer`
- Users may hold multiple roles simultaneously (e.g., a volunteer who is also a coordinator)
- `admin` inherits all coordinator capabilities plus system configuration access

---

## Known Limitations in Local Development

### SMS Sending
SMS messages will **not** send in local dev unless you add valid Twilio (or other provider) credentials to your `.env`. The application will not throw an error — it will silently skip sending. All SMS attempts are logged in the `sms_logs` table regardless.

### No Volunteer-Facing Portal
The current UI is coordinator/admin-only. Volunteers do not have a login portal to view their own assignments, confirm/decline via the web, or manage their own availability. Volunteer interactions (confirm/decline) are currently SMS-based only.

### Meeting Assignment UI
Assigning volunteers to specific meeting occurrences and managing the confirm/decline/cancel workflow is currently handled via API endpoints. A full coordinator UI for this workflow (viewing occurrence calendars, assigning from a list, bulk actions) is not yet built.

### Reports
A `ReportController` exists but report views are not yet implemented. Reporting on volunteer hours, facility coverage, and credential expirations is planned but incomplete.

### Dual-Role User Experience
Users with both `volunteer` and `coordinator` roles are fully supported in the data model. However, the UI does not yet present a role-switcher or differentiated dashboard view for dual-role users — they will see the coordinator dashboard by default.

### Edit Facility Meeting Slots
The "Add Facility" modal supports adding multiple meeting slots when creating a facility. However, editing the meeting slots of an *existing* facility (adding new slots, removing slots, or changing the schedule of existing slots) does not yet have a dedicated UI — changes must be made directly via the API or database.

### Matching Algorithm UI
The matching/auto-assign feature is exposed via API (`/api/meetings/{meeting}/auto-assign`) and is callable from the matching view, but it is not yet integrated into the main meeting management flow as a one-click action from within a facility's meeting list.

### No Email Notifications
All notifications are SMS-only. There is no email notification system for assignment confirmations, reminders, or credential expiration alerts.

### Credential Expiration Alerts
The credential expiration data is stored and viewable per volunteer, but there is no automated alert, dashboard widget, or scheduled job that proactively flags upcoming expirations to coordinators.

---

## Database Schema Overview

| Table | Purpose |
|-------|---------|
| users | System users (admins, coordinators) |
| volunteers | Volunteer profiles linked to users |
| facilities | Meeting locations |
| meetings | Recurring meeting slot definitions |
| meeting_assignments | Per-occurrence volunteer assignments |
| volunteer_credentials | Credential records per volunteer |
| credential_types | Credential type definitions |
| availability | Volunteer weekly availability windows |
| sms_logs | Log of all SMS send attempts |
| audit_logs | Full audit trail of all system actions |
| sms_config | SMS provider configuration |

---

## Running Tests

```bash
php artisan test
```

Test coverage includes: Auth, Volunteers, Facilities, Matching, and SMS flows.
