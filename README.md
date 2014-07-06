Пользовательские переменные
=========

Необходимы в случае, если требуется добавить несколько простых настроек для сайта с возможностью редактирования из
административной части, например для возможности изменения клиентом. Для удобства есть возможность группировки переменных.

Библиотека находятся в пространстве имен ``Citfact\UserVars``

Установка
==================
Создайте или обновите ``composer.json`` файл и запустите ``php composer.phar install``
``` json
  {
      "require": {
          "citfact/uservars": "dev-master"
      }
  }
```

Пример использования
==================
Для быстрого доступа к переменным и группам, служат классы ``Citfact\UserVars\Vars`` и ``Citfact\UserVars\VarsGroup``

``` php
  use Citfact\UserVars;

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
```

Для более гибкой выборки переменных или групп, работайте на прямую через модели:

- ``Citfact\UserVars\Model\VarsTable``
- ``Citfact\UserVars\Model\VarsGroupTable``

``` php
  use Bitrix\Main\Entity;
  use Citfact\UserVars\Model;

  $queryBuilder = new Entity\Query(Model\VarsGroupTable::getEntity());
  $queryBuilder
    ->setSelect(array('ID', 'NAME', 'CODE'))
    ->setOrder(array('ID', 'asc'))
    ->setFilter(array('CODE' => 'TEST_CODE'))
    ->setLimit(1);

  $result = $queryBuilder->exec()->fetch();
```

Удаление и обновление, также выполняется через модель

``` php
  use Citfact\UserVars\Model;

  // Удаляем переменную с ID = 1
  Model\VarsTable::delete(1);

  // Обновляем наименование у группы
  Model\VarsGroupTable::update(1, array('NAME' => 'New name'));
