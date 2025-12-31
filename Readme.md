Here's a sample `README.md` file for your **NAMO** PHP framework:

---

````markdown
# 🧩 NAMO - Lightweight PHP API Framework

**NAMO** is a lightweight and powerful PHP micro-framework built on top of [Slim 4](https://www.slimframework.com/) designed specifically for building robust, secure, and scalable APIs. It comes pre-integrated with essential features like **JWT authentication**, **input validation**, **middleware support**, **logging**, and a **PDO wrapper** for database operations — everything you need to get started with your next API project quickly.

---

## ✨ Features

- 🔐 **JWT Authentication** out of the box
- 🧰 **Validation** powered by Rakit\Validation
- ⚙️ **Middleware** for auth, CORS, etc.
- 📦 **PDO Wrapper** for clean and easy DB access, powered by Medoo
- 📝 **Monolog Logger** for debugging and application logs
- 🔄 Sample codes to help you bootstrap your API

---

## 🚀 Getting Started

### 1. Clone the repo

```bash
git clone https://github.com/skriptxadmin/namophp.git

cd NAMOPHP
```
````

### 2. Install dependencies via Composer

```bash
composer update
```

### 3. Setup Environment

Copy `.env.example` to `.env` and update the variables (DB, JWT secret, etc.)

```bash
cp .env.example .env
```

Update `.env`:

```env
APP_ENV=local   #production or local or dev
```

All static files need to be placed in public folder

Default way of linking css file

```html
<link rel="stylesheet" href="{$url}/styles/app.css">
```

```php
{css file="styles/app.css"}
{js file="scripts/app.js"}
```

---

## 📚 More Examples

- [x] User Registration & Login with JWT
- [x] Protected Profile Route
- [x] Form Validation Example
- [x] Middleware for Logging Requests

---

## 🧱 Built With

- [Slim 4](https://www.slimframework.com/)
- [Rakit\Validation](https://github.com/rakit/validation)
- [Firebase JWT](https://github.com/firebase/php-jwt)
- [Monolog](https://github.com/Seldaek/monolog)
- [Medoo] (https://github.com/catfan/Medoo)

---

## Writing validation code

```php


    $validator = new \App\Helpers\Validator();
    $data      = $request->getParsedBody();
    $rules     = [
        'pin' => 'required|min:6|max:6|regex:/^[1-9][0-9]*$/',
    ];
    $messages = [

    ];
    $validationResult = $validator->make($data, $rules, $messages);
    if ($validationResult !== true) {
        return $this->json(['errors' => $validationResult], 422);
    }
    $validData = $validator->validData;



```

### Getting request user from the middleware

```php

        $uid = $request->getAttribute('uid');

```

### MySQL query (alias with medoo)

```php
        $rows = $this->db->select('table-name', '*');

        $rows = $this->db->select('table-name', ['id','name']);

        $rows = $this->db->select('table-name', 'id');

        $rows = $this->db->select('table-name', 'id', ['id'=> 3]);

        $rows = $this->db->get('table-name', 'id', ['id'=> 3]);

        $rows = $this->db->insert('table-name', $data);

        $rows = $this->db->update('table-name', $data, $where);

        $rows = $this->db->delete('table-name', $where);

```
### Dynamic routing
```php

    $group->get('/{slug}', [App\Controllers\Roles\IndexController::class, 'get'])->setName('roles.get');

```

### Get slug value in controller

```php
    public function get(Request $request, Response $response, array $args): Response
    {

        $slug = $args['slug'];

        $row = $this->db->get('roles', '*' ,['slug' => $slug]);

        return $this->json($row);

    }

```

## 🤝 Contributing

Pull requests are welcome. For major changes, please open an issue first.

---

## 📄 License

This project is open-source and available under the [MIT License](LICENSE).

---



## 👤 Author

**Alaksandar Jesus Gene AMS**
Entrepreneur, Developer — [skriptx.com](https://skriptx.com)

---
