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

use Bitrix\Main\Application;
use Bitrix\Main\Entity;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Citfact\UserVars;
use Citfact\UserVars\Model;

Loc::loadMessages(__FILE__);
Loc::loadMessages(__DIR__ . '/user_vars.php');

global $APPLICATION;

$application = Application::getInstance();
$applicationOld = & $APPLICATION;

$APPLICATION->setTitle(Loc::getMessage('USER_VARS_TITLE'));

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
    'TEXT' => Loc::getMessage('USER_VARS_BACK_GROUP'),
    'TITLE' => Loc::getMessage('USER_VARS_BACK_GROUP'),
    'LINK' => 'user_vars.php?lang=' . LANGUAGE_ID,
    'ICON' => 'btn_list',
);

$tabsStructur = array(
    array(
        'DIV' => 'group',
        'ICON' => 'main_user_edit',
        'TAB' => Loc::GetMessage('USER_VARS_GROUP_NAME'),
        'TITLE' => Loc::GetMessage('USER_VARS_GROUP_NAME')
    ),
);

$submitTypeSave = ($request->getPost('save')) ? true : false;
$submitTypeApply = ($request->getPost('apply')) ? true : false;

$isOldGroup = false;
$isNewGroup = true;

if (array_key_exists('ID', $_REQUEST) && (int)$_REQUEST['ID'] > 0) {
    $varsGroup = new UserVars\VarsGroup();
    $dataGroup = $varsGroup
        ->findOneById($_REQUEST['ID'])
        ->fetch();

    if (!empty($dataGroup)) {
        $isOldGroup = true;
        $isNewGroup = false;
    }
} else {
    $dataGroup = array_fill_keys(array('ID', 'NAME', 'CODE'), '');
}

if ($isOldGroup && $request->getQuery('action') == 'delete' && check_bitrix_sessid()) {
    Model\VarsGroupTable::delete($dataGroup['ID']);
    LocalRedirect(sprintf('user_vars.php?lang=%s', LANGUAGE_ID));
}

if ($request->isPost() && check_bitrix_sessid()) {
    $postData = array_map('strip_tags', $request->getPostList()->toArray());
    $postData = array_intersect_key($postData, array('NAME' => null, 'CODE' => null));

    if ($isNewGroup) {
        $result = Model\VarsGroupTable::add($postData);
        $groupId = $result->getId();
    } else {
        $result = Model\VarsGroupTable::update($dataGroup['ID'], $postData);
        $groupId = $dataGroup['ID'];
    }

    if (!$result->isSuccess()) {
        $errorsList = $result->getErrorMessages();
    } else {

        if ($submitTypeApply) {
            $redirectPath = sprintf('user_vars_group_edit.php?ID=%dlang=%s', $groupId, LANGUAGE_ID);
        } else {
            $redirectPath = sprintf('user_vars.php?lang=%s', LANGUAGE_ID);
        }

        LocalRedirect($redirectPath);
    }

    foreach ($postData as $key => $value) {
        $dataGroup[$key] = $value;
    }
}

$tabControl = new CAdminTabControl('tabControl', $tabsStructur);
$context = new CAdminContextMenu($contextMenu);

$errorsList = (isset($errorsList)) ? $errorsList : array();
$dataGroup = array_map('htmlspecialchars', $dataGroup);

$prologType = ($request->getQuery('mode') == 'list') ? 'prolog_js' : 'prolog';
require sprintf('%s%s', getenv('DOCUMENT_ROOT'), $includePath[$prologType]);

$context->show();

if (sizeof($errorsList) > 0) {
    CAdminMessage::ShowMessage(join(PHP_EOL, $errorsList));
}

?>
    <form method="post" action="<?= $applicationOld->getCurPage() ?>" enctype="multipart/form-data">
    <input type="hidden" name="ID" value="<?= $dataGroup['ID'] ?>">
    <input type="hidden" name="lang" value="<?= LANGUAGE_ID ?>">
    <?=bitrix_sessid_post();?>
<?

$tabControl->begin();
$tabControl->beginNextTab();
$groupMap = Model\VarsGroupTable::getMap();

?>
    <tr>
        <td width="40%"><strong><?= $groupMap['NAME']['title'] ?></strong></td>
        <td width="60%"><input type="text" name="NAME" value="<?= $dataGroup['NAME'] ?>" /></td>
    </tr>
    <tr>
        <td><strong><?= $groupMap['CODE']['title'] ?></strong></td>
        <td><input type="text" name="CODE" value="<?= $dataGroup['CODE'] ?>" /></td>
    </tr>
<?

$tabControl->buttons(array('back_url' => 'user_vars.php?lang=' . LANGUAGE_ID));
$tabControl->end();

?>
    </form>
<?

$epilogType = ($request->getQuery('mode') == 'list') ? 'epilog_js' : 'epilog';
require sprintf('%s%s', getenv('DOCUMENT_ROOT'), $includePath[$epilogType]);