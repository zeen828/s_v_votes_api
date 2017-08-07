<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * @package     CodeIgniter Github Deploy
 * @author      Will Lu
 * @copyright   Copyright (c) 2017, Will Lu
 * @link        https://github.com/zeen828/CodeIgniter-Github-Deploy
 * @since       Version 1.0
 */

/*
|--------------------------------------------------------------------------
| System git path
|--------------------------------------------------------------------------
|
| Server git command absolute path
|
*/
$config['git_path'] = '/usr/bin/git';

/*
|--------------------------------------------------------------------------
| Website config
|--------------------------------------------------------------------------
|
| Server local website project name
|
*/
$config['local_project_name'] = 'codeigniter-github-deploy';

/*
|--------------------------------------------------------------------------
| GitHub config
|--------------------------------------------------------------------------
|
| github project name, secret, path and branch name
| Example:
| $config['github'] = array(
|     'project name'=>array(
|         'branch name'=>array(
|             'secret_key'=>'Webhooks Secret',
|             'git_clean'=>false,
|             'project_path'=>'Service website project absolute path',
|         ),
|     ),
| );
|
| #project name all lowercase
|
*/
$config['github'] = array(
	'codeigniter-github-deploy'=>array(
		'master'=>array(
			'git_clean'=>false,
			'secret_key'=>'ci_deploy',
			'project_path'=>'/var/www/html/',
		),
		'develop'=>array(
			'git_clean'=>false,
			'secret_key'=>'ci_deploy',
			'project_path'=>'/var/www/html_dev/',
		),
	),
);

/*
|--------------------------------------------------------------------------
| lib debug
|--------------------------------------------------------------------------
|
| 除錯用
|
*/
$config['github_deploy_debug'] = true;
