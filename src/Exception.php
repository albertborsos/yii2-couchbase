<?php

namespace albertborsos\couchbase;

class Exception extends \yii\base\Exception
{
    public function getName()
    {
        return 'Couchbase Exception';
    }
}
