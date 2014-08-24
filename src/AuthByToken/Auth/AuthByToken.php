<?php

namespace AuthByToken\Auth;

class AuthByToken extends \Zend\Authentication\AuthenticationService{
    
    /**
     * @param \Zend\Authentication\Storage\StorageInterface $storage
     * @param \Zend\Authentication\Adapter\AdapterInterface $adapter
     * @return \Zend\Authentication\AuthenticationService
     */
    public static function create($storage, $adapter){
        return new static($storage,$adapter);
    }
    
    
    /**
     * Returns the persistent storage handler
     *
     * Session storage is used by default unless a different storage adapter has been set.
     *
     * @return \Zend\Authentication\Storage\StorageInterface
     */
    public function getStorage()
    {
        if (null === $this->storage) {
            $this->setStorage(new \Zend\Authentication\Storage\NonPersistent());
        }

        return $this->storage;
    }
}
