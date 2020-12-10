Playground
==============

```
cp elastic.ini.dist elastic.ini
vi elastic.ini # change it according to your own config

docker-compose up --build
```

The endpoints can be reached on:

```
http://localhost/index_dev.php/sqlite
http://localhost/index_dev.php/pgsql
http://localhost/index_dev.php/mysql
http://localhost/index_dev.php/doctrine-dbal-mysql
```

The error is happening in here and it seems to be related to doctrine/dbal:

```
// src/DoctrineDbalMysql.php

public function selectOne(): array
{
    $queryBuilder = $this->connection->createQueryBuilder();
    $queryBuilder->select('m.*')
        ->from('messages', 'm')
        ->where("m.id = ?");

    return $this->connection->fetchAssoc($queryBuilder, [1]);
}
```

The exception thrown looks like this:

```
1/1) FatalThrowableError
Type error: elastic_apm_call_intercepted_original() expects parameter 1 to be string, object given

in InterceptionManager.php line 86
at elastic_apm_call_intercepted_original()
in InterceptionManager.php line 86
at InterceptionManager->interceptedCall(1, 2, object(PDOConnection), array(object(QueryBuilder), array()))
in PhpPartFacade.php line 138
at PhpPartFacade::interceptedCall(2, object(PDOConnection), object(QueryBuilder), array())
at PDO->prepare(object(QueryBuilder), array())
in PDOConnection.php line 77
at PDOConnection->prepare(object(QueryBuilder))
in Connection.php line 904
at Connection->executeQuery(object(QueryBuilder), array(1), array())
in Connection.php line 554
at Connection->fetchAssoc(object(QueryBuilder), array(1))
in DoctrineDbalMysql.php line 34
at DoctrineDbalMysql->selectOne()
in controllers.php line 140
at {closure}(object(Request))
in HttpKernel.php line 149
at HttpKernel->handleRaw(object(Request), 1)
in HttpKernel.php line 66
at HttpKernel->handle(object(Request), 1, true)
in Application.php line 496
at Application->handle(object(Request))
in Application.php line 477
at Application->run()
in index_dev.php line 22
```
