<?php
/**
 * Bootstrap
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
$base = substr(__DIR__, 0, strlen(__DIR__) - 5);
if (function_exists('CreateClassMap')) {
} else {
    include_once __DIR__ . '/CreateClassMap.php';
}
include_once $base . '/vendor/autoload.php';

$classmap = array();
$results  = createClassMap($base . '/Controller', 'Molajo\\Controller\\');
$classmap = array_merge($classmap, $results);

$results  = createClassMap($base . '/Source/Model', 'Molajo\\Query\\Model\\');
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
$results  = createClassMap($base . '/Source/Builder', 'Molajo\\Query\\Builder\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/Source/Model', 'Molajo\\Query\\Model\\');
$classmap = array_merge($classmap, $results);

$results  = createClassMap($base . '/Interfaces', 'CommonApi\\Query\\');
$classmap = array_merge($classmap, $results);

$classmap['Molajo\\Query\\ModelRegistryTrait'] = $base . '/Source/ModelRegistryTrait.php';
$classmap['Molajo\\Query\\QueryTrait']         = $base . '/Source/QueryTrait.php';
$classmap['Molajo\\Query\\QueryBuilder']       = $base . '/Source/QueryBuilder.php';
$classmap['Molajo\\Query\\QueryProxy']         = $base . '/Source/QueryProxy.php';

$results  = createClassMap($base . '/.dev/Mocks/Controller', 'Molajo\\Controller\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/.dev/Mocks/Database', 'Molajo\\Database\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/.dev/Mocks/Fieldhandler', 'Molajo\\Fieldhandler\\');
$classmap = array_merge($classmap, $results);
$results  = createClassMap($base . '/.dev/Mocks/Query', 'Molajo\\Query\\');
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
