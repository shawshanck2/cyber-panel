<?php
/**
 * By MahmoudAp
 * Github: https://github.com/mahmoud-ap
 */

if (!defined('PATH')) die();

$configs['db']['enabled']   = true;
$configs['db']['driver']    = getenv("DB_CONNECTION");
$configs['db']['host']      = getenv('DB_HOST');
$configs['db']['database']  = getenv("DB_DATABASE");
$configs['db']['username']  = getenv("DB_USERNAME");
$configs['db']['password']  = getenv("DB_PASSWORD");
$configs['db']['charset']   = 'utf8';
$configs['db']['collation'] = 'utf8_unicode_ci';
$configs['db']['prefix']    = "cp_";
