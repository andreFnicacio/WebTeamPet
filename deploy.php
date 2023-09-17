<?php

namespace Deployer;

require 'recipe/laravel.php';

// Config
set('application', 'LifetPet ERP');
set('keep_releases', 3);
set('repository', 'git@bitbucket.org:lifepetwebteam/petmanager.git');
set('git_tty', true);

// Hosts
host('staging')
    ->set('hostname', 'lifepet-erp-staging')
    ->set('branch', 'staging')
    ->set('remote_user', 'ubuntu')
    ->set('deploy_path', '/home/ubuntu/staging-app.lifepet.com.br');

// Tasks
// Disable route:cache since routes/api uses Closures instead of Controller@action
task('artisan:route:cache', function () {});

task('build', function () {
    cd('{{release_path}}');
    run('npm run build');
});

after('deploy:failed', 'deploy:unlock');
