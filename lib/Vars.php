<?php

/*
 * This file is part of the Studio Fact package.
 *
 * (c) Kulichkin Denis (onEXHovia) <onexhovia@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Citfact\UserVars;

use Bitrix\Main\Entity;
use Citfact\UserVars\Model;

class Vars
{
    /**
     * Executes the query
     *
     * @param string $filterField
     * @param mixed $filterFieldValue
     * @param string $method
     *
     * @return \Bitrix\Main\DB\MysqlResult
     */
    private function execute($filterField, $filterFieldValue, $method)
    {
        $queryBuilder = new Entity\Query(Model\VarsTable::getEntity());
        $queryBuilder
            ->setSelect(array('ID', 'NAME', 'CODE', 'DESCRIPTION', 'GROUP_ID', 'VALUE'))
            ->setOrder(array('ID' => 'ASC'))
            ->setFilter(array($filterField => $filterFieldValue));

        if ($method == 'findOneBy') {
            $queryBuilder->setLimit(1);
        }

        return $queryBuilder->exec();
    }

    /**
     * Magic finders.
     *
     * @param string $method
     * @param array $arguments
     *
     * @return \Bitrix\Main\DB\MysqlResult
     *
     * @throws \BadMethodCallException   If the method called is an invalid find* method.
     *         \InvalidArgumentException If invalid filter field name.
     */
    public function __call($method, $arguments)
    {
        switch (true) {
            case (0 === strpos($method, 'findBy')):
                $by = substr($method, 6);
                $method = 'findBy';
                break;

            case (0 === strpos($method, 'findOneBy')):
                $by = substr($method, 9);
                $method = 'findOneBy';
                break;

            default:
                throw new \BadMethodCallException(
                    "Undefined method '$method'. The method name must start with ".
                    "either findBy or findOneBy!"
                );
        }

        $fieldName = strtoupper($by);
        $fieldName = ($fieldName == 'GROUP') ? 'GROUP_ID' : $fieldName;
        if (!in_array($fieldName, array('ID', 'CODE', 'GROUP_ID'))) {
            throw new \InvalidArgumentException('Invalid field name. Expected ID, CODE or GROUP');
        }

        return $this->execute($fieldName, $arguments[0], $method);
    }
}