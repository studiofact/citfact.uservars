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

if (array_key_exists('GROUP_ID', $_REQUEST) && (int)$_REQUEST['GROUP_ID'] > 0) {
    $varsGroup = new UserVars\VarsGroup();
    $dataGroup = $varsGroup
        ->findOneById($_REQUEST['GROUP_ID'])
        ->fetch();
}

if (!isset($dataGroup) || empty($dataGroup)) {
    LocalRedirect(sprintf('user_vars.php?lang=%s', LANGUAGE_ID));
}

$includePath = array(
    'prolog' => '/bitrix/modules/main/include/prolog_admin_after.php',
    'prolog_js' => '/bitrix/modules/main/include/prolog_admin_js.php',
    'epilog' => '/bitrix/modules/main/include/epilog_admin.php',
    'epilog_js' => '/bitrix/modules/main/include/epilog_admin_js.php',
);

$contextMenu[] = array(
    'TEXT' => Loc::getMessage('USER_VARS_BACK_VAR'),
    'TITLE' => Loc::getMessage('USER_VARS_BACK_VAR'),
    'LINK' => 'user_vars_list.php?GROUP_ID='. $dataGroup['ID'] .'&lang=' . LANGUAGE_ID,
    'ICON' => 'btn_list',
);

$tabsStructur = array(
    array(
        'DIV' => 'group',
        'ICON' => 'main_user_edit',
        'TAB' => Loc::GetMessage('USER_VARS_VAR_NAME'),
        'TITLE' => Loc::GetMessage('USER_VARS_VAR_NAME')
    ),
);

$submitTypeSave = ($request->getPost('save')) ? true : false;
$submitTypeApply = ($request->getPost('apply')) ? true : false;

$isOldVar = false;
$isNewVar = true;

if (array_key_exists('ID', $_REQUEST) && (int)$_REQUEST['ID'] > 0) {
    $vars = new UserVars\Vars();
    $dataVar = $vars
        ->findOneById($_REQUEST['ID'])
        ->fetch();

    if (!empty($dataVar)) {
        $isOldVar = true;
        $isNewVar = false;
    }
} else {
    $dataVar = array_fill_keys(array('ID', 'NAME', 'CODE', 'VALUE', 'DESCRIPTION', 'GROUP_ID'), '');
}

if ($isOldVar && $request->getQuery('action') == 'delete' && check_bitrix_sessid()) {
    Model\VarsTable::delete($dataVar['ID']);
    LocalRedirect(sprintf('user_vars_list.php?GROUP_ID=%d&lang=%s', $dataGroup['ID'], LANGUAGE_ID));
}

if ($request->isPost() && check_bitrix_sessid()) {
    $postData = array_map('strip_tags', $request->getPostList()->toArray());
    $postData = array_intersect_key($postData, $dataVar);
    $postData['GROUP_ID'] = $dataGroup['ID'];

    if ($isNewVar) {
        $result = Model\VarsTable::add($postData);
        $varId = $result->getId();
    } else {
        $result = Model\VarsTable::update($dataVar['ID'], $postData);
        $varId = $dataVar['ID'];
    }

    if (!$result->isSuccess()) {
        $errorsList = $result->getErrorMessages();
    } else {

        if ($submitTypeApply) {
            $redirectPath = sprintf('user_vars_edit.php?ID=%d&GROUP_ID=%d&lang=%s', $varId, $dataGroup['ID'], LANGUAGE_ID);
        } else {
            $redirectPath = sprintf('user_vars_list.php?GROUP_ID=%d&lang=%s', $dataGroup['ID'], LANGUAGE_ID);
        }

        LocalRedirect($redirectPath);
    }

    foreach ($postData as $key => $value) {
        $dataVar[$key] = $value;
    }
}

$tabControl = new CAdminTabControl('tabControl', $tabsStructur);
$context = new CAdminContextMenu($contextMenu);

$errorsList = (isset($errorsList)) ? $errorsList : array();
$dataVar = array_map('htmlspecialchars', $dataVar);

$prologType = ($request->getQuery('mode') == 'list') ? 'prolog_js' : 'prolog';
require sprintf('%s%s', getenv('DOCUMENT_ROOT'), $includePath[$prologType]);

$context->show();

if (sizeof($errorsList) > 0) {
    CAdminMessage::ShowMessage(join(PHP_EOL, $errorsList));
}

?>
    <form method="post" action="<?= $applicationOld->getCurPage() ?>" enctype="multipart/form-data">
    <input type="hidden" name="ID" value="<?= $dataVar['ID'] ?>">
    <input type="hidden" name="GROUP_ID" value="<?= $dataGroup['ID'] ?>">
    <input type="hidden" name="lang" value="<?= LANGUAGE_ID ?>">
    <?=bitrix_sessid_post();?>
<?

$tabControl->begin();
$tabControl->beginNextTab();
$varMap = Model\VarsTable::getMap();

?>
    <tr>
        <td width="40%"><strong><?= $varMap['NAME']['title'] ?></strong></td>
        <td width="60%"><input type="text" name="NAME" value="<?= $dataVar['NAME'] ?>" /></td>
    </tr>
    <tr>
        <td><strong><?= $varMap['CODE']['title'] ?></strong></td>
        <td><input type="text" name="CODE" value="<?= $dataVar['CODE'] ?>" /></td>
    </tr>
    <tr>
        <td><strong><?= $varMap['VALUE']['title'] ?></strong></td>
        <td><input type="text" name="VALUE" value="<?= $dataVar['VALUE'] ?>" /></td>
    </tr>
    <tr>
        <td><strong><?= $varMap['DESCRIPTION']['title'] ?></strong></td>
        <td><textarea name="DESCRIPTION" ><?= $dataVar['DESCRIPTION'] ?></textarea></td>
    </tr>
<?

$tabControl->buttons(array('back_url' => 'user_vars_list.php?GROUP_ID='. $dataGroup['ID'] .'&lang=' . LANGUAGE_ID));
$tabControl->end();

?>
    </form>
<?

$epilogType = ($request->getQuery('mode') == 'list') ? 'epilog_js' : 'epilog';
require sprintf('%s%s', getenv('DOCUMENT_ROOT'), $includePath[$epilogType]);