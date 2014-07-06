<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Citfact\UserVars\Model;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class VarsGroupTable extends Entity\DataManager
{
    /**
     * {@inheritdoc}
     */
    public static function getFilePath()
    {
        return __FILE__;
    }

    /**
     * {@inheritdoc}
     */
    public static function getTableName()
    {
        return 'b_citfact_uservars_group';
    }

    /**
     * {@inheritdoc}
     */
    public static function getMap()
    {
        return array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
                'title' => Loc::getMessage('VARS_GROUP_ENTITY_ID_FIELD'),
            ),
            'NAME' => array(
                'data_type' => 'string',
                'required' => true,
                'title' => Loc::getMessage('VARS_GROUP_ENTITY_NAME_FIELD'),
            ),
            'CODE' => array(
                'data_type' => 'string',
                'required' => true,
                'title' => Loc::getMessage('VARS_GROUP_ENTITY_CODE_FIELD'),
            ),
        );
    }
}