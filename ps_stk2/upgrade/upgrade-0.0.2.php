<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_0_0_2($module)
{
    $module->registerHook('actionProductAdd');
    $module->registerHook('actionProductUpdate');
    $module->registerHook('actionProductDelete');
    $module->registerHook('displayHome');
    $module->registerHook('displayOrderConfirmation2');
    $module->registerHook('displayCrossSellingShoppingCart');
    $module->registerHook('actionCategoryUpdate');
    $module->registerHook('actionAdminGroupsControllerSaveAfter');

    return true;
}