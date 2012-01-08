<?php
$env = include __DIR__ . '/../config/env.config.php';
$host = strtolower(trim($_SERVER['SERVER_NAME']));

error_reporting(E_ALL);
ini_set('display_errors', true);

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (isset($env['hostname'][$host]) ? $env['hostname'][$host] : 'production'));

defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(__DIR__ . '/..'));

date_default_timezone_set('Europe/Warsaw');


chdir(dirname(__DIR__));
require_once (getenv('ZF2_PATH') ?: 'vendor/ZendFramework/library') . '/Zend/Loader/AutoloaderFactory.php';
Zend\Loader\AutoloaderFactory::factory(array('Zend\Loader\StandardAutoloader' => array()));

$appConfig = include __DIR__ . '/../config/application.config.php';

$listenerOptions  = new Zend\Module\Listener\ListenerOptions($appConfig['module_listener_options']);
$defaultListeners = new Zend\Module\Listener\DefaultListenerAggregate($listenerOptions);
//$defaultListeners->getConfigListener()->addConfigGlobPath('config/autoload/*.config.php');

$moduleManager = new Zend\Module\Manager($appConfig['modules']);
$moduleManager->events()->attachAggregate($defaultListeners);
$moduleManager->loadModules();

// Create application, bootstrap, and run
$bootstrap   = new Zend\Mvc\Bootstrap($defaultListeners->getConfigListener()->getMergedConfig());
$application = new Zend\Mvc\Application;
$bootstrap->bootstrap($application);

//echo '<pre>';
//print_r($_SERVER);

if (isset($env['baseUri'][APPLICATION_ENV])) {
//    define('BASE_URI', (isset($_SERVER['HTTPS']) ? 'https' : 'http'). '//'. $_SERVER['HTTP_HOST'] . '/' .ltrim($env['baseUri'][APPLICATION_ENV], '/'));
    define('BASE_URI', $env['baseUri'][APPLICATION_ENV]);
    /* @var $r \Zend\Mvc\Router\SimpleRouteStack */
    $r = $application->getRouter();
    $r->setBaseUrl($env['baseUri'][APPLICATION_ENV]);

    /* @var $rq \Zend\Http\PhpEnvironment\Request */
    $rq = $application->getRequest();
    $r->match($rq);
    $r->getRequestUri();
} else {
    define('BASE_URI', '/');
}

//if (APPLICATION_ENV == 'production')
//{
//    $uri = $rq->uri();
//    $path = $uri->getPath();
//    $path = str_replace($path, '/programista/quiz/public/', '/');
//    $uri->setPath($path);
//}

try
{
    /** @var $response \Zend\Http\Response */
    $response = $application->run();
    $response->send();
} catch (\Exception $e) {
    \Zend\Debug::dump($e->getMessage());
    \Zend\Debug::dump($e->getFile());
    \Zend\Debug::dump($e->getLine());
}

//echo '<pre>';
//print_r($moduleManager->getMergedConfig()->toArray());

