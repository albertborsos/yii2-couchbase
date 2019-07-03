<?php

namespace albertborsos\couchbase\repositories;

use albertborsos\couchbase\Connection;
use albertborsos\couchbase\Exception;
use albertborsos\ddd\interfaces\EntityInterface;
use albertborsos\ddd\factories\EntityFactory;
use albertborsos\ddd\repositories\AbstractCacheRepository;
use mito\cms\sitemap\domains\layout\Layout;
use yii\base\Model;
use yii\di\Instance;

abstract class AbstractCouchbaseCacheRepository extends AbstractCacheRepository
{
    /**
     * @var string|Connection
     */
    protected $couchbase = 'couchbase';

    /**
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->couchbase = Instance::ensure($this->couchbase, Connection::class);
    }

    /**
     * @param $key
     * @return mixed|null
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function get($key)
    {
        $couchbaseModel = $this->couchbase->getBucket()->get($key);

        if (empty($couchbaseModel)) {
            return null;
        }

        return $couchbaseModel->value;
    }

    /**
     * @param $key
     * @param $value
     * @param null $duration
     * @param null $dependency
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function set($key, $value, $duration = null, $dependency = null)
    {
        return $this->couchbase->getBucket()->set($key, $value, $duration ? ['expiry' => $duration] : []);
    }

    /**
     * @param $key
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function delete($key)
    {
        return $this->couchbase->getBucket()->delete($key);
    }

    /**
     * @param $id
     * @return EntityInterface|void|null
     * @throws \yii\base\InvalidConfigException
     * @throws Exception
     */
    public function findById($id)
    {
        $model = EntityFactory::create(static::entityModelClass(), ['id' => $id]);

        return $this->findEntityByKey($model->getCacheKey());
    }

    /**
     * @param EntityInterface $model
     * @return EntityInterface|null
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function findByEntity(EntityInterface $model): ?EntityInterface
    {
        return $this->findEntityByKey($model->getCacheKey());
    }

    /**
     * @param string $key
     * @return EntityInterface|null
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function findEntityByKey(string $key): ?EntityInterface
    {
        $couchbaseModel = $this->couchbase->getBucket()->get($key);

        if ($couchbaseModel === null) {
            return null;
        }

        return EntityFactory::create(static::entityModelClass(), (array)$couchbaseModel->value);
    }

    /**
     * @param EntityInterface|Model $model
     * @param null $duration
     * @param null $dependency
     * @throws Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function storeEntity(EntityInterface $model, $duration = null, $dependency = null)
    {
        return $this->set($model->getCacheKey(), $model->attributes, $duration, $dependency);
    }
}
