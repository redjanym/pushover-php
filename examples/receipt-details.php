<?php
/**
 * @author Redjan Ymeraj <ymerajr@yahoo.com>
 */

include "../vendor/autoload.php";
include "config.php";

$pushOver = new Pushover($appToken, $userKey);

var_dump($pushOver->getReceipt("rg7o2t7n21giguwg9ezijn83v11e3z"));
var_dump($pushOver->getResponse());
