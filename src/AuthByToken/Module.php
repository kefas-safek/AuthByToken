<?php

namespace AuthByToken;

use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\InitProviderInterface;
use Zend\ModuleManager\ModuleManagerInterface;
use Zend\Loader\AutoloaderFactory;
use Zend\Loader\StandardAutoloader;

class Module implements
BootstrapListenerInterface, AutoloaderProviderInterface, ConfigProviderInterface, ServiceProviderInterface, InitProviderInterface {

    public function onBootstrap(EventInterface $e) {
        
    }

    public function init(ModuleManagerInterface $manager) {
        
    }

    public function getServiceConfig() {
        return array(
            'factories' => array(
                'authByToken' => new \AuthByToken\Service\AuthByTokenFactory(),
            )
        );
    }

    public function getConfig() {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getAutoloaderConfig() {
        return array(
            AutoloaderFactory::STANDARD_AUTOLOADER => array(
                StandardAutoloader::LOAD_NS => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

}
