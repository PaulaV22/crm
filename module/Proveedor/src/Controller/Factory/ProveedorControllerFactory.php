<?php
namespace Proveedor\Controller\Factory;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Proveedor\Controller\ProveedorController;
use Proveedor\Service\ProveedorManager;
use TipoEvento\Service\TipoEventoManager;
use Evento\Service\EventoManager;
use Persona\Service\PersonaManager;
use TipoComprobante\Service\TipoComprobanteManager;
use CuentaCorriente\Service\CuentaCorrienteManager;

class ProveedorControllerFactory implements FactoryInterface {
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) {
        $proveedorManager = $container->get(ProveedorManager::class);
        $tipoEventosManager = $container->get(TipoEventoManager::class);
        $eventoManager = $container->get(EventoManager::class);
        $personaManager = $container->get(PersonaManager::class);
        $tipoComprobanteManager = $container->get(TipoComprobanteManager::class);
        $cuentaCorrienteManager = $container->get(CuentaCorrienteManager::class);

        return new ProveedorController($proveedorManager, $tipoEventosManager,
         $eventoManager, $personaManager, $tipoComprobanteManager, $cuentaCorrienteManager);
    }    
}
