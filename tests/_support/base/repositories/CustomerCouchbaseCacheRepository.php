<?php

namespace albertborsos\couchbase\tests\support\base\repositories;

use albertborsos\couchbase\repositories\AbstractCouchbaseCacheRepository;
use albertborsos\couchbase\tests\support\base\models\Customer;

class CustomerCouchbaseCacheRepository extends AbstractCouchbaseCacheRepository
{
    /**
     * @return string
     */
    protected static function entityModelClass(): string
    {
        return Customer::class;
    }
}
