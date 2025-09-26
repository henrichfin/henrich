# Professional Task Manager

A modern, professional task management web application built with PHP, Apache, and MySQL.

## Features

- ✅ Create, read, update, and delete tasks
- ✅ Task status management (Pending, In Progress, Done)
- ✅ Professional, responsive UI
- ✅ RESTful API
- ✅ MySQL database integration

## Tech Stack

- **Frontend:** HTML5, CSS3, Vanilla JavaScript
- **Backend:** PHP 8.2
- **Database:** MySQL 8.0
- **Server:** Apache 2.4

## Local Development

### Using XAMPP (Windows)
1. Start XAMPP services (Apache, MySQL)
2. Import `database/henrich.sql` to create database
3. Visit `http://localhost/henrich/`

### Using Docker
```bash
# Build and run with Docker Compose
docker-compose up -d

# Visit http://localhost
```

## Deployment

### Coolify Deployment
1. Connect your Git repository to Coolify
2. Coolify will automatically detect the Dockerfile
3. Set environment variables:
   - `DB_HOST`: Your MySQL host
   - `DB_NAME`: Database name (henrich)
   - `DB_USER`: Database username
   - `DB_PASSWORD`: Database password
4. Deploy!

## Environment Variables

- `DB_HOST`: MySQL host (default: localhost)
- `DB_NAME`: Database name (default: henrich)
- `DB_USER`: Database username (default: root)
- `DB_PASSWORD`: Database password

## API Endpoints

- `GET /backend/api/health` - Health check
- `GET /backend/api/tasks` - Get all tasks
- `POST /backend/api/tasks` - Create new task
- `PUT /backend/api/tasks/{id}` - Update task
- `DELETE /backend/api/tasks/{id}` - Delete task

## Project Structure

```
henrich/
├── backend/
│   └── api.php          # PHP API endpoints
├── database/
│   └── henrich.sql      # Database schema
├── config/
│   └── henrich.conf     # Apache configuration
├── index.html           # Main application
├── script.js           # Frontend JavaScript
├── style.css           # Frontend CSS
├── .htaccess           # URL rewriting
├── Dockerfile          # Docker configuration
└── docker-compose.yml  # Local development
```
