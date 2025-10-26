# AI Agent Instructions for prrahi-admin

This document provides essential knowledge for AI agents working with this Laravel-based admin panel codebase.

## Project Architecture

- **Type**: Laravel 10.x admin panel with Tailwind CSS and Alpine.js
- **Main Components**:
  - Authentication system with custom admin guard (`config/auth.php`)
  - Role-based access control (RBAC) with permissions
  - Export functionality using Maatwebsite/Excel
  - Frontend using Tailwind CSS and Alpine.js

## Key Workflows

### Development Setup
```bash
# Install dependencies
composer install
npm install

# Start development servers (either way works)
npm run hot  # Runs both PHP and Vite servers concurrently
# OR separately:
php artisan serve  # Terminal 1
npm run dev       # Terminal 2

# Production build
npm run build
```

### Database Patterns
- Models are in `app/Models/` with corresponding migrations in `database/migrations/`
- Follow established naming patterns for new features:
  ```bash
  php artisan make:model NewFeature -mcr  # Creates model, migration, controller
  ```

## Project-Specific Conventions

### Permission System
- Custom middleware `CheckPermission` for route-level access control
- Usage in routes:
  ```php
  Route::get('/path', [Controller::class, 'method'])
      ->middleware('permission:permission_name')
      ->name('route.name');
  ```

### Notification Pattern
- Uses Toastr for flash messages
- Controller pattern:
  ```php
  return redirect()->back()->with('toast', [
      'type' => 'success',
      'message' => 'Operation completed!'
  ]);
  ```

### Export Features
- Uses `Maatwebsite/Excel` for exports
- New exports should extend base export class in `app/Exports/`
- Generate with: `php artisan make:export EntityExport --model=Entity`

## Integration Points

1. **Frontend Stack**:
   - Vite for asset bundling
   - Tailwind CSS for styling
   - Alpine.js for interactive components
   
2. **External Dependencies**:
   - Excel exports via `maatwebsite/excel`
   - Authentication via Laravel Sanctum
   - Toastr for notifications

## Key Files/Directories

- `app/Http/Middleware/CheckPermission.php`: Custom permission handling
- `app/Models/`: Core business models
- `app/Exports/`: Excel export definitions
- `routes/web.php`: Main application routes
- `resources/views/`: Blade templates

## Common Patterns

1. **Model Creation**: Always use resource generators (-mcr flag) for consistency
2. **Authorization**: Use middleware('permission:x') on routes requiring authorization
3. **Flash Messages**: Always use the toast pattern for user feedback
4. **Excel Exports**: Follow the established export class pattern