<?php
/**
 * By MahmoudAp
 * Github: https://github.com/mahmoud-ap
 */

if (!defined('PATH')) die();

loadHelpers("main");

$app->add(new \App\Middlewares\OptionsMethodCheck);

$uController = new \App\Controllers\Users($container);

$container['notFoundHandler']   = function ($container) use ($uController) {
	return function ($request, $response) use ($uController) {
		return $uController->notFoundPage($request, $response);
	};
};

$container['notAllowedHandler']   = function ($container) use ($uController) {
	return function ($request, $response) use ($uController) {
		return $uController->notFoundPage($request, $response);
	};
};

include "admin.php";
include "api.php";
include "cron.php";







