## webfiles-framework-php

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


Access and describe data in a structured way. Interact with remote data.
The fascination about webbed data.

developer & contact: Sebastian Monzel (mail@sebastianmonzel.de)

Api-Documentation: http://sebastianmonzel.github.io/webfiles-framework-php-api/<br />
Documentation: http://sebastianmonzel.github.io/webfiles-framework-doc/<br />
Packagist: https://packagist.org/packages/simpleserv/webfiles-framework

### What is webfiles framework for?
The webfiles framework manages access to database system (creates tables on the fly and 
read/write data in database) and to file system (read and write files).  
Through an webfile definition and the standarized api you can access the different systems
in the same way. Remote datastores enable you to access locally defined datastores via HTTP.

### First Steps
 - add webfiles-framework as dependency `webfiles-framework/framework` to your `composer.json`
 - define your datastucture as webfile definition
 - save and read data in database or filesystem via the datastore api


### Samples

#### Create your first webfile
```php

use webfilesframework\core\datasystem\file\format\MWebfile;

class Contact extends MWebfile
{

    private $m_sFirstname; // s defines the type of the attribute
    private $m_sLastname;
    private $m_sCity;

    public static $m__sClassName = __CLASS__;
    
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

#### Read from DirectoryDatastore
```php
<?php
use webfilesframework\core\datastore\types\directory\MDirectoryDatastore;
use webfilesframework\core\datasystem\file\system\MDirectory;

$directoryDatastore = new MDirectoryDatastore(new MDirectory("dir"));
$directoryDatastore->getWebfilesAsArray();
```
#### Read from DatabaseDatastore (actually mysql only)
```php
<?php
use webfilesframework\core\datastore\types\database\MDatabaseDatastore;
use webfilesframework\core\datasystem\database\MDatabaseConnection;

$databaseDatastore = new MDatabaseDatastore(
    new MDatabaseConnection(
        "localhost",
        "wonderfulDatabasename",
        "myTableprefix",
        "myuser",
        "mypassword"
        )
);
$databaseDatastore->getWebfilesAsArray();

```


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
You can make a datastore accessible from remote via http. `MRemoteDatastoreEndpoint` encapsulates the datastore to make
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
$remoteDatastore->getWebfilesAsArray();
```
