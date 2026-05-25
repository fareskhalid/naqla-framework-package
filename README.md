# Naqla Sehia PHP Micro Framework

A lightweight, modern PHP micro-framework designed for building web applications with clean architecture and minimal overhead.

## Features

- **Modern PHP 8.0+** with typed properties and return types
- **Routing** - Simple and clean route definition with GET/POST methods
- **Request/Response** - Elegant HTTP request and response handling
- **Validation** - Built-in validation system with extensible rules
- **Database Abstraction** - Support for MySQL and SQLite with query builder
- **Session Management** - Flash message support and session handling
- **View Rendering** - Template support with layout inheritance
- **Helper Functions** - Convenient global helper functions
- **Configuration Management** - Environment-based configuration system
- **PDO-based Database** - Safe prepared statement execution
- **Error Handling** - Proper exception handling and HTTP status codes

## Installation

Install via Composer:

```bash
composer require naqla-sehia/naqla-framework
```

## Quick Start

### Project Structure

Your project should follow this structure:

```
app/
├── Models/
│   └── User.php
├── Controllers/
│   └── UserController.php
routes/
├── web.php
public/
├── index.php
├── css/
└── js/
database/
├── migrations/
└── database.sqlite
config/
├── app.php
├── database.php
└── mail.php
views/
├── layouts/
│   └── main.php
├── dashboard.php
└── errors/
    └── 404.php
vendor/
.env
```

### Setup .env

Create a `.env` file in your project root:

```env
DB_DRIVER=mysql
DB_HOST=localhost
DB_USERNAME=root
DB_PASSWORD=
DB_DATABASE=naqla_framework

APP_NAME=MyApp
APP_DEBUG=true
```

### Create Entry Point (public/index.php)

```php
<?php

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
(new \Dotenv\Dotenv(__DIR__ . '/../'))->load();

// Load routes
require __DIR__ . '/../routes/web.php';

// Run application
app()->run();
```

### Define Routes (routes/web.php)

```php
<?php

use NaqlaSehia\Http\Route;
use App\Controllers\HomeController;

// Closure route
Route::get('/', function() {
    view('dashboard', ['name' => 'World']);
});

// Controller route
Route::post('/users', [UserController::class, 'store']);

// GET route
Route::get('/users', [UserController::class, 'index']);
```

### Create a Controller

```php
<?php

namespace App\Controllers;

use NaqlaSehia\Http\Request;

class UserController
{
    public function index()
    {
        $users = app()->db->read();
        view('users.index', ['users' => $users]);
    }

    public function store()
    {
        $request = request();
        
        $validator = validator()
            ->setRules([
                'name' => 'required|alnum',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|between:6,20'
            ])
            ->setAliases(['email' => 'Email Address'])
            ->make($request->all());

        if ($validator->fails()) {
            return back();
        }

        app()->db->create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password'))
        ]);

        session()->setFlash('success', 'User created successfully');
        return back();
    }
}
```

## Core Components

### Request

Handle incoming HTTP requests:

```php
$request = request();

// Get all input
$data = $request->all();

// Get specific input
$email = $request->input('email');
$email = $request->get('email', 'default@example.com');

// Check if input exists
if ($request->has('email')) {
    // ...
}

// Get specific fields only
$fields = $request->only(['name', 'email']);

// Get all except specific fields
$fields = $request->except(['password']);

// Get request method
$method = $request->method(); // 'get', 'post', etc.

// Check request method
if ($request->isMethod('post')) {
    // ...
}

// Get path
$path = $request->path();
```

### Response

Control outgoing HTTP responses:

```php
$response = app()->response;

// Set HTTP status code
$response->setStatusCode(200);
$response->setStatusCode(Response::HTTP_NOT_FOUND); // 404

// Redirect
$response->redirect('/dashboard');
$response->back();

// JSON response
$response->json(['success' => true]);

// Set headers
$response->setHeader('X-Custom-Header', 'value');

// Method chaining
$response->setStatusCode(201)->json(['id' => 1]);
```

### Database

Work with MySQL and SQLite:

```php
$db = app()->db;

// Create
$db->create([
    'name' => 'John Doe',
    'email' => 'john@example.com'
]);

// Read with filter
$users = $db->read('*', ['email', '=', 'john@example.com']);
$users = $db->read(['name', 'email']);

// Update
$db->update(1, [
    'name' => 'Jane Doe'
]);

// Delete
$db->delete(1);

// Raw query
$results = $db->raw('SELECT * FROM users WHERE age > ?', [18]);
```

### Validation

Validate input data:

```php
$validator = validator()
    ->setRules([
        'name' => 'required|alnum',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|between:6,20',
        'password_confirmation' => 'required|confirmed'
    ])
    ->setAliases([
        'email' => 'Email Address',
        'password' => 'Password'
    ])
    ->make(request()->all());

if ($validator->fails()) {
    $errors = $validator->errors();
    $nameErrors = $validator->errors('name');
}

if ($validator->passes()) {
    // Process valid data
}
```

**Available Rules:**
- `required` - Field is required
- `email` - Valid email format
- `alnum` - Alphanumeric characters only
- `between:min,max` - String length between min and max
- `max:length` - Maximum string length
- `confirmed` - Field value matches field_confirmation
- `unique:table,column` - Value is unique in table

### View

Render views with template inheritance:

```php
// Simple view
view('dashboard');

// With parameters
view('dashboard', [
    'user' => $user,
    'posts' => $posts
]);

// View in subdirectory (uses dot notation)
view('posts.index', ['posts' => $posts]);
// Renders: views/posts/index.php
```

**Layout (views/layouts/main.php):**
```php
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $title ?? 'My App'; ?></title>
</head>
<body>
    {{content}}
</body>
</html>
```

