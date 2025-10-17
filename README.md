<img width="827" height="770" alt="image" src="https://github.com/user-attachments/assets/65651103-1380-4aeb-a5cc-f62a71ceeb49" /><img width="827" height="770" alt="image" src="https://github.com/user-attachments/assets/589722c3-949a-4ebf-8ab7-d4ffac6d22d4" /># To-do List Web App

A Laravel 12 + Vite application that helps you manage daily tasks with powerful filtering, quick editing, and a calendar-first overview. The interface is crafted with Tailwind CSS 4 utilities to keep the experience modern and responsive.

## Key Features

- **Full task lifecycle management**: create, edit, mark complete, or delete tasks straight from the main list.
- **Filtering & search**: locate tasks by title/notes, filter by status (to do/done), and sort by creation time, due date, or priority.
- **Bulk actions**: select multiple tasks at once to delete them or toggle completion status in a single operation.
- **Productivity insights**: weekly and monthly statistics with progress indicators plus totals for active, completed, and pending tasks.
- **CSV export**: download the currently filtered task list in CSV format for reporting or backups.
- **Interactive calendar**: inspect monthly task distribution with priority breakdowns and weekly summaries.

## Technology Stack

- [Laravel 12](https://laravel.com) serving as the backend and Blade templating engine.
- [PHP ^8.2](https://www.php.net/) and Composer for dependency management.
- [Vite](https://vitejs.dev/) for modern asset bundling.
- [Tailwind CSS 4](https://tailwindcss.com/) as the styling framework.
- [SQLite](https://www.sqlite.org/) (default) or any database supported by Laravel.

## Prerequisites

Ensure the following tools are installed locally:

- PHP 8.2 or newer with Laravel's required extensions (openssl, pdo, mbstring, tokenizer, xml, ctype, json, fileinfo).
- Composer 2.
- Node.js 20 LTS and npm.
- SQLite (optional, if you are using the default database) or credentials for another database defined in `.env`.

## Installation Steps

```bash
# 1. Enter the application directory
cd todolist

# 2. Install PHP dependencies
composer install

# 3. Copy the environment file & generate the app key
cp .env.example .env
php artisan key:generate

# 4. Prepare the database
# For SQLite: create an empty file
mkdir -p database
touch database/database.sqlite
# or update the DB_* variables in .env for MySQL/PostgreSQL

# 5. Run the migrations
php artisan migrate

# 6. Install JavaScript dependencies
npm install
```

## Running the App Locally

Choose one of the following options:

1. **Unified script**
   ```bash
   # Runs the Laravel server, queue listener, and Vite together
   composer dev
   ```

2. **Manual setup** (for example, when you do not need the queue listener):
   ```bash
   # Terminal 1
   php artisan serve

   # Terminal 2
   npm run dev
   ```

The application is available at `http://localhost:8000` by default.

## Production Build

```bash
npm run build
```

Build artifacts are emitted to `public/build`. Consider running `php artisan config:cache` and `php artisan route:cache` during deployment when appropriate.

## Testing

```bash
php artisan test
```

The command clears cached configuration before executing the Pest-powered test suite.

## Key Directory Structure

- `app/Http/Controllers/TaskController.php` – business logic for task management (filters, sorting, export, calendar, etc.).
- `resources/views/tasks/index.blade.php` – task list view covering forms, summaries, and bulk actions.
- `resources/views/tasks/calendar.blade.php` – monthly calendar view with priority statistics and weekly breakdowns.
- `database/migrations/2024_01_01_000000_create_tasks_table.php` – schema definition for the `tasks` table.

## Screenshot Checklist for Documentation


## Contribution Guidelines

- Run `composer pint` to keep PHP styling consistent.
- Open an issue or pull request for significant changes. Include a clear description of the feature/bugfix and the testing steps performed.

## License

The project is distributed under the [MIT](LICENSE) license that ships with Laravel.
