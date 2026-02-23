# Att-Ch8

A highly configurable Laravel application built via Sail allowing users to automatically send authentication tokens and execute check-ins/actions across customizable third-party HR platforms based on local times.

## Prerequisites

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (or Docker Engine)
- [Git](https://git-scm.com/) (if cloning natively)

## Initial Local Setup

1. **Clone the repository** and navigate to the root directory.

2. **Install Composer Dependencies** (If doing this fresh without native PHP/Composer, use Docker):
   ```bash
   docker run --rm \
       -u "$(id -u):$(id -g)" \
       -v "$(pwd):/var/www/html" \
       -w /var/www/html \
       laravelsail/php83-composer:latest \
       composer install --ignore-platform-reqs
   ```

3. **Configure Environment File**
   ```bash
   cp .env.example .env
   ```

4. **Bring up Docker Containers** (starts MySQL, Redis, and Laravel locally):
   ```bash
   ./vendor/bin/sail up -d
   ```

5. **Generate the App Key**:
   ```bash
   ./vendor/bin/sail artisan key:generate
   ```

6. **Run Database Migrations and Seeders**:
   ```bash
   ./vendor/bin/sail artisan migrate:fresh --seed
   ```
   *Note: This creates a Super Admin user: `admin@autoattend.local` with password `password`.*

7. **Compile Frontend Assets**:
   ```bash
   ./vendor/bin/sail npm install
   ./vendor/bin/sail npm run build
   ```

## Configuring Mailgun Email Notifications

The application automatically emails users when their scheduled action successfully or unsuccessfully fires. Open your `.env` file and set the mailer configuration properties accordingly:

```env
MAIL_MAILER=mailgun
MAIL_FROM_ADDRESS="no-reply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"

MAILGUN_DOMAIN="your-mailgun-domain.com"
MAILGUN_SECRET="your-mailgun-api-key"
MAILGUN_ENDPOINT="api.mailgun.net"
```

## Running the Automated Action Scheduler

The application requires Laravel's task scheduler to run every minute in the background for processing target times down to the minute.
Keep this terminal window running:

```bash
./vendor/bin/sail artisan schedule:work
```

## Creating Additional Super Admins

Only `super_admin` role users can view the "Admin Panel" and configure generic Platform API endpoints. Either leverage the seeder mentioned above, or update the user's role in the database directly.
