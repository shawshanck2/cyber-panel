<?php
/**
 * By MahmoudAp
 * Github: https://github.com/mahmoud-ap
 */

if (!defined('PATH')) die();

if (getConfig('session', 'enabled')) {
  $container['session'] = function ($c) {
    return new \SlimSession\Helper;
  };
}
