<?php
namespace Deployer;

require 'deploy-common.php';

// Project repository
//set('repository', 'git@bitbucket.org:antwebstudio/ruangkongsi.git');

// Project name
set('project_path', '{{release_path}}');

// Shared files/dirs between deploys 
set('shared_files', [
	'.env',
]);
set('shared_dirs', [
	'storage',
]);

// Writable dirs by web server 
set('writable_dirs', []);

desc('Deploy your project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
	'deploy:install',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);
