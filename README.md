# Monsoon Framework

Monsoon is a simple, fast and secure PHP MVC framework for your rapid application development (RAD) needs. Documentation is available at https://monsoonphp.com 

## Getting Started

Download the framework, either through composer or from the Git repository. Point your webserver's root to public/ folder and access the URL from your browser. 

You can also use PHP's internal webserver to run the application in your development system. Give this command in the terminal.

```php -S localhost:8080 -t public```

The default URL will be http://localhost:8080

## Available Features

This framework readily supports Docker, Composer, Phinx, Gitlab CI/CD and Gulp.

### Composer is optional

The framework is designed to be Composer independent. But you can go ahead and start using composer out of the box for your application needs. You can add your favourite Composer packages right away after you initialize composer. 

```composer require vendor/package```

### Docker
To run this framework in a docker container, give this command in terminal.

```docker-compose up -d```

The default URL will be http://localhost:8080.

### Phinx

Version your database changes with Phinx library. Run your migrations with this commands. See your phinx.php for predefined values. 

### Gulp
You can install Gulp with NPM with these commands. See _gulpfile.js_ for configurations.

```
npm install

gulp js|css|sass
```


