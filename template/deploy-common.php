<?php
namespace Deployer;

require 'recipe/common.php';

inventory('hosts.yml');

set('bin/yii', '{{bin/php}} yii');

task('ssh-add', function() {
	runLocally('eval $(ssh-agent -s)');
	runLocally('ssh-add ~/.ssh/id_rsa');
});

task('deploy:run_migrations', function() {
	run('cd {{release_path}} && {{bin/yii}} migrate --interactive=0');
});

task('deploy:install', function () {
	run('cd {{project_path}} && {{bin/composer}} setup -- --name="{{name}}" --theme={{theme}} --db={{db}} --dbUser={{dbUser}} --dbPassword={{dbPassword}} --dbPrefix={{dbPrefix}} --baseUrl={{baseUrl}}');
	
	run('cd {{release_path}} && {{bin/yii}} setup --interactive=0');
	
	//run("cd {{project_path}}/web && {{bin/symlink}} {{project_path}}/backend/web admin");
	//run("cd {{project_path}}/web && {{bin/symlink}} {{project_path}}/storage/web storage");
});

