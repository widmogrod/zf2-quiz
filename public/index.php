<?php
$env = include __DIR__ . '/../configs/env.config.php';
$host = strtolower(trim($_SERVER['SERVER_NAME']));

error_reporting(E_ALL);
ini_set('display_errors', true);

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (isset($env['hostname'][$host]) ? $env['hostname'][$host] : 'production'));

defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(__DIR__ . '/..'));

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

$moduleManager = new Zend\Module\Manager($appConfig['modules']);
$listenerOptions = new Zend\Module\Listener\ListenerOptions($appConfig['module_listener_options']);
$listenerOptions->setApplicationEnvironment(APPLICATION_ENV);
$moduleManager->setDefaultListenerOptions($listenerOptions);
//$moduleManager->getConfigListener()->addConfigGlobPath(dirname(__DIR__) . '/config/autoload/*.config.php');
$moduleManager->loadModules();

// Create application, bootstrap, and run
$bootstrap      = new Zend\Mvc\Bootstrap($moduleManager->getMergedConfig());
$application    = new Zend\Mvc\Application;
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

