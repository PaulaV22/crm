<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Ejecutivo;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

return [
    'router' => [
        'routes' => [
            'ejecutivos' => [
                'type' => Literal::class,
                'options' => [
                    'route' => '/herramientas/ejecutivos',
                    'defaults' => [
                        'controller' => Controller\EjecutivoController::class,
                        'action' => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'buscar' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/seach',
                            'defaults' => [
                                'controller' => Controller\EjecutivoController::class,
                                'action' => 'search',
                            ],
                        ],
                    ],
                    'page' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/page[/:id[/:estado]]',
                            'defaults' => [
                                'controller' => Controller\EjecutivoController::class,
                                'action' => 'index',
                            ],
                            'constraints' => [
                                'id' => '[0-9]\d*',
                            ],
                        ],
                    ],
                    'agregar' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/add[/:id]',
                            'defaults' => [
                                'controller' => Controller\EjecutivoController::class,
                                'action' => 'add',
                            ],
                        ],
                    ],
                    'borrar' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/delete[/:id]',
                            'defaults' => [
                                'controller' => Controller\EjecutivoController::class,
                                'action' => 'delete',
                            ],
                            'constraints' => [
                                'id' => '[0-9]\d*',
                            ],
                        ],
                    ],
                     'editar' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/edit[/:id]',
                            'defaults' => [
                                'controller' => Controller\EjecutivoController::class,
                                'action' => 'edit',
                            ],
                            'constraints' => [
                                'id' => '[0-9]\d*',
                            ],
                        ],
                    ],
                    'buscar' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/seach',
                            'defaults' => [
                                'controller' => Controller\EjecutivoController::class,
                                'action' => 'search',
                            ],
                        ],
                    ],
                    'inactivos' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/inactivos[/:id]',
                            'defaults' => [
                                'controller' => Controller\EjecutivoInactivoController::class,
                                'action' => 'index',
                            ],
                        ],
                        'constraints' => [
                            'id' => '[0-9]\d*',
                        ],
                    ],
                    'activar' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/activar[/:id]',
                            'defaults' => [
                                'controller' => Controller\EjecutivoInactivoController::class,
                                'action' => 'activar',
                            ],
                        ],
                        'constraints' => [
                            'id' => '[0-9]\d*',
                        ],
                    ],
                    'pageinactivos' => [
                        'type' => Segment::class,
                        'options' => [
                            'route' => '/pageinactivos[/:id[/:estado]]',
                            'defaults' => [
                                'controller' => Controller\EjecutivoInactivoController::class,
                                'action' => 'index',
                            ],
                            'constraints' => [
                                'id' => '[0-9]\d*',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
   'controllers' => [
        'factories' => [
            Controller\EjecutivoController::class => Controller\Factory\EjecutivoControllerFactory::class,
            Controller\EjecutivoInactivoController::class => Controller\Factory\EjecutivoInactivoControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            Service\EjecutivoManager::class => Service\Factory\EjecutivoManagerFactory::class,
            Service\EjecutivoInactivoManager::class => Service\Factory\EjecutivoInactivoManagerFactory::class,
            \Zend\Authentication\AuthenticationService::class => Service\Factory\AuthenticationServiceFactory::class,
        ],
    ],
    'view_manager' => [
//        'display_not_found_reason' => true,
//        'display_exceptions'       => true,
//        'doctype'                  => 'HTML5',
//        'not_found_template'       => 'error/404',
//        'exception_template'       => 'error/index',
//        'template_map' => [
//            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
//            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
//            'error/404'               => __DIR__ . '/../view/error/404.phtml',
//            'error/index'             => __DIR__ . '/../view/error/index.phtml',
//        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ . '_driver' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
                'paths' => [__DIR__ . '/../src/Entity']
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                ]
            ]
        ]
    ]
];
