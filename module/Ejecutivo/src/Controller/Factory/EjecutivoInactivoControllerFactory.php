<?php
namespace Ejecutivo\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Ejecutivo\Controller\EjecutivoInactivoController;
use Ejecutivo\Service\EjecutivoInactivoManager;
use Persona\Service\PersonaManager;
/**
 * Description of EjecutivoInactivoControllerFactory
 *
 * @author mariano
 */
class EjecutivoInactivoControllerFactory implements FactoryInterface {
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null){
        $entityManager = $container->get('doctrine.entitymanager.orm_default');
        $ejecutivoInactivoManager = $container->get(EjecutivoInactivoManager::class);
        $personaManager = $container->get(PersonaManager::class);

        // Instantiate the service and inject dependencies      
        return new EjecutivoInactivoController($entityManager, $ejecutivoInactivoManager,
         $personaManager);
    }    
}
