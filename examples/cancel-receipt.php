<?php
/**
 * @author Redjan Ymeraj <ymerajr@yahoo.com>
 */

include "../vendor/autoload.php";
include "config.php";

$pushOver = new Pushover($appToken);

$pushOver->cancelEmergencyPriority("rrdjk8ng68gzgt68f3hrfyq6tkax9v");

var_dump($pushOver);
var_dump($pushOver->getResponse());
