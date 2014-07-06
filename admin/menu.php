<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Entity;
use Bitrix\Main\Loader;
use Citfact\UserVars\Model;

Loc::loadMessages(__FILE__);
Loader::includeModule('citfact.uservars');

$queryBuilder = new Entity\Query(Model\VarsGroupTable::getEntity());
$queryBuilder
    ->setSelect(array('ID', 'NAME', 'CODE'))
    ->setOrder(array('ID' => 'ASC'));

$varsGroupResult = $queryBuilder->exec();
$varsGroupList = array();
while ($group = $varsGroupResult->fetch()) {
    $varsGroupList[] = array(
        'text' => $group['NAME'],
        'url' => 'user_vars_list.php?GROUP_ID='.$group['ID'].'&lang='.LANG,
        'module_id' => 'user_vars',
        'more_url' => Array(
            'user_vars_edit.php?GROUP_ID='.$group['ID'].'&lang='.LANG,
        ),
    );
}

$menuList[] = array(
    'parent_menu' => (Loader::includeModule('citfact.core')) ? 'global_menu_citfact' : 'global_menu_services',
    'section' => 'user_vars',
    'sort' => 200,
    'text' => Loc::getMessage('USER_VARS_TITLE'),
    'url' => 'user_vars.php?lang='.LANGUAGE_ID,
    'icon' => 'user_vars_menu_icon',
    'page_icon' => 'user_vars_page_icon',
    'more_url' => array(
        'user_vars_group_edit.php'
    ),
    'items_id' => 'uservars_menu',
    'items' => $varsGroupList,
);

return $menuList;

