<?php
return [
    'cache' => false,
    'cache_key' => 'deploy_process',
    'deploy_timeout' => 600,
    'lockfile' => __DIR__ . '/../data/deploy_lock.txt',
    'use_ssh' => true,
    'ssh_ip' => [
    	'192.168.1.1',
        '192.168.1.2',
    ],
    'project_url' => 'https://github.com/CawaKharkov/php-test-task.git',
	/* Заводим два проекта /var/www/project.v1 и /var/www/project.v2 и две сим линки для продакшена и стейжинга на эти папки */
	'path' => [
        'dir' => '/var/www',
        'production' => '/var/www/production',
        'staging' => '/var/www/staging'
    ]
];