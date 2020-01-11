# Monsoon Framework

Monsoon is a simple, fast and secure PHP MVC framework for your rapid application development (RAD) needs. Documentation is available at https://monsoonphp.com 

## Getting Started

1. Download the framework as a zip file from Github repository or through composer
2. Point your webserver's root to _public/_ folder 
3. Access the URL from your browser

## Available Features

This framework readily supports Composer, Docker, Phinx and Gulp.

### Composer is NOT mandatory

The framework is designed to be Composer independent. But you can go ahead and start using composer out of the box by using ```composer install``` command.

The framework can also be installed through composer with this command. 

```composer create-project monsoon/framework .```

You can add your favourite Composer packages right away after you initialize composer. 

```composer require vendor/package```

### Running on PHP server
You can use PHP's internal webserver to run the application in your development system. Give this command in the terminal. The default URL will be http://localhost:8080

```php -S localhost:8080 -t public```

Alternatively, you can also run bin/start.sh. 

```sh bin/start.sh```

### Docker
To run this framework in a docker container, give this command in terminal. Dockerfile is available in _data/docker/_ folder

```docker-compose up -d```

The default URL will be http://localhost:8080

### Phinx

Version your database changes with Phinx library. Run your migrations with this commands. See _phinx.php_ for predefined values. 

### Gulp
You can install Gulp with NPM with these commands. See _gulpfile.js_ for configurations.

```
npm install

gulp js|css|sass
```


