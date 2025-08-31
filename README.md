Slim 4 minimal API example (PHP 8.3) - testing Junie

Quick start
- Install dependencies: composer install
- Run dev server: composer start (serves http://localhost:8000)

Endpoints
- GET /health – health check
- GET /todos – list todos
- GET /todos/{id} – get todo by id
- POST /todos – create todo (JSON: {"title": string, "done": bool})
- PUT /todos/{id} – update todo
- DELETE /todos/{id} – delete todo

How to use the API
- See docs/USAGE.md for step-by-step examples with curl and the PhpStorm HTTP Client (.http files). On Ubuntu 24.04, install php8.3 and php8.3-sqlite3.

Notes
- Uses PHP-DI for container and Monolog for logging
- Dependencies are configured in src/Config/dependencies.php
- MVC-ish structure:
  - Controllers in src/Controllers
  - Models in src/Models
  - Services in src/Services
  - Routes in src/Routes/routes.php (loaded from Bootstrap)
- Todos are persisted with SQLite at storage/database.sqlite (auto-created). To reset, delete the file.
