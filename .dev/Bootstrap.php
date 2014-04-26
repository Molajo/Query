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

$classmap = array();
$results  = createClassMap($base . '/Controller', 'Molajo\\Controller\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Model', 'Molajo\\Model\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Resource/Adapter', 'Molajo\\Resource\\Adapter\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Resource/Api', 'Molajo\\Resource\\Api\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Resource/Configuration', 'Molajo\\Resource\\Configuration\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Resource/Factory', 'Molajo\\Resource\\Factory\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Source/Adapter', 'Molajo\\Query\\Adapter\\');
$classmap = array_merge($classmap, $results);
$classmap['Molajo\\Query\\Driver'] = $base . '/Source/Driver.php';

ksort($classmap);

spl_autoload_register(
    function ($class) use ($classmap) {
        if (array_key_exists($class, $classmap)) {
            require_once $classmap[$class];
        }
    }
);
