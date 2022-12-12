<?php
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Bundle\FrameworkBundle\Routing\AnnotatedRouteControllerLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Routing\Loader\AnnotationDirectoryLoader;
use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Outils\Utilitaires;
session_start();
require '../config/define.php';

/** @var ClassLoader $loader */
$loader = require  '../vendor/autoload.php';
AnnotationRegistry::registerLoader([$loader, 'loadClass']);
$loader = new AnnotationDirectoryLoader(
    new FileLocator('/var/www/html/src/Controller/'),
    new AnnotatedRouteControllerLoader(
        new AnnotationReader()
    )
);

$routes = $loader->load('/var/www/html/src/Controller/');
$context = new RequestContext();
$context->fromRequest(Request::createFromGlobals());
$matcher = new UrlMatcher($routes, $context);
$parameters = $matcher->match($context->getPathInfo());
$controllerInfo = explode('::',$parameters['_controller']);
$controller = new $controllerInfo[0];
$action = $controllerInfo[1];
$controller->$action();
$estConnecte =Utilitaires::estConnecte();
