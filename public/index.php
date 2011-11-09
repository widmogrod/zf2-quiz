<?php
$env = include __DIR__ . '/../configs/env.config.php';
$host = strtolower(trim($_SERVER['SERVER_NAME']));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (isset($env[$host]) ? $env[$host] : 'production'));

// Ensure ZF is on the include path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(__DIR__ . '/../library'),
    realpath(__DIR__ . '/../library/ZendFramework/library'),
    get_include_path(),
)));

date_default_timezone_set('Europe/Warsaw');

require_once 'Zend/Loader/AutoloaderFactory.php';
Zend\Loader\AutoloaderFactory::factory(array('Zend\Loader\StandardAutoloader' => array()));

$appConfig = new Zend\Config\Config(include __DIR__ . '/../configs/application.config.php');

$moduleLoader = new Zend\Loader\ModuleAutoloader($appConfig['module_paths']);
$moduleLoader->register();

$moduleManager = new Zend\Module\AutoDependencyManager(
    $appConfig['modules'],
    new Zend\Module\ManagerOptions($appConfig['module_manager_options'])
);

// Create application, bootstrap, and run
$bootstrap      = new Zend\Mvc\Bootstrap($moduleManager);
$application    = new Zend\Mvc\Application;
$bootstrap->bootstrap($application);

/* @var $r \Zend\Mvc\Router\SimpleRouteStack */
$r = $application->getRouter();

/* @var $rq \Zend\Http\PhpEnvironment\Request */
$rq = $application->getRequest();
if ($rq->server()->get('SERVER_NAME') == 'tomatoe.pl')
{
    $uri = $rq->uri();
    $path = $uri->getPath();
    $path = str_replace($path, '/programista/quiz/public/', '/');
    $uri->setPath($path);
}

//echo '<pre>';
//print_r($moduleManager->getMergedConfig(false));
//echo '</pre>';

$application->run()->send();
