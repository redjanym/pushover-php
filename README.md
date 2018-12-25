# pushover-php

PHP SDK(not official) for the pushover.net API

**Installation**

* Use composer:

```bash
composer require redjanym/pushover-php
```

* Download and include file:

Download the repository, unzip the files and require ```Pushover.php```

```php
include "path/to/Pushover.php";
```

**Usage**

The class Pushover.php is composed of several setter methods to provide information about the message to send like ```title```, ```message```, ```url``` etc and access different API endpoints like sending message, validate user, work with receipts and more.
In this step we assume you have already setup your account in Pushover and are in possession of the APP anf USER keys.

**Example**

Sending a simple message.

```php
$pushOver = new Pushover("YOUR_APP_TOKEN", "YOUR_USER_KEY");

$pushOver
    ->setTitle("Test title")
    ->setMessage('Test message')
;

$pushOver->send();

var_dump($pushOver->getResponse());
```

More examples are available in the [examples directory](examples/)
. Be sure to add your config keys into the ```config.php``` file.

**Having issues? Not receiving notifications?**

Be sure to have create the two environment variables described above and/or check the response value of the requests sent in Pushover

**To do**

Add support for ```attachments```.