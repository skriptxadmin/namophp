# 🧩 NAMO PHP — Lightweight PHP API Framework

**NAMO PHP** is a lightweight PHP micro-framework built on [Slim 4](https://www.slimframework.com/), designed to simplify the development of secure, maintainable, and scalable web applications and REST APIs.

It integrates essential backend components such as JWT authentication, request validation, middleware, logging, and database operations through Medoo. It also includes a JavaScript and CSS asset build workflow powered by npm and esbuild.

NAMO PHP provides a clean foundation for building PHP applications with practical examples, reusable components, and a straightforward development workflow.

---

## ✨ Features

- 🔐 **JWT Authentication** — Token-based authentication for API endpoints.
- 🧰 **Request Validation** — Input validation powered by Rakit Validation.
- ⚙️ **Middleware Support** — Authentication, CORS, and other request-processing middleware.
- 🗄️ **Database Operations** — Simplified database queries using Medoo and PDO.
- 📝 **Application Logging** — Logging and debugging with Monolog.
- 🧭 **Flexible Routing** — Route definitions, route groups, and dynamic URL parameters.
- 🎨 **Asset Management** — JavaScript and CSS bundling using esbuild.
- 📦 **Library Bundling** — Centralized configuration for frontend libraries.
- 🧩 **Smarty Integration** — Convenient template helpers for loading static assets.
- 📚 **Example Implementations** — Sample code to help developers get started quickly.

---

## 🚀 Getting Started

### Prerequisites

Make sure the following tools are installed on your development machine:

- PHP and Composer
- Node.js and npm
- Git
- A supported database, such as MySQL, if required by your application

### 1. Clone the repository

```bash
git clone https://github.com/skriptxadmin/namophp.git

cd namophp
```

### 2. Install PHP dependencies

```bash
composer update
```

This resolves and installs the PHP dependencies defined in `composer.json`.

For a reproducible installation using an existing `composer.lock` file, use `composer install` instead.

### 3. Install JavaScript dependencies

```bash
npm install
```

This installs the frontend build tools and JavaScript libraries defined in `package.json`.

### 4. Configure the environment

Copy the example environment file:

```bash
cp .env.example .env
```

Update `.env` with your application configuration, including the environment, database connection, and JWT secret where applicable.

Example:

```dotenv
APP_ENV=local
```

Use the appropriate environment setting for development or production according to your application's configuration.

**Important:** Keep production secrets out of version control.

### 5. Build frontend libraries

NAMO PHP uses `esbuild-libs.conf.js` to configure the frontend libraries that need to be bundled.

To add or modify libraries, update this configuration file and run:

```bash
npm run esbuild:libs
```

This builds the configured libraries for use by your application.

### 6. Start development mode

```bash
npm run dev
```

Use this command to run the configured frontend development workflow.

### 7. Build for production

```bash
npm run build
```

Use the production build command to generate optimized frontend assets for deployment.

### 8. Start the PHP development server

```bash
php -S localhost:9999 -t public
```

Open the application at:

http://localhost:9999

This built-in PHP server is intended for local development and testing, not production hosting.

---

## 📁 Static Assets

Place publicly accessible static files inside the `public` directory.

### Loading assets in HTML templates

Use the configured application URL when referencing assets:

```html
<link rel="stylesheet" href="{$url}/styles/app.css">
```

### Loading assets using Smarty helpers

NAMO PHP provides convenient template helpers for including CSS and JavaScript files:

```smarty
{css file="styles/app.css"}
{js file="scripts/app.js"}
```

Ensure that the referenced files exist in the public assets directory and that the helper configuration matches your project setup.

---

## 🧭 Routing

NAMO PHP uses Slim 4 routing. Refer to the provided examples for route registration, route groups, middleware, and controller organization.

### Dynamic routes

Define a route with a dynamic `slug` parameter:

```php
$group->get(
    '/{slug}',
    [App\Controllers\Roles\IndexController::class, 'get']
)->setName('roles.get');
```

### Accessing route parameters in a controller

Slim passes route parameters through the `$args` array.

```php
public function get(
    Request $request,
    Response $response,
    array $args
): Response {

    $slug = $args['slug'];

    $row = $this->db->get(
        'roles',
        '*',
        ['slug' => $slug]
    );

    return $this->json($row);
}
```

Refer to the included examples for additional routing patterns and controller implementations.

---

## 🧰 Request Validation

NAMO PHP integrates [Rakit Validation](https://github.com/rakit/validation) through its validator helper.

### Example: Validate a PIN

```php
$validator = new \App\Helpers\Validator();

$data = $request->getParsedBody();

$rules = [
    'pin' => 'required|min:6|max:6|regex:/^[1-9][0-9]*$/',
];

$messages = [];

$validationResult = $validator->make(
    $data,
    $rules,
    $messages
);

if ($validationResult !== true) {
    return $this->json([
        'errors' => $validationResult
    ], 422);
}

$validData = $validator->validData;
```

The example validates a six-digit PIN that cannot begin with zero. When validation fails, the API returns a `422 Unprocessable Entity` response.

Use `$validData` to work with the validated input rather than trusting the original request data.

---

## 🔐 Accessing the Authenticated User

When authentication middleware attaches the user ID to the request, retrieve it in your controller using:

```php
$user_id = $request->getAttribute('user_id');
```

This assumes the authentication middleware has already validated the request and populated the `uid` attribute.

Refer to the authentication examples for the complete JWT and middleware workflow.

---

## 🗄️ Database Operations with Medoo

NAMO PHP uses [Medoo](https://medoo.in/) to simplify database interactions.

The following examples demonstrate the database methods used in the framework.

### Select records

```php
// Select all columns
$rows = $this->db->select('table-name', '*');

// Select specific columns
$rows = $this->db->select(
    'table-name',
    ['id', 'name']
);

// Select a single column
$rows = $this->db->select('table-name', 'id');

// Select a column with a condition
$rows = $this->db->select(
    'table-name',
    'id',
    ['id' => 3]
);
```

### Retrieve a single record or value

```php
$row = $this->db->get(
    'table-name',
    'id',
    ['id' => 3]
);
```

### Insert, update, and delete

```php
// Insert a record
$result = $this->db->insert(
    'table-name',
    $data
);

// Update matching records
$result = $this->db->update(
    'table-name',
    $data,
    $where
);

// Delete matching records
$result = $this->db->delete(
    'table-name',
    $where
);
```

Replace `table-name`, `$data`, and `$where` with the appropriate values for your application.

---

## 📚 Examples and Reference

NAMO PHP includes examples demonstrating common application development patterns.

Use the provided examples as the primary reference for:

- User registration and login using JWT
- Protected API routes and authenticated user access
- Request validation and error responses
- Middleware configuration
- Database queries using Medoo
- Dynamic routes and controller methods
- Application asset loading and frontend library bundling

Review the existing project structure before adding new controllers, routes, middleware, or helpers so that your implementation follows the framework's conventions.

---

## 🧱 Built With

| Technology | Purpose |
|---|---|
| [Slim 4](https://www.slimframework.com/) | PHP routing and HTTP request handling |
| [Rakit Validation](https://github.com/rakit/validation) | Input validation |
| [Firebase PHP-JWT](https://github.com/firebase/php-jwt) | JWT authentication |
| [Monolog](https://github.com/Seldaek/monolog) | Application logging |
| [Medoo](https://medoo.in/) | Database abstraction |
| [Smarty](https://www.smarty.net/) | Template rendering |
| [Node.js](https://nodejs.org/) and npm | Frontend tooling and dependency management |
| [esbuild](https://esbuild.github.io/) | JavaScript and CSS asset bundling |

---

## 🤝 Contributing

Contributions, bug reports, and suggestions are welcome.

For significant changes, please open an issue or discuss the proposed implementation before submitting a pull request.

---

## 📄 License

This project is distributed under the MIT License, subject to the terms specified in the repository's `LICENSE` file.

---

## 👤 Author

**Alaksandar Jesus Gene AMS**

Entrepreneur and Software Developer

Website: [skriptx.com](https://skriptx.com)

GitHub: [skriptxadmin](https://github.com/skriptxadmin)

---

**NAMO PHP — A practical foundation for building PHP applications and APIs.**
