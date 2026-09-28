<?php

// Keep the lab's /LAMPAPI URLs when using PHP's local development server.
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
$routes = array(
    "/LAMPAPI/Login.php" => "Login.php",
    "/LAMPAPI/AddColor.php" => "AddColor.php",
    "/LAMPAPI/SearchColors.php" => "SearchColors.php"
);

if (isset($routes[$path]))
{
    require __DIR__ . "/../api/" . $routes[$path];
    return true;
}

return false;
