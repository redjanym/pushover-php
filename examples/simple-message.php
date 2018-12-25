<?php
/**
 * @author Redjan Ymeraj <ymerajr@yahoo.com>
 */

include "../vendor/autoload.php";
include "config.php";

$pushOver = new Pushover($appToken, $userKey);

$pushOver
    ->setTitle("Test title")
    ->setMessage('Test message')
;

$pushOver->send();

var_dump($pushOver);
var_dump($pushOver->getResponse());
