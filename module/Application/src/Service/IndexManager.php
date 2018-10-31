<?php

namespace Index\Service;

use DBAL\Entity\Cliente;
use Clientes\Service\ClientesManager;

/**
 * This service is responsible for adding/editing users
 * and changing user password.
 */
class IndexManager {

    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    private $clientesManager;
    
    public function __construct($entityManager) {
        $this->entityManager = $entityManager;
        $this->clientesManager= new ClientesManager($entityManager);
    }

    public function getResult($data){
        $result= $this->clientesManager->busquedaPorFiltros($data);
        print_r($result);
        die();
        return $result;
    }
    
   
}