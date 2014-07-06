Пользовательские переменные
=========

Необходимы в случае, если требуется добавить несколько простых настроек для сайта с возможностью редактирования из
административной части, например для возможности изменения клиентом. Для удобства есть возможность группировки переменных.

Библиотека находятся в пространстве имен ``Citfact\Core\UserVars``

Пример использования
~~~~~~~~~~
Для быстрого доступа к переменным и группам, служат классы ``Citfact\Core\UserVars\Vars`` и ``Citfact\Core\UserVars\VarsGroup``

.. code-block:: php

  use Citfact\Core\UserVars;

  $varsGroup = new UserVars\VarsGroup();

  // Предопределены два метода findBy* и findOneBy*
  $varsGroup->findById(1);
  $varsGroup->findByCode('TEST');

  $vars = new UserVars\Vars();
  $vars->findById(1);
  $vars->findByCode('TEST');
  $vars->findByGroup(1);

  // Получаем все переменные по символьному коду
  $varsResult = $vars->findByCode('FIND_CODE');
  while ($var = $varsResult->fetch()) {
    print_r($var);
  }

  // Получаем одну переменную из группы
  $var = $vars->findOneByGroup(1)->fetch();

Для более гибкой выборки переменных или групп, работайте на прямую через модели:

- ``Citfact\Core\UserVars\Model\VarsTable``
- ``Citfact\Core\UserVars\Model\VarsGroupTable``

.. code-block:: php

  use Bitrix\Main\Entity;
  use Citfact\Core\UserVars\Model;

  $queryBuilder = new Entity\Query(Model\VarsGroupTable::getEntity());
  $queryBuilder
    ->setSelect(array('ID', 'NAME', 'CODE'))
    ->setOrder(array('ID', 'asc'))
    ->setFilter(array('CODE' => 'TEST_CODE'))
    ->setLimit(1);

  $result = $queryBuilder->exec()->fetch();

Удаление и обновление, также выполняется через модель

.. code-block:: php

  use Citfact\Core\UserVars\Model;

  // Удаляем переменную с ID = 1
  Model\VarsTable::delete(1);

  // Обновляем наименование у группы
  Model\VarsGroupTable::update(1, array('NAME' => 'New name'));
