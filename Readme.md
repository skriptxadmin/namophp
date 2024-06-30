## NAMO PHP Framework: Power in Simplicity ##

Discover Namo, the lightweight PHP framework designed for developers who value performance and efficiency. With its small footprint, Namo delivers the core features you need to build robust applications without the overhead. Enjoy an intuitive routing system, elegant ORM, built-in authentication, and a powerful templating engine—all while keeping your code clean and maintainable. Namo PHP Framework offers the perfect balance of simplicity and power, making it the ideal choice for modern web development.

### Download Instructions ###

`git clone https://github.com/skriptxadmin/namophp.git`


### Install Instructions ###

`composer update`

`npm install`

### Setup ###

update .env file with your base site url, app version and database details

If you are using xampp, point to public folder

```
SITE_URL="http://localhost/namo/public"
APP_VERSION=3.0.0 
```

While production use, point your entry file as public/index.

Update the timezone in public/index.php

```
date_default_timezone_set('Asia/Kolkata');
```

### Routing ###

Routing can be specified in

`app/Routes/web.php` 

and 

`app/Routes/api.php`

We use Braums Router and documentation can be found from

`https://github.com/bramus/router`


### Accessing .env variables ###

You can access the .env variables as $_ENV['key']

example you can see in app/Controllers/controller


### JS and CSS files ###

This framework uses webpack mix and hot reload is not implemented.

The files can be found in src/scripts and src/styles


#### For Development ####


`npm start` 


#### For Production ####

`npm run prod`


## Advanced ##

We use illuminate/database, from laravel framework. If you are not familiar with migrations and seeding, use some UI for easy development

### Migrations ###

select the driver (sqlite or mysql) in the .env file

update the details on respective block

in database/migrations.php and app/Models/Model.php

### Seeding ###

Use database/seed.php and example of users seed

## Version 3 ##

### Session ###

```
use Josantonius\Session\Session;
$session = new Session();
$username = $session->get('username'); 
```
### Logger ###

```
use App\Helpers\Logger;
$logger = new Logger();
$logger->error("Your error message")
```

### S3 Storage ###

```
use App\Helpers\S3;
$path = $_FILES['image']['name'];
$ext = pathinfo($path, PATHINFO_EXTENSION);
$filename = uniqid().'.'.$ext;
$key  = $username.'/'.$filename;
$args = [    'Key' => $key,
'ACL'    => 'public-read',
'SourceFile' => $_FILES['image']["tmp_name"]];
$s3 = new S3;
$store = $s3->put($args);
$url = $s3->getUrl($key);
```