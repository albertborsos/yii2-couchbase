<?php

namespace albertborsos\couchbase\tests\support\base\repositories;

use albertborsos\couchbase\repositories\AbstractCouchbaseCacheRepository;
use albertborsos\couchbase\tests\support\base\models\Customer;
use yii\data\BaseDataProvider;

class CustomerCouchbaseCacheRepository extends AbstractCouchbaseCacheRepository
{
    protected $entityClass = Customer::class;

    /**
     * Creates data provider instance with search query applied
     *
     * @param $params
     * @param null $formName
     * @return BaseDataProvider
     */
    public function search($params, $formName = null): BaseDataProvider
    {
        // TODO: Implement search() method.
    }
}
