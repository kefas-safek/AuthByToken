<?php

namespace AuthByToken\Repository;

interface TokenRepositoryInterface{
    public function getActiveToken($token,$applicationId);
}

