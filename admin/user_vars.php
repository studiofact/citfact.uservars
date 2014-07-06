<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once getenv('DOCUMENT_ROOT') . '/bitrix/modules/main/include/prolog_admin_before.php';

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Citfact\UserVars;
use Citfact\UserVars\Model;

Loc::loadMessages(__FILE__);

global $APPLICATION;

$application = Application::getInstance();
$applicationOld = & $APPLICATION;

$applicationOld->setTitle(Loc::getMessage('USER_VARS_TITLE'));

if (!Loader::includeModule('citfact.uservars')) {
    $applicationOld->authForm(Loc::getMessage('ACCESS_DENIED'));
}

$request = $application
    ->getContext()
    ->getRequest();

$includePath = array(
    'prolog' => '/bitrix/modules/main/include/prolog_admin_after.php',
    'prolog_js' => '/bitrix/modules/main/include/prolog_admin_js.php',
    'epilog' => '/bitrix/modules/main/include/epilog_admin.php',
    'epilog_js' => '/bitrix/modules/main/include/epilog_admin_js.php',
);

$contextMenu[] = array(
    'TEXT' => Loc::getMessage('USER_VARS_ADD_GROUP'),
    'TITLE' => Loc::getMessage('USER_VARS_ADD_GROUP'),
    'LINK' => 'user_vars_group_edit.php?lang=' . LANGUAGE_ID,
    'ICON' => 'btn_new',
);

$headers = array(
    array('id' => 'ID', 'content' => 'ID', 'sort' => 'ID', 'default' => true),
    array('id' => 'NAME', 'content' => Loc::getMessage('USER_VARS_TABLE_NAME'), 'sort' => 'NAME', 'default' => true),
    array('id' => 'CODE', 'content' => Loc::getMessage('USER_VARS_TABLE_CODE'), 'sort' => 'CODE', 'default' => true)
);

$tableId = 'tbl_user_vars';
$adminSort = new CAdminSorting($tableId, 'NAME', 'asc');
$adminList = new CAdminList($tableId, $adminSort);
$adminList->addHeaders($headers);

if ($request->getQuery('mode') != 'list') {
    $context = new CAdminContextMenu($contextMenu);
}

$queryBuilder = new Entity\Query(Model\VarsGroupTable::getEntity());
$queryBuilder->setSelect(array('ID', 'NAME', 'CODE'));

$sortBy = ($request->getQuery('by')) ? strtoupper($request->getQuery('by')) : 'ID';
$sortOrder = ($request->getQuery('order')) ?: 'asc';
$queryBuilder->setOrder(array($sortBy => $sortOrder));

$resultData = new CAdminResult($queryBuilder->exec(), $tableId);
$resultData->navStart();

$adminList->navText($resultData->getNavPrint(Loc::getMessage('PAGES')));
while ($item = $resultData->fetch()) {
    $row = $adminList->addRow($item['ID'], $item);
    $actions = array();

    $actions[] = array(
        'ICON' => 'list',
        'TEXT' => Loc::getMessage('USER_VARS_ACTION_LIST'),
        'ACTION' => $adminList->actionRedirect('user_vars_list.php?lang=' . LANGUAGE_ID . '&GROUP_ID=' . $item['ID']),
    );

    $actions[] = array(
        'ICON' => 'edit',
        'TEXT' => Loc::GetMessage('USER_VARS_ACTION_EDIT_GROUP'),
        'ACTION' => $adminList->actionRedirect('user_vars_group_edit.php?ID=' . $item['ID'])
    );

    $actions[] = array(
        'ICON' => 'delete',
        'TEXT' => Loc::getMessage('USER_VARS_ACTION_DELETE_GROUP'),
        'ACTION' => "if(confirm('" . Loc::getMessage('USER_VARS_DELETE_GROUP_CONFIRM') . "')) " .
            $adminList->actionRedirect('user_vars_group_edit.php?action=delete&ID=' . $item['ID'] . '&' . bitrix_sessid_get())
    );

    $row->addActions($actions);
}

$prologType = ($request->getQuery('mode') == 'list') ? 'prolog_js' : 'prolog';
require sprintf('%s%s', getenv('DOCUMENT_ROOT'), $includePath[$prologType]);

if ($prologType != 'prolog_js') {
    $context->show();
}

$adminList->checkListMode();
$adminList->displayList();

$epilogType = ($request->getQuery('mode') == 'list') ? 'epilog_js' : 'epilog';
require sprintf('%s%s', getenv('DOCUMENT_ROOT'), $includePath[$epilogType]);