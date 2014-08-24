<?php

namespace AuthByToken\Repository;

interface ApplicationRepositoryInterface{
    public function getApplication($server,$secret);
}