**View (views/dashboard.php):**
```php
<h1>Welcome <?php echo $name; ?></h1>
<p>This content replaces {{content}} in the layout</p>
```

### Session

Manage user sessions:

```php
$session = app()->session;

// Set value
$session->set('user_id', 1);

// Get value
$userId = $session->get('user_id');

// Check existence
if ($session->exists('user_id')) {
    // ...
}

// Remove value
$session->remove('user_id');

// Flash messages (auto-removed after next request)
$session->setFlash('success', 'Operation completed');
$session->setFlash('error', 'Something went wrong');

// Get flash message
if ($session->hasFlash('success')) {
    $message = $session->getFlash('success');
}
```

### Configuration

Access configuration values:

```php
// Get all config
$allConfig = config();

// Get specific config
$dbDriver = config('database.driver');
$appName = config('app.name', 'Default Name');

// Check config exists
if (config()->has('app.debug')) {
    // ...
}

// Set config
config('app.debug', false);
config(['app.name' => 'MyApp']);
```

### Helper Functions

Convenient global functions:

```php
// Environment variables
$debug = env('APP_DEBUG', false);

// Application instance
$app = app();

// Request instance
$request = request('name'); // Get 'name' input
$request = request(['name', 'email']); // Get only these fields

// Config helper
$name = config('app.name');

// View helper
view('dashboard', ['data' => $data]);

// Validation
$validator = validator();

// Hashing
$hash = bcrypt('password');

// Response
back(); // Redirect back

// Paths
base_path();       // Project root
config_path();     // config/ directory
database_path();   // database/ directory
public_path();     // public/ directory
view_path();       // views/ directory

// Other utilities
class_basename($class); // Get class name without namespace
value($value);          // Call if closure, otherwise return value
```

## Built-in Support Classes

### Arr (Array Helper)

```php
use NaqlaSehia\Support\Arr;

// Get value using dot notation
$value = Arr::get($array, 'user.name', 'default');

// Set value using dot notation
Arr::set($array, 'user.name', 'John');

// Check if key exists
Arr::has($array, 'user.email');

// Get only specific keys
$subset = Arr::only($array, ['name', 'email']);

// Get all except specific keys
$subset = Arr::except($array, ['password']);

// Add value if key doesn't exist
Arr::add($array, 'role', 'user');

// Remove keys
Arr::forget($array, ['password', 'token']);

// Flatten array
$flat = Arr::flatten($multidimensional);

// Get first/last element
$first = Arr::first($array);
$last = Arr::last($array);
```

### Str (String Helper)

```php
use NaqlaSehia\Support\Str;

// Convert to lowercase
$lower = Str::lower('HELLO');

// Pluralization
$plural = Str::plural('user');    // 'users'
$singular = Str::singular('users'); // 'user'

// Plural if condition
$text = Str::plural_if(5, 'user'); // '5 users'
```

### Hash (Security Helper)

```php
use NaqlaSehia\Support\Hash;

// Hash password with bcrypt
$hashed = Hash::make('password');

// Verify password
if (Hash::verify('password', $hashed)) {
    // Valid password
}

// Quick hash (not for passwords)
$hash = Hash::hash('data');
```

## Error Handling

The framework automatically handles errors:

```php
// 404 errors
// If route not found, displays views/errors/404.php

// HTTP Status Codes
$response->setStatusCode(Response::HTTP_NOT_FOUND);
$response->setStatusCode(Response::HTTP_INTERNAL_ERROR);
$response->setStatusCode(Response::HTTP_UNAUTHORIZED);
```

## Database Configuration

### MySQL (config/database.php)

```php
return [
    'driver' => env('DB_DRIVER', 'mysql'),
    'host' => env('DB_HOST', 'localhost'),
    'database' => env('DB_DATABASE'),
    'username' => env('DB_USERNAME'),
    'password' => env('DB_PASSWORD'),
];
```

### SQLite (config/database.php)

```php
return [
    'driver' => env('DB_DRIVER', 'sqlite'),
    'database' => database_path() . 'database.sqlite',
];
```

## Extending the Framework

### Create Custom Validation Rules

```php
<?php

namespace App\Validation\Rules;

use NaqlaSehia\Validation\Rules\Contract\Rule;

class PhoneRule implements Rule
{
    public function apply($field, $value, $data = [])
    {
        return preg_match('/^[0-9]{10,15}$/', $value);
    }

    public function __toString()
    {
        return '%s must be a valid phone number';
    }
}
```

Then register in `RulesMapper`:

```php
protected static array $map = [
    'phone' => App\Validation\Rules\PhoneRule::class,
    // ... other rules
];
```

## Best Practices

1. **Use Type Hints** - Always type hint properties and parameters
2. **Validate Input** - Always validate user input before processing
3. **Use Prepared Statements** - The framework uses PDO prepared statements
4. **Environment Configuration** - Keep sensitive data in .env files
5. **Separate Concerns** - Keep controllers thin, logic in services
6. **Use Helper Functions** - They provide a clean, fluent API
7. **Handle Errors Gracefully** - Return appropriate HTTP status codes
8. **Use Sessions** - Store user state securely in sessions

## Requirements

- PHP 8.0 or higher
- PDO extension
- OpenSSL extension (for password hashing)

## Dependencies

- `phpmailer/phpmailer` - Email handling
- `symfony/var-dumper` - Debugging utilities
- `vlucas/phpdotenv` - Environment variable loading

## License

MIT License - Free to use in commercial and private projects.

## Support

For issues, questions, or contributions, please visit the project repository.

## Developed By

**Fares Khalid** - Software Engineer

---

**Inspired by:** SecTheater YouTube Channel Tutorial

Get started building amazing web applications with Naqla Sehia Framework today!

