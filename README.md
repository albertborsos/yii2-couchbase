[![Build Status](https://travis-ci.org/albertborsos/yii2-couchbase.svg?branch=master)](https://travis-ci.org/albertborsos/yii2-couchbase)
[![Coverage Status](https://coveralls.io/repos/github/albertborsos/yii2-couchbase/badge.svg)](https://coveralls.io/github/albertborsos/yii2-couchbase)

Yii 2.0 Couchbase Component
===========================
Couchbase component for Yii 2.0 Framework

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist albertborsos/yii2-couchbase "*"
```

or add

```
"albertborsos/yii2-couchbase": "*"
```

to the require section of your `composer.json` file.


Usage
-----

Create `couchbase.ini`:
```ini
extension=couchbase.so
; priority=30
```

To install couchbase to docker, add these lines to your `Dockerfile.dev`:

```dockerfile
#install couchbase extension
RUN curl -O http://packages.couchbase.com/releases/couchbase-release/couchbase-release-1.0-6-amd64.deb
RUN dpkg -i couchbase-release-1.0-6-amd64.deb
RUN apt-get update && \
	    apt-get install -y --no-install-recommends \
	    libcouchbase-dev build-essential php-pear php-dev zlib1g-dev
RUN pecl install couchbase
ADD couchbase.ini /etc/php/7.3/mods-available/couchbase.ini
RUN phpenmod couchbase
```

For development use the following docker-compose image configuration

```yaml
    cb:
        image: couchbase/server
        volumes:
            - ~/couchbase/cb:/opt/couchbase/var
        ports:
            - 8091
            - 11210
```

Then you have to configure the component:

```php
return  [
    ...
    'components' => [
        ...
        'couchbase' => [
            'class' => \albertborsos\couchbase\Connection::class,
            'dsn' => 'cb',
            'username' => 'frontend',
            'password' => 'frontend',
            'defaultBucketName' => 'frontend',
            'defaultBucketPassword' => 'frontend',
        ],
        ...
    ],
    ...
];
```
