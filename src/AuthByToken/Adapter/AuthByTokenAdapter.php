<?php

namespace AuthByToken\Adapter;

class AuthByTokenAdapter implements \Zend\Authentication\Adapter\AdapterInterface {

    /**
     * @var string 
     */
    protected $tokenParam = '_t';

    /**
     * @var string 
     */
    protected $secretParam = '_s';

    /**
     * @var \Zend\Http\Request 
     */
    protected $request;

    /**
     * @var \Doctrine\ODM\MongoDB\DocumentManager 
     */
    protected $documentManager;

    /**
     * @var string 
     */
    protected $tokenDocumentClass = '\Application\Document\Token';

    /**
     * @var string 
     */
    protected $applicationDocumentClass = '\Application\Document\Application';

    /**
     * @param \Zend\Http\Request $request
     * @param array $config
     */
    public function __construct(\Zend\Http\Request $request, \Doctrine\ODM\MongoDB\DocumentManager $documentManager, $config = null) {
        $this->setDocumentManager($documentManager);
        $this->setRequest($request);
        if (is_array($config)) {
            $this->setConfig($config);
        }
    }

    /**
     * @param array $config
     */
    public function setConfig($config) {
        if (is_array($config)) {
            if (array_key_exists('tokenParam', $config) && is_string($config['tokenParam'])) {
                $this->setTokenParam($config['tokenParam']);
            }
            if (array_key_exists('secretParam', $config) && is_string($config['secretParam'])) {
                $this->setSecretParam($config['secretParam']);
            }
            if (array_key_exists('tokenDocumentClass', $config) && is_string($config['tokenDocumentClass'])) {
                $this->setTokenDocumentClass($config['tokenDocumentClass']);
            }
            if (array_key_exists('applicationDocumentClass', $config) && is_string($config['applicationDocumentClass'])) {
                $this->setApplicationDocumentClass($config['applicationDocumentClass']);
            }
        }
    }

    /**
     * @return \Zend\Authentication\Result
     */
    public function authenticate() {
        if (!$this->getDocumentManager()) {
            throw new \Exception('Document manager must be setted before you call authenticate!');
        }

        if (!$this->getTokenFromParam() || !$this->getApplication()) {
            return new \Zend\Authentication\Result(\Zend\Authentication\Result::FAILURE_CREDENTIAL_INVALID, false);
        }

        $result = $this->getIdentityByToken();

        if (!$result) {
            return new \Zend\Authentication\Result(\Zend\Authentication\Result::FAILURE, false);
        }

        return new \Zend\Authentication\Result(\Zend\Authentication\Result::SUCCESS, $result);
    }

    /**
     * @return mixed
     */
    public function getApplication() {
        $applicationRepository = $this->getDocumentManager()->getRepository($this->getApplicationDocumentClass());

        if (!$applicationRepository instanceof \AuthByToken\Repository\ApplicationRepositoryInterface) {
            throw new \Exception('Repository must be an instance of \AuthByToken\Repository\ApplicationRepositoryInterface!');
        }

        return $applicationRepository->getApplication($this->getServer(), $this->getSecret());
    }

    /**
     * @return mixed
     */
    public function getServer() {
        return $this->getRequest()->getServer('SERVER_NAME');
    }

    /**
     * @return mixed
     */
    public function getSecret() {
        return $this->getRequest()->isPost() ? $this->request->getPost($this->secretParam, null) : false;
    }

    /**
     * @return \Zend\Http\Request 
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * @param \Zend\Http\Request $request
     */
    public function setRequest(\Zend\Http\Request $request) {
        $this->request = $request;
    }

