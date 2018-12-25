<?php
/**
 * @author Redjan Ymeraj <ymerajr@yahoo.com>
 */

include "../vendor/autoload.php";
include "config.php";

$pushOver = new Pushover($appToken);

$list = $pushOver->getSoundsList();

var_dump($pushOver);
var_dump($pushOver->getResponse());
var_dump($list);
