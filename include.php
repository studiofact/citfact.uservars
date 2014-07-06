<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Bitrix\Main\Loader;

Loader::registerAutoLoadClasses('citfact.uservars', array(
    'Citfact\UserVars\Vars' => 'lib/Vars.php',
    'Citfact\UserVars\VarsGroup' => 'lib/VarsGroup.php',
    'Citfact\UserVars\Model\VarsTable' => 'lib/Model/VarsTable.php',
    'Citfact\UserVars\Model\VarsGroupTable' => 'lib/Model/VarsGroupTable.php',
));