    /**
     * @return mixed|false
     */
    public function getIdentityByToken() {
        $application = $this->getApplication();
        $token = $this->getTokenFromParam();

        $tokenRepository = $this->getDocumentManager()->getRepository($this->getTokenDocumentClass());

        if (!$tokenRepository instanceof \AuthByToken\Repository\TokenRepositoryInterface) {
            throw new \Exception('token repository must be an instance of \AuthByToken\Repository\TokenRepositoryInterface');
        }

        $tokenDocument = $tokenRepository->getActiveToken($token, $application->getId());

//        $tokenDocument = $this->getDocumentManager()->findOneBy(
//                array(
//                    'token' => $token,
//                    'expirationDate' => array('$gte' => new \DateTime()),
//                    'application.$id' => new \MongoId($application->getId())
//                )
//        );
        if (!$tokenDocument) {
            return false;
        }

        if (!$tokenDocument instanceof \AuthByToken\Document\TokenInterface) {
            throw new \Exception('token repository must be an instance of \AuthByToken\Document\TokenIterface');
        }

        $userDocument = $tokenDocument->getUser();

        if (!$userDocument) {
            return false;
        }

        if (!$userDocument instanceof \AuthByToken\Document\ToArrayInterface) {
            throw new \Exception('token repository must be an instance of \AuthByToken\Document\ToArrayIterface');
        }

        return $userDocument->toArray();
    }

    /**
     * @return string|false|null
     */
    public function getTokenFromParam() {
        return $this->getRequest()->getQuery($this->tokenParam, null);
    }

    /**
     * @return string
     */
    public function getTokenParam() {
        return $this->tokenParam;
    }

    /**
     * @return string
     */
    public function getSecretParam() {
        return $this->secretParam;
    }

    /**
     * @return string
     */
    public function getTokenDocumentClass() {
        return $this->tokenDocumentClass;
    }

    /**
     * @return string
     */
    public function getApplicationDocumentClass() {
        return $this->applicationDocumentClass;
    }

    /**
     * @return string
     */
    public function getUserDocumentClass() {
        return $this->userDocumentClass;
    }

    /**
     * @return \Doctrine\ODM\MongoDB\DocumentManager
     */
    public function getDocumentManager() {
        return $this->documentManager;
    }

    /**
     * @return string
     */
    public function getLogin() {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @param string $login
     */
    public function setLogin($login) {
        $this->login = $login;
    }

    /**
     * @param string $password
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * @param \Doctrine\ODM\MongoDB\DocumentManager $documentManager
     * @return \AuthByToken\Adapter\AuthByTokenAdapter
     */
    public function setDocumentManager(\Doctrine\ODM\MongoDB\DocumentManager $documentManager) {
        $this->documentManager = $documentManager;
        return $this;
    }

    /**
     * @param string $userDocumentClass
     * @return \AuthByToken\Adapter\AuthByTokenAdapter
     */
    public function setUserDocumentClass($userDocumentClass) {
        $this->userDocumentClass = $userDocumentClass;
        return $this;
    }

    /**
     * @param string $tokenParam
     * @return \AuthByToken\Adapter\AuthByTokenAdapter
     */
    public function setTokenParam($tokenParam) {
        $this->tokenParam = $tokenParam;
        return $this;
    }

    /**
     * @param string $secretParam
     * @return \AuthByToken\Adapter\AuthByTokenAdapter
     */
    public function setSecretParam($secretParam) {
        $this->secretParam = $secretParam;
        return $this;
    }

    /**
     * @param string $tokenDocumentClass
     * @return \AuthByToken\Adapter\AuthByTokenAdapter
     */
    public function setTokenDocumentClass($tokenDocumentClass) {
        $this->tokenDocumentClass = $tokenDocumentClass;
        return $this;
    }

    /**
     * @param string $ApplicationDocumentClass
     * @return \AuthByToken\Adapter\AuthByTokenAdapter
     */
    public function setApplicationDocumentClass($ApplicationDocumentClass) {
        $this->applicationDocumentClass = $ApplicationDocumentClass;
        return $this;
    }

    public static function create($request, $documentManager, $config) {
        return new static($request, $documentManager, $config);
    }

}
