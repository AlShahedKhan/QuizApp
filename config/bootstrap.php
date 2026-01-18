<?php
$APP_CONFIG = require __DIR__ . "/config.php";

if (!function_exists("config")) {
  function config(?string $key = null, $default = null)
  {
    global $APP_CONFIG;
    if ($key === null) {
      return $APP_CONFIG;
    }
    $segments = explode(".", $key);
    $value = $APP_CONFIG;
    foreach ($segments as $segment) {
      if (!is_array($value) || !array_key_exists($segment, $value)) {
        return $default;
      }
      $value = $value[$segment];
    }
    return $value;
  }
}

date_default_timezone_set(config("app.timezone", "UTC"));

if (session_status() === PHP_SESSION_NONE) {
  $secure = !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off";
  session_set_cookie_params([
    "httponly" => true,
    "secure" => $secure,
    "samesite" => "Lax",
  ]);
  session_start();
}

require_once __DIR__ . "/db.php";
require_once __DIR__ . "/helpers.php";
