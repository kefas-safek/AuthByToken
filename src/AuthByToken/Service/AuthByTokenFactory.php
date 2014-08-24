<?php

namespace AuthByToken\Service;

class AuthByTokenFactory implements \Zend\ServiceManager\FactoryInterface {

    /**
     * @param \Zend\ServiceManager\ServiceLocatorInterface $serviceLocator
     * @return type
     */
    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator) {
        $request = $serviceLocator->get('Request');
        $documentManager = $serviceLocator->get('doctrine.documentmanager.odm_default');
        $adapter = new \AuthByToken\Adapter\AuthByTokenAdapter($request,$documentManager);
        return \AuthByToken\Auth\AuthByToken::create(new \Zend\Authentication\Storage\Session(), $adapter);
    }
}