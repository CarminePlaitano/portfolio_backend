<?php

use App\Kernel;
use Symfony\Component\HttpFoundation\Request;

require_once dirname(__DIR__).'/vendor/autoload.php';

try {
    // Provide defaults if env vars are not set by host
    $env = $_SERVER['APP_ENV'] ?? 'prod';
    $debug = (bool) ($_SERVER['APP_DEBUG'] ?? 0);

    $kernel = new Kernel($env, $debug);

    $request = Request::createFromGlobals();
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);
} catch (Throwable $e) {
    // Show error in browser for debugging
    http_response_code(500);
    echo '<h1>Exception while booting Symfony kernel</h1>';
    echo '<pre>'.htmlspecialchars((string) $e).'</pre>';

    // Log it as well
    error_log((string) $e);
    exit(1);
}
