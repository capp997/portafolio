<?php
define("APP_DEBUG", false);
define("APP_NAME", "Portafolio V5");
define("APP_ENV", "local");

ini_set("session.cookie_httponly", 1);
ini_set("session.use_strict_mode", 1);
ini_set("session.cookie_secure", 0);

if (!APP_DEBUG) {
    error_reporting(0);
    ini_set("display_errors", 0);
} else {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);
}
?>
