<?php
/**
 * Bootstrap
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
include_once __DIR__ . '/CreateClassMap.php';

$base     = substr(__DIR__, 0, strlen(__DIR__) - 5);
include $base . '/vendor/autoload.php';

$results  = createClassMap($base . '/Source/Handler', 'Molajo\\Render\\Adapter\\');
$classmap['Molajo\\Render\\Adapter'] = $base . '/Source/Adapter.php';
ksort($classmap);

spl_autoload_register(
    function ($class) use ($classmap) {
        if (array_key_exists($class, $classmap)) {
            require_once $classmap[$class];
        }
    }
);
