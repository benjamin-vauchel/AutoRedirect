<?php
/**
 * Array of plugin events for AutoRedirect package
 *
 * @package autoredirect
 * @subpackage build
 */
$events = array();

/* Note: These must not be existing System Events!

 * This example is not used by default in the build.
 * It shows how to add custom System Events
 * for your plugin. See the commented out plugin section
 * of built.transport.php */


$events['OnBeforeDocFormSave']= $modx->newObject('modPluginEvent');
$events['OnBeforeDocFormSave']->fromArray(array(
    'event' => 'OnBeforeDocFormSave',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);

$events['OnEmptyTrash']= $modx->newObject('modPluginEvent');
$events['OnEmptyTrash']->fromArray(array(
    'event' => 'OnEmptyTrash',
    'priority' => 0,
    'propertyset' => 0,
),'',true,true);

return $events;