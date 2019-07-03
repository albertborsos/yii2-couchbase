<?php

namespace albertborsos\couchbase\tests\unit\repositories;

use albertborsos\couchbase\tests\support\base\models\Customer;
use albertborsos\couchbase\tests\support\base\repositories\CustomerCouchbaseCacheRepository;
use albertborsos\couchbase\tests\support\base\TestCase;
use albertborsos\ddd\interfaces\EntityInterface;

class CouchbaseCacheRepositoryTest extends TestCase
{
    public function setandGetDataProvider()
    {
        return [
            'string'  => ['key', 'value'],
            'integer' => ['key', 10],
            'array'   => ['key', ['string' => 'value', 'integer' => 10]],
            'model'   => ['key', new Customer(['id' => 1, 'name' => 'Albert Borsos'])],
        ];
    }

    public function entityDataProvider()
    {
        return [
            [new Customer(['id' => 1, 'name' => 'Albert Borsos'])],
        ];
    }

    /**
     * @dataProvider setandGetDataProvider
     * @throws \yii\base\InvalidConfigException
     * @throws \albertborsos\couchbase\Exception
     */
    public function testSetGetDeleteByKey($key, $value)
    {
        $repository = $this->mockRepository();
        $repository->set($key, $value);

        $this->assertEquals($value, $repository->get($key));

        $repository->delete($key);

        $this->assertNull($repository->get($key));
    }

    /**
     * @dataProvider entityDataProvider
     * @param EntityInterface $model
     * @throws \yii\base\InvalidConfigException
     * @throws \albertborsos\couchbase\Exception
     */
    public function testFindByEntity($model)
    {
        $repository = $this->mockRepository();
        $repository->storeEntity($model);

        $this->assertEquals($model, $repository->findEntityByKey($model->getCacheKey()));
        $this->assertEquals($model, $repository->findByEntity($model));

        $repository->delete($model->getCacheKey());

        $this->assertNull($repository->findEntityByKey($model->getCacheKey()));
        $this->assertNull($repository->findByEntity($model));
    }

    public function findByIdDataProvider()
    {
        return [
            'integer' => [1],
        ];
    }

    /**
     * @dataProvider findByIdDataProvider
     * @param $id
     * @throws \albertborsos\couchbase\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function testFindById($id)
    {
        $repository = $this->mockRepository();
        $model = new Customer(['id' => $id]);

        $repository->storeEntity($model);

        $this->assertEquals($model, $repository->findById($id));

        $repository->delete($model->getCacheKey());

        $this->assertNull($repository->findById($id));
    }

    /**
     * @return CustomerCouchbaseCacheRepository|object
     * @throws \yii\base\InvalidConfigException
     */
    private function mockRepository()
    {
        return \Yii::createObject(CustomerCouchbaseCacheRepository::class);
    }
}
