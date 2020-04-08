# Monsoon Framework

Monsoon PHP is an open-source, simple, fast, secure and free PHP web framework that assists developers in creating a secure application rapidly (RAD). You can maintain your Application’s code, as well as your REST API and CLI based scripts in a single codebase with Monsoon. Documentation is available at https://monsoonphp.com 

## Getting Started

### Direct Download

1. Download the framework as a zip file from Github repository or through composer
2. Point your webserver's root to _public/_ folder 
3. Access the URL from your browser

### Composer Download

1. Give the command in terminal ```composer create-project monsoon/framework .```
2. Point your webserver's root to _public/_ folder 
3. Access the URL from your browser

## Available Tools from Composer

This framework uses following packages when you install as composer-project. 

* phinx
* php_codesniffer
* phpunit
* pdepend
* phpmd

## Configuration File

Configuration file distributable file is available under ```src/Config/.env.php.dist```. Remove the .dist extension to get started. More configuration parameters can be setup in ```src/Config/Config.php```. 

### Database migrations with Phinx

Version your database changes with Phinx library. Run your migrations with this commands. See _phinx.php_ for predefined values. 

### Running on PHP server
You can use PHP's internal webserver to run the application in your development system. Give this command in the terminal. The default URL will be http://localhost:8080

```php -S localhost:8080 -t public```

Alternatively, you can also run bin/start.sh. 

```sh bin/start.sh```

### Docker
To run this framework in a docker container, give this command in terminal. Dockerfile is available in _data/docker/_ folder

```docker-compose up -d```

The default URL will be http://localhost:8080


### Gulp
You can install Gulp with NPM with these commands. See _gulpfile.js_ for configurations.

```
npm install

gulp js|css|sass
```