<?php

namespace Inventory;

use Inventory\Model\Computer;
use Inventory\Model\ComputerTable;
use Zend\Db\Adapter\Adapter;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Database_RO' => function($sm) {
                    $config = $sm->get('config');
                    $config['database']['username'] = $config['database']['accounts']['read-only']['username'];
                    $config['database']['password'] = $config['database']['accounts']['read-only']['password'];
                    return new Adapter($config['database']);
                },
                'Database_RW' => function($sm) {
                    $config = $sm->get('config');
                    $config['database']['username'] = $config['database']['accounts']['admin']['username'];
                    $config['database']['password'] = $config['database']['accounts']['admin']['password'];
                    return new Adapter($config['database']);
                },
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}
