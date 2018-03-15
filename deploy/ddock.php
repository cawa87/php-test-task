#!/usr/bin/env php
<?php
/*
 * Deployment Dock Console Application
 */

// Composer autoloader
require __DIR__ . '/vendor/autoload.php';

use App\Ddock;

$ddock = new Ddock($input);

$ddock
    //->task('deploy:npm-install', 'npm i')
    ->task('deploy:composer-install', 'composer install')
    //->task('deploy:compile-assets', 'grunt deploy-production')
    //->task('deploy:create-route-cache', 'php artisan route:cache')
    //->task('deploy:create-config-cache', 'php artisan config:cache')

    //->task('migrate:make-migrations', 'php artisan migrate --force')
    ->task('failure:send-email', 'php -r "mail(\'test@gmail.com\', \'Deploy Failure\', \'Deploy Failure\')"')

    ->task('success:project-serve', 'php artisan serve')
    //->task('rollback:rollback-migrations', 'php artisan migrate:rollback')

    ->after('deploy')
        //->success('migrate')
        ->success('failure')
        ->failure('failure')

    ->after('migrate')
        ->success('success');
        //->failure('rollback');

$ddock->start('deploy');
