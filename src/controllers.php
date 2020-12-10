<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Elastic\Apm\ElasticApm;

//Request::setTrustedProxies(array('127.0.0.1'));

$app->get('/sqlite', function (Request $request) use ($app) {
    $transaction = ElasticApm::getCurrentTransaction();
    $transaction->setName(sprintf(
        '%s %s',
        $request->getMethod(),
        $request->getUri()
    ));

    $data = [];

    $driver = new Sqlite();
    $driver->dropTable();
    $driver->createTable();

    $driver->insert();
    $data['insert'] = $driver->select();

    $driver->update();
    $data['update'] = $driver->select();

    $driver->delete();
    $data['delete'] = $driver->select();

    return new JsonResponse([
        'driver' => 'sqlite',
        'count' => count(array_filter($data)),
        'data' => $data,
        'request' => sprintf(
            '%s %s',
            $request->getMethod(),
            $request->getUri()
        ),
    ]);
})
->bind('sqlite')
;

$app->get('/mysql', function (Request $request) use ($app) {
    $transaction = ElasticApm::getCurrentTransaction();
    $transaction->setName(sprintf(
        '%s %s',
        $request->getMethod(),
        $request->getUri()
    ));

    $data = [];

    $driver = new Mysql();
    $driver->dropTable();
    $driver->createTable();

    $driver->insert();
    $data['insert'] = $driver->select();

    $driver->update();
    $data['update'] = $driver->select();

    $driver->delete();
    $data['delete'] = $driver->select();

    return new JsonResponse([
        'driver' => 'mysql',
        'count' => count(array_filter($data)),
        'data' => $data,
        'request' => sprintf(
            '%s %s',
            $request->getMethod(),
            $request->getUri()
        ),
    ]);
})
    ->bind('mysql')
;

$app->get('/pgsql', function (Request $request) use ($app) {
    $transaction = ElasticApm::getCurrentTransaction();
    $transaction->setName(sprintf(
        '%s %s',
        $request->getMethod(),
        $request->getUri()
    ));

    $data = [];

    $driver = new Pgsql();
    $driver->dropTable();
    $driver->createTable();

    $driver->insert();
    $data['insert'] = $driver->select();

    $driver->update();
    $data['update'] = $driver->select();
    $data['selectOne'] = $driver->selectOne();

    $driver->delete();
    $data['delete'] = $driver->select();

    return new JsonResponse([
        'driver' => 'pgsql',
        'count' => count(array_filter($data)),
        'data' => $data,
        'request' => sprintf(
            '%s %s',
            $request->getMethod(),
            $request->getUri()
        ),
    ]);
})
    ->bind('pgsql')
;

$app->get('/doctrine-dbal-mysql', function (Request $request) use ($app) {
    $transaction = ElasticApm::getCurrentTransaction();
    $transaction->setName(sprintf(
        '%s %s',
        $request->getMethod(),
        $request->getUri()
    ));

    $data = [];

    $driver = new DoctrineDbalMysql();
    $driver->dropTable();
    $driver->createTable();

    $driver->insert();
    $data['insert'] = $driver->select();
    $data['dbal'] = $driver->selectOne();

    $driver->update();
    $data['update'] = $driver->selectOne();

    $driver->delete();
    $data['delete'] = $driver->selectOne();

    return new JsonResponse([
        'driver' => 'doctrine-dbal-mysql',
        'count' => count(array_filter($data)),
        'data' => $data,
        'request' => sprintf(
            '%s %s',
            $request->getMethod(),
            $request->getUri()
        ),
    ]);
})
    ->bind('doctrine-dbal-mysql')
;

$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/'.$code.'.html.twig',
        'errors/'.substr($code, 0, 2).'x.html.twig',
        'errors/'.substr($code, 0, 1).'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
