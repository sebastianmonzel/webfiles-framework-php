## webfiles-framework-php

https://www.webfiles-framework.org/

[![Join the chat at https://gitter.im/sebastianmonzel/webfiles-framework-php][Gitter Chat image]][Gitter Chat link]
[![Build status][Travis Develop image]][Travis Develop link]
[![Code Climate][Codeclimate image]][Codeclimate link]
[![Test Coverage][Codeclimate coverage image]][Codeclimate coverage link]

[Gitter Chat image]: https://badges.gitter.im/sebastianmonzel/webfiles-framework-php.svg
[Gitter Chat link]: https://gitter.im/sebastianmonzel/webfiles-framework-php?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge

[Travis Develop image]: https://img.shields.io/travis/sebastianmonzel/webfiles-framework-php/develop.svg?style=flat-square
[Travis Develop link]: https://travis-ci.org/sebastianmonzel/webfiles-framework-php

[Codeclimate image]: https://codeclimate.com/github/sebastianmonzel/webfiles-framework-php/badges/gpa.svg
[Codeclimate link]: https://codeclimate.com/github/sebastianmonzel/webfiles-framework-php

[Codeclimate coverage image]: https://codeclimate.com/github/sebastianmonzel/webfiles-framework-php/badges/coverage.svg
[Codeclimate coverage link]: https://codeclimate.com/github/sebastianmonzel/webfiles-framework-php/coverage

Api-Documentation: http://sebastianmonzel.github.io/webfiles-framework-php-api/<br />
Documentation: http://sebastianmonzel.github.io/webfiles-framework-php-doc/ ([github-repo](https://github.com/sebastianmonzel/webfiles-framework-php-doc))<br />
Packagist: https://packagist.org/packages/webfiles-framework/framework

Access and describe data in a generalized way. Interact with remote data.<br />
developer & contact: Sebastian Monzel (mail@sebastianmonzel.de)


### What is webfiles framework for?
The purpose of webfiles framework is to provide a lightweight libary to interoperate with data on a standarized way: the webfiles.

The webfiles framework generalizes data access to database system, to file system and accessing data on the remote site. Through an webfile definition and the standarized api you can access the different systems in the same way.


### First Steps
 - add webfiles-framework as dependency `webfiles-framework/framework` to your `composer.json`
 - define your datastucture as webfile definition
 - save and read data in database or filesystem via the datastore api


### Sample use cases

 - [Define your first webfile definition](#define-your-first-webfile-definition)
 - [Store a webfile in a datastore](#store-a-webfile-in-a-datastore) - database tables will be created on the fly according to the webfiles defintion.
 - [Read from DirectoryDatastore](#read-from-directorydatastore)
 - [Read from DatabaseDatastore (actually mysql only)](#read-from-databasedatastore-actually-mysql-only)
 - [Transfer data from one datastore to another](#transfer-data-from-one-datastore-to-another)
 - [Read from RemoteDatastore](#read-from-remotedatastore) - a remote datastore is a datastore which lives on an other server in the web 

#### Define your first webfile definition
```php

use webfilesframework\core\datasystem\file\format\MWebfile;

class Contact extends MWebfile
{

    private $m_sFirstname; // attributes has to be in the given scheme - all attributes with "m_" as prefix gets persisted - "s" defines the type of the attribute (string)
    private $m_sLastname;
    private $m_sCity;

    
    public function setFirstname($m_sFirstname)
    {
        $this->m_sFirstname = $m_sFirstname;
    }
    
    public function getFirstname()
    {
        return $this->m_sFirstname;
    }

    public function setLastname($m_sLastname)
    {
        $this->m_sLastname = $m_sLastname;
    }
    
    public function getFirstname()
    {
        return $this->m_sFirstname;
    }

    public function setCity($m_sCity)
    {
        $this->m_sCity = $m_sCity;
    }
    
    public function getCity($m_sCity)
    {
        $this->m_sCity = $m_sCity;
    }

}
```

#### Store a webfile in a datastore:
via directory:
```php
<?php
use webfilesframework\core\datastore\types\directory\MDirectoryDatastore;
use webfilesframework\core\datasystem\file\system\MDirectory;

$directoryDatastore = new MDirectoryDatastore(new MDirectory("yourDirectoryToStoreWebfiles"));
$contact = new Contact();
$contact->setId(1);
$contact->setFirstname("Sebastian");
$contact->setLastname("Monzel");

$directoryDatastore->storeWebfile($contact);
```

or via database:
```php
<?php
use webfilesframework\core\datastore\types\directory\MDirectoryDatastore;
use webfilesframework\core\datasystem\file\system\MDirectory;

$databaseDatastore = new MDatabaseDatastore(
                          new MDatabaseConnection(
                              "localhost",
                              "yourDatabaseToStoreWebfiles",
                              "tablePrefixInDatabaseForActualDatastore", // will add this string before every created table name 
                              "username",
                              "password"
                              )
                      );
$contact = new Contact();
$contact->setFirstname("Sebastian");
$contact->setLastname("Monzel");

$databaseDatastore->storeWebfile($contact);
```

#### Read from DirectoryDatastore
```php
<?php
use webfilesframework\core\datastore\types\directory\MDirectoryDatastore;
use webfilesframework\core\datasystem\file\system\MDirectory;

$directoryDatastore = new MDirectoryDatastore(new MDirectory("yourDirectoryToStoreWebfiles"));
$directoryDatastore->getAllWebfiles();
```
#### Read from DatabaseDatastore (actually mysql only)
```php
<?php
use webfilesframework\core\datastore\types\database\MDatabaseDatastore;
use webfilesframework\core\datasystem\database\MDatabaseConnection;

$databaseDatastore = new MDatabaseDatastore(
    new MDatabaseConnection(
        "localhost",
        "yourDatabaseToStoreWebfiles",
        "tablePrefixInDatabaseForActualDatastore", // will add this string before every created table name 
        "username",
        "password"
        )
);
$databaseDatastore->getAllWebfiles();

```

#### Search by template in a datastore
TODO

#### Transfer data from one datastore to another
```php
<?php
use webfilesframework\core\datastore\types\directory\MDirectoryDatastore;
use webfilesframework\core\datasystem\file\system\MDirectory;
use webfilesframework\core\datastore\MDatastoreTransfer;

$source = new MDirectoryDatastore(new MDirectory("sourceDir"));
$target = new MDatabaseDatastore(new MDatabaseConnection("localhost","wonderfulDatabasename","mytableprefix","myuser","mypassword"));

$datastoreTransfer = new MDatastoreTransfer($source, $target);
$datastoreTransfer->transfer();

```
#### Read from RemoteDatastore
You can make a datastore accessible from remote via http(s). `MRemoteDatastoreEndpoint` encapsulates the datastore to make
it accessible. On the other site you can use `MRemoteDatastore` to access the encapsulated datastore. 

*Serverside to provide access to the datastore:*
```php
<?php
use webfilesframework\core\datastore\types\remote\MRemoteDatastoreEndpoint; 
use webfilesframework\core\datastore\types\directory\MDirectoryDatastore;
use webfilesframework\core\datasystem\file\system\MDirectory;

$remoteDatastoreEndpoint = new MRemoteDatastoreEndpoint(
    new MDirectoryDatastore(new MDirectory("localDirectory"))
);
$remoteDatastoreEndpoint->handleRemoteCall();
```

*Clientside to access the datastore:*
```php
<?php
use webfilesframework\core\datastore\types\remote\MRemoteDatastore;

// url on which the method $remoteDatastoreEndpoint->handleRemoteCall(); is reachable:
$datastoreUrl = "http://localhost:1234/datastore/";

$remoteDatastore = new MRemoteDatastore($datastoreUrl);
$remoteDatastore->getAllWebfiles();
```
