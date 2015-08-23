<?php
/**
 * Bootstrap
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
$base = substr(__DIR__, 0, strlen(__DIR__) - 5);
if (function_exists('CreateClassMap')) {
} else {
    include_once __DIR__ . '/CreateClassMap.php';
}
include_once $base . '/vendor/autoload.php';

$classmap = array();
$results  = createClassMap($base . '/Traits', 'Molajo\\Query\\');
$classmap = array_merge($classmap, $results);

$results  = createClassMap($base . '/Controller', 'Molajo\\Controller\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Model', 'Molajo\\Model\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Source/Adapter', 'Molajo\\Query\\Adapter\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Source/Builder', 'Molajo\\Query\\Builder\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Source/Data/Adapter', 'Molajo\\Data\\Adapter\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Source/Data', 'Molajo\\Data\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Source/Model', 'Molajo\\Query\\Model\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Source', 'Molajo\\Query\\');
$classmap = array_merge($classmap, $results);

$results  = createClassMap($base . '/.dev/Mocks/Controller', 'Molajo\\Controller\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/.dev/Mocks/Database', 'Molajo\\Data\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/.dev/Mocks/Fieldhandler', 'Molajo\\Fieldhandler\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/.dev/Mocks/Query', 'Molajo\\Query\\');
$classmap = array_merge($classmap, $results);

ksort($classmap);

spl_autoload_register(
    function ($class) use ($classmap) {
        if (array_key_exists($class, $classmap)) {
            require_once $classmap[$class];
        }
    }
);


//include_once $base . '/.dev/Reflection.php';
