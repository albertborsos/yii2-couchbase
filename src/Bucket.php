<?php

namespace albertborsos\couchbase;

use Yii;
use yii\base\BaseObject;

/**
 * Bucket represents a couchbase bucket
 */
class Bucket extends BaseObject
{
    /**
     * @var \CouchbaseBucket couchbase bucket instance
     */
    public $bucket;

    /**
     * @var array bucket timeouts
     * keys are one of the following:
     * operation
     * view
     * durability
     * http
     * config
     * configNode
     * htconfigIdle
     */
    public $timeouts = [];

    public function init()
    {
        foreach ($this->timeouts as $key => $value) {
            $prop = $key . (strrpos($key, 'Timeout') === strlen($key) - strlen('Timeout') ? '' : 'Timeout');
            $this->bucket->$prop = $value;
        }
        parent::init();
    }

    /**
     * @param $key
     * @return bool
     */
    public function getMasterServer($key)
    {
        if (!method_exists($this->bucket, 'mapKey')) {
            return false;
        }

        return $this->bucket->mapKey($key);
    }

    /**
     * @param string $ids key to return
     * @param array $options
     * @param mixed $default default value to return in case a key is not found
     * @return array|\Couchbase\Document|null
     * @throws Exception
     */
    public function get($ids, $options = [], $default = null)
    {
        $logToken = "Getting from couchbase: $ids";
        Yii::debug($logToken, __METHOD__);
        Yii::beginProfile($logToken, __METHOD__);
        try {
            $data = $this->bucket->get($ids, $options);
            Yii::endProfile($logToken, __METHOD__);
            return $data;
        } catch (\CouchbaseException $e) {
            Yii::endProfile($logToken, __METHOD__);
            $code = $e->getCode();
            if ($code === COUCHBASE_KEY_ENOENT) {
                return $default;
            }

            throw new Exception($e->getMessage(), 0, $e);
        }
    }

    /**
     * Add an item to the cache, but fail if it already exists.
     * @param $id
     * @param null $val
     * @param array $options
     * @return boolean true if the value did not exist, false otherwise
     * @throws Exception
     */
    public function add($id, $val = null, $options = [])
    {
        $duration = '';
        if (isset($options['expiry'])) {
            $duration = ' with expiry of ' . $options['expiry'] . ' seconds';
        }

        $logToken = "Inserting into couchbase$duration: $id";

        Yii::debug($logToken, __METHOD__);
        Yii::beginProfile($logToken, __METHOD__);

        try {
            $this->bucket->insert($id, $val, $options);
            Yii::endProfile($logToken, __METHOD__);
            return true;
        } catch (\CouchbaseException $e) {
            Yii::endProfile($logToken, __METHOD__);
            $code = $e->getCode();
            if ($code === COUCHBASE_KEY_EEXISTS) {
                return false;
            }

            throw new Exception($e->getMessage(), 0, $e);
        }
    }

    /**
     * @param $id
     * @param null $val
     * @param array $options
     * @throws Exception
     */
    public function set($id, $val = null, $options = [])
    {
        $duration = '';
        if (isset($options['expiry'])) {
            $duration = ' with expiry of ' . $options['expiry'] . ' seconds';
        }

        $logToken = "Saving into couchbase$duration: $id";

        Yii::debug($logToken, __METHOD__);
        Yii::beginProfile($logToken, __METHOD__);

        try {
            $this->bucket->upsert($id, $val, $options);
            Yii::endProfile($logToken, __METHOD__);
        } catch (\CouchbaseException $e) {
            Yii::endProfile($logToken, __METHOD__);
            throw new Exception($e->getMessage(), 0, $e);
        }
    }

    /**
     * @param $ids
     * @param array $options
     * @throws Exception
     */
    public function delete($ids, $options = [])
    {
        $logToken = "Deleting from couchbase: $ids";

        Yii::debug($logToken, __METHOD__);
        Yii::beginProfile($logToken, __METHOD__);

        try {
            $this->bucket->remove($ids, $options);
            Yii::endProfile($logToken, __METHOD__);
        } catch (\CouchbaseException $e) {
            Yii::endProfile($logToken, __METHOD__);
            $code = $e->getCode();
            if ($code === COUCHBASE_KEY_ENOENT) {
                // it doesn't exist, that's ok.
                return;
            }

            throw new Exception($e->getMessage(), 0, $e);
        }
    }

    /**
     * @throws Exception
     */
    public function flush()
    {
        $logToken = 'Flushing bucket';

        Yii::debug($logToken, __METHOD__);
        Yii::beginProfile($logToken, __METHOD__);

        try {
            $this->bucket->manager()->flush();
            Yii::endProfile($logToken, __METHOD__);
        } catch (\CouchbaseException $e) {
            Yii::endProfile($logToken, __METHOD__);
            throw new Exception($e->getMessage(), 0, $e);
        }
    }
}
