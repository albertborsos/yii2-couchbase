<?php

namespace albertborsos\couchbase;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;

/**
 * Connection represents a connection to the Couchbase cluster.
 */
class Connection extends Component
{
    /**
     * @event Event an event that is triggered after a DB connection is established
     */
    const EVENT_AFTER_OPEN = 'afterOpen';

    /**
     * @var string host:port,...
     *
     * passed to couchbase constructor
     *
     */
    public $dsn;

    /**
     * @var string cluster username
     * This is only needed for management.
     */
    public $username = '';

    /**
     * @var string cluster password
     * This is only needed for management.
     */
    public $password = '';

    /**
     * @var string name of the bucket to use by default.
     * If this field left blank, the default bucket will be used.
     */
    public $defaultBucketName = '';

    /**
     * @var string password for the default bucket.
     */
    public $defaultBucketPassword = '';

    /**
     * @var \CouchbaseCluster couchbase cluster instance
     */
    public $cluster;

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
    public $bucketTimeouts = [];

    /**
     * Bucket[] list of buckets
     */
    private $_buckets = [];

    /**
     * Returns the Couchbase bucket with the given name.
     * @param string|null $name bucket name, if null default one will be used.
     * @param string $password bucket password, ignored if using default
     * @param boolean $refresh whether to reestablish the database connection even if it is found in the cache.
     * @return Bucket bucket instance.
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function getBucket($name = null, $password = '', $refresh = false)
    {
        if ($name === null) {
            $name = $this->defaultBucketName;
            $password = $this->defaultBucketPassword;
        }
        if ($refresh || !array_key_exists($name, $this->_buckets)) {
            $token = 'Opening Couchbase bucket: ' . $name;
            Yii::beginProfile($token, __METHOD__);
            $this->_buckets[$name] = $this->openBucket($name, $password);
            Yii::endProfile($token, __METHOD__);
        }

        return $this->_buckets[$name];
    }

    /**
     * Opens the bucket with given name.
     * @param string $name bucket name.
     * @param string $password bucket password.
     * @return Bucket bucket instance.
     * @throws Exception
     * @throws InvalidConfigException
     */
    protected function openBucket($name, $password = '')
    {
        $this->open();

        return Yii::createObject([
            'class' => Bucket::class,
            'bucket' => $this->cluster->openBucket($name, $password),
            'timeouts' => $this->bucketTimeouts,
        ]);
    }

    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function open()
    {
        if ($this->cluster === null) {
            if (empty($this->dsn)) {
                throw new InvalidConfigException($this->className() . '::dsn cannot be empty.');
            }

            $token = 'Opening Couchbase connection: ' . $this->dsn;
            try {
                Yii::debug($token, __METHOD__);
                Yii::beginProfile($token, __METHOD__);

                $authenticator = new \Couchbase\PasswordAuthenticator();
                $authenticator->username($this->username)->password($this->password);

                $this->cluster = new \CouchbaseCluster($this->dsn);
                $this->cluster->authenticate($authenticator);

                $this->initConnection();
                Yii::endProfile($token, __METHOD__);
            } catch (\Exception $e) {
                Yii::endProfile($token, __METHOD__);
                throw new Exception($e->getMessage(), (int)$e->getCode(), $e);
            }

            ini_set('couchbase.decoder.json_arrays', true);
            ini_set('couchbase.encoder.format', 'php');
        }
    }

    public function close()
    {
        if ($this->cluster !== null) {
            Yii::debug('Closing Couchbase connection: ' . $this->dsn, __METHOD__);
            $this->cluster = null;
            $this->_buckets = [];
        }
    }

    /**
     * Initializes the DB connection.
     * This method is invoked right after the DB connection is established.
     * The default implementation triggers an [[EVENT_AFTER_OPEN]] event.
     */
    protected function initConnection()
    {
        $this->trigger(self::EVENT_AFTER_OPEN);
    }
}
