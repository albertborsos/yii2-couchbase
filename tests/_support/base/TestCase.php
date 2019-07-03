<?php

namespace albertborsos\couchbase\tests\support\base;

use albertborsos\couchbase\Connection;
use Yii;
use yii\helpers\ArrayHelper;

abstract class TestCase extends \Codeception\PHPUnit\TestCase
{
    public static $params;

    /** @var Connection */
    protected $couchbase = 'couchbase';

    protected function setUp()
    {
        parent::setUp();
        $this->mockApplication();
    }

    protected function tearDown()
    {
        Yii::$app->couchbase->close();
        $this->destroyApplication();
    }

    /**
     * Populates Yii::$app with a new application
     * The application will be destroyed on tearDown() automatically.
     * @param array $config The application configuration, if needed
     * @param string $appClass name of the application class to create
     */
    protected function mockApplication($appClass = \yii\console\Application::class)
    {
        $config = require(dirname(__DIR__) . '/../config/main.php');

        new $appClass(ArrayHelper::merge([
            'id' => 'testapp',
            'basePath' => dirname(dirname(__DIR__)),
            'vendorPath' => $this->getVendorPath(),
            'runtimePath' => dirname(dirname(__DIR__)) . '/runtime',
        ], $config));
    }

    protected function getVendorPath()
    {
        $vendor = dirname(dirname(__DIR__)) . '/vendor';
        if (!is_dir($vendor)) {
            $vendor = dirname(dirname(dirname(dirname(__DIR__))));
        }
        return $vendor;
    }

    /**
     * Destroys application in Yii::$app by setting it to null.
     */
    protected function destroyApplication()
    {
        Yii::$app = null;
    }
}
