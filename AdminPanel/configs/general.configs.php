<?php
/**
 * By MahmoudAp
 * Github: https://github.com/mahmoud-ap
 */

if (!defined('PATH')) die();

$root = (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] .  str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);

$configs['url'] = $root;

$configs['timezone'] = 'Asia/Tehran';

$configs['show_errors']     = true;
$configs['versionUrl']      = "https://api.github.com/repos/mahmoud-ap/cyber-panel/releases/latest";

$configs['access_control']['allow_origin'] = '*';
$configs['access_control']['allow_headers'] = '*';
$configs['access_control']['allow_methods'] = '*';
$configs['access_control']['allow_credentials'] = '*';
