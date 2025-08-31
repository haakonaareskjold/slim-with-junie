Slim API usage guide (Ubuntu 24.04 / WSL / PhpStorm HTTP Client)

Prerequisites
- Ubuntu 24.04 (native) or WSL with bash shell
- PHP 8.3 or higher available on PATH
- Composer installed

Start the API
- Install dependencies:
```bash
composer install
```
- Start the built-in PHP server on http://localhost:8000:
```bash
composer start
```
(This runs: php -S localhost:8000 -t public)

Ubuntu 24.04 notes
- Install PHP 8.3 if missing: `sudo apt update && sudo apt install php8.3 php8.3-sqlite3 php8.3-cli`
- Ensure SQLite extension is enabled (php -m | grep sqlite3 should list it). If not, install `php8.3-sqlite3` and restart your shell.

WSL note
- If you run this inside WSL, open your browser in Windows at http://localhost:8000 (port is forwarded).
- Alternatively, use curl from within WSL as shown below.

Base URL
- http://localhost:8000

Content type
- Send and expect JSON. Include header:
  Content-Type: application/json

Using JetBrains PhpStorm HTTP Client
- You can create a `.http` file and run requests directly in PhpStorm.
- Quick start: create docs/api.http (or any .http file), paste the block below, and click the run gutter icons.

Example docs/api.http
```
### Environment
@baseUrl = http://localhost:8000
@json = application/json

### Health
GET {{baseUrl}}/health
Accept: application/json

### List todos
GET {{baseUrl}}/todos
Accept: {{json}}

### Get todo by id
GET {{baseUrl}}/todos/1
Accept: {{json}}

### Create todo
POST {{baseUrl}}/todos
Content-Type: {{json}}
Accept: {{json}}

{
  "title": "Write docs",
  "done": false
}

### Update todo (title)
PUT {{baseUrl}}/todos/1
Content-Type: {{json}}
Accept: {{json}}

{
  "title": "Learn Slim 4"
}

### Update todo (toggle done)
PUT {{baseUrl}}/todos/1
Content-Type: {{json}}
Accept: {{json}}

{
  "done": true
}

### Delete todo
DELETE {{baseUrl}}/todos/1
Accept: {{json}}
```

Health check
- Request (curl):
```bash
curl -i http://localhost:8000/health
```
- Response 200 OK:
```json
{"status":"ok","time":"2025-01-01T12:34:56+00:00"}
```

Todos resource
1) List todos
- Request (curl):
```bash
curl -i http://localhost:8000/todos
```
- Response 200 OK (example):
```json
[{"id":1,"title":"Learn Slim","done":false}, {"id":2,"title":"Build API","done":false}]
```

2) Get a todo by id
- Request (curl):
```bash
curl -i http://localhost:8000/todos/1
```
- Response 200 OK (example):
```json
{"id":1,"title":"Learn Slim","done":false}
```
- Not found:
```bash
curl -i http://localhost:8000/todos/999
```
-> 404 with body {"error":"Not Found"}

3) Create a todo
- Request (curl):
```bash
curl -i -X POST http://localhost:8000/todos \
  -H "Content-Type: application/json" \
  -d '{"title":"Write docs","done":false}'
```
- Response 201 Created (example):
```json
{"id":3,"title":"Write docs","done":false}
```
- Validation error (missing title): 422 with body {"error":"title is required"}

4) Update a todo (full or partial)
- Request (change title, curl):
```bash
curl -i -X PUT http://localhost:8000/todos/1 \
  -H "Content-Type: application/json" \
  -d '{"title":"Learn Slim 4"}'
```
- Request (toggle done, curl):
```bash
curl -i -X PUT http://localhost:8000/todos/1 \
  -H "Content-Type: application/json" \
  -d '{"done":true}'
```
- Response 200 OK (example):
```json
{"id":1,"title":"Learn Slim 4","done":true}
```
- Not found: 404 {"error":"Not Found"}

5) Delete a todo
- Request (curl):
```bash
curl -i -X DELETE http://localhost:8000/todos/1
```
- Response 204 No Content
- Not found: 404 {"error":"Not Found"}


Notes
- Todos are persisted in SQLite at storage/database.sqlite. The file and schema are created automatically on first run. To reset data, stop the server and delete the file.
- Dependencies are configured in src/Config/dependencies.php; routes in src/Routes/routes.php.
- Tip: In PhpStorm, you can set the `@baseUrl` variable at the top of your .http file to switch between environments easily.
