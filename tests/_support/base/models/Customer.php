<?php

namespace albertborsos\couchbase\tests\support\base\models;

use albertborsos\ddd\models\AbstractEntity;

class Customer extends AbstractEntity
{
    public $id;
    public $name;
}
