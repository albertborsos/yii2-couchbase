<?php

namespace albertborsos\couchbase\tests\support\base\models;

use albertborsos\ddd\models\AbstractEntity;

class Customer extends AbstractEntity
{
    public $id;
    public $name;

    /**
     * Mapping of property keys to entity classnames.
     *
     * @return array
     */
    public function relationMapping(): array
    {
        return [];
    }
}
