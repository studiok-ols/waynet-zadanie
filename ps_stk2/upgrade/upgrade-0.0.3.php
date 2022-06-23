<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_0_0_3($module)
{
    $module->registerHook('header');

    return true;
}