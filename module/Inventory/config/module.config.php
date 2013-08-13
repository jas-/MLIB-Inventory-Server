<?php
return array(
	'database' => array(
		'host' => 'localhost',
		'name' => 'inventory_v01',
		'driver' => 'Pdo_Mysql',
		'accounts' => array(
			'admin' => array(
				'username' => 'Administrator',
				'password' => 'password',
			),
			'read-only' => array(
				'username' => 'Read_Only',
				'password' => 'password',
			),
		),
	),
	'controllers' => array(
        'invokables' => array(
            'Inventory\Controller\Inventory' => 'Inventory\Controller\InventoryController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'inventory' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/inventory[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'Inventory\Controller\Inventory',
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
		'strategies' => array(
			'ViewJsonStrategy',
		),
        'template_path_stack' => array(
            'album' => __DIR__ . '/../view',
        ),
    ),
);