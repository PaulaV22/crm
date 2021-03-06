<?php

namespace Evento\Service;

use DBAL\Entity\Evento;
use DBAL\Entity\TipoEvento;
use Evento\Form\EventoForm;
use DBAL\Entity\Cliente;
use DBAL\Entity\Proveedor;
use DBAL\Entity\Ejecutivo;
use DBAL\Entity\Empresa;
use Zend\Paginator\Paginator;
use DoctrineModule\Paginator\Adapter\Selectable as SelectableAdapter;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use DateInterval;

/**
 * This service is responsible for adding/editing eventos
 * and changing evento password.
 */
class EventoManager {

    /**
     * Doctrine entity manager.
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected $personaManager;
    /**
     * Application config.
     * @var type 
     */
    protected $estado;

    /**
     * Constructs the service.
     */
    public function __construct($entityManager, $personaManager) {
        $this->entityManager = $entityManager;
        $this->personaManager = $personaManager;
    }

    public function getEventos() {
        $eventos = $this->entityManager->getRepository(Evento::class)->findAll();
        return $eventos;
    }

    public function getTabla() {

        $adapter = new SelectableAdapter($this->entityManager->getRepository(Evento::class)); // An object repository implements Selectable
        $paginator = new Paginator($adapter);

        return ($paginator);
    }

    public function getEventoId($id) {
        return $this->entityManager->getRepository(Evento::class)
                        ->find($id);
    }

    public function getEstado() {
        return $this->estado;
    }

    /**
     * This method adds a new evento.
     */
    public function addEvento($data, $persona) {
        $evento = new Evento();
        $fecha_evento = \DateTime::createFromFormat('d/m/Y', $data['fecha_evento']);
        $fecha_vencimiento = \DateTime::createFromFormat('d/m/Y', $data['fecha_evento']);
        $tipo_evento = $this->entityManager->getRepository(TipoEvento::class)
                        ->findOneBy(['id_tipo_evento' => $data['accion_comercial']]);
        $ejecutivo = $this->entityManager->getRepository(Ejecutivo::class)
                ->findOneBy(['usuario' => $data['ejecutivo']]);     
        $evento->setFecha($fecha_evento)
                ->setTipo($tipo_evento)
                ->setId_persona($persona)
                ->setId_ejecutivo($ejecutivo)
                ->setDescripcion($data['detalle'])
                ->setTipo_persona($persona->getTipo());
        $cliente_proveedor =null;
        if ($persona->getTipo()=="CLIENTE"){
              $cliente_proveedor= $this->entityManager
                ->getRepository(Cliente::class)->findOneBy(['persona' => $persona->getId()]);
        }
        else {
            $cliente_proveedor= $this->entityManager
            ->getRepository(Proveedor::class)->findOneBy(['persona' => $persona->getId()]);
        }
        $this->actualizaFechas($evento, $cliente_proveedor, $tipo_evento, $fecha_evento, $fecha_vencimiento);     
        $this->entityManager->persist($evento);
        $this->entityManager->flush();
        return $evento;
    }

    private function actualizaFechas($evento, $cliente, $tipo_evento, $fecha_evento, $fecha_vencimiento) {
        // Ultimo Contacto
        if (is_null($cliente->getFechaUltimoContacto())) {
            $cliente->setFechaUltimoContacto($fecha_evento);
        } else {
            if ($fecha_evento > $cliente->getFechaUltimoContacto()) {
                $cliente->setFechaUltimoContacto($fecha_evento);
            }
        }
    }
    
    public function getEventosFiltrados($parametros) {
        if (size($parametros) == 0) {
            $eventos = $this->entityManager->getRepository(Evento::class)->findAll();
            return $eventos;
        }
        $filtros = $this->limpiarParametros($parametros);
        $query = $this->busquedaPorFiltros($filtros);

        $adapter = new DoctrineAdapter(new ORMPaginator($query));
        $paginator = new Paginator($adapter);

        return $paginator;
    }

    public function busquedaPorFiltros($parametros) {
        $entityManager = $this->entityManager;
        $queryBuilder = $entityManager->createQueryBuilder();
        $queryBuilder->select('E')
                ->from(Evento::class, 'E');
        $indices = array_keys($parametros);
        for ($i = 0; $i < count($indices); $i++) {
            $p = $i + 1;
            $nombreCampo = $indices[$i];
            $valorCampo = $parametros[$nombreCampo];
            if ($i == 0) {
                $queryBuilder->where("E.$nombreCampo = ?$p");
            } else {
                $queryBuilder->andWhere("E.$nombreCampo = ?$p");
            }
            $queryBuilder->setParameter("$p", $valorCampo);
        }
        return $queryBuilder->getQuery();
    }

    public function limpiarParametros($param) {
        foreach ($param as $filtro => $valor) {
            if (empty($valor)) {
                unset($param[$filtro]);
            } else {
                trim($param[$filtro]);
            }
        }
        return ($param);
    }

    public function createForm($tipos) {
        return new EventoForm('create', $this->entityManager, null, $tipos);
    }

    public function formValid($form, $data) {
        $form->setData($data);
        return $form->isValid();
    }

    public function getFormForEvento($evento) {
        $tipos = $this->entityManager->getRepository(TipoEvento::class)->findAll();
        if ($evento == null) {
            return null;
        }
        $form = new EventoForm('update', $this->entityManager, $evento, $tipos);
        return $form;
    }

    public function getFormEdited($form, $evento) {
        $form->setData(array(
            'fecha_evento' => $evento->getFecha(),
            'tipo_evento' => $evento->getTipo(),
            'id_cliente' => $evento->getId_cliente(),
            'id_ejecutivo' => $evento->getId_Ejecutivo(),
        ));
    }

    /**
     * This method updates data of an existing evento.
     */
    public function updateEvento($evento, $form) {
        $data = $form->getData();
        $fecha_evento = \DateTime::createFromFormat('d/m/Y', $data['fecha_evento']);
        $evento->setFecha($fecha_evento);
        $evento->setTipo($data['accion_comercial']);
        $evento->setId_cliente($data['id_cliente']);
        $evento->setId_ejecutivo($data['id_ejecutivo']);
        // Apply changes to database.
        $this->entityManager->flush();
        return true;
    }

    public function removeEvento($id) {
        $evento = $this->entityManager->getRepository(Evento::class)
                ->findOneById($id);
        if ($this->tryRemoveEvento($evento)) {
            $_SESSION['MENSAJES']['ficha_cliente'] = 1;
            $_SESSION['MENSAJES']['ficha_cliente_msj'] = 'Actividad eliminada/as correctamente';
        } else {
            $_SESSION['MENSAJES']['ficha_cliente'] = 0;
            $_SESSION['MENSAJES']['ficha_cliente_msj'] = 'Error al eliminar actividad/es';
        }
    }

    public function eliminarEventos($eventos_array) {
        foreach ($eventos_array as $eve) {
            removeEvento($eve);
        }
    }

    public function eliminarTipoEventos($id) {
        $entityManager = $this->entityManager;
        $eventos = $this->entityManager->getRepository(Evento::class)->findBy(['tipo'=>$id]);
        foreach ($eventos as $evento) {
            if (!is_null($evento->getTipoId())) {
                if ($evento->getTipoId() == $id) {
                    $evento->setTipo(null);
                }
            }
        }      
        $entityManager->flush();
    }

    private function tryAddEvento($evento) {
        try {
            $this->entityManager->persist($evento);
            $this->entityManager->flush();
            return true;
        } catch (\Exception $e) {
            $this->entityManager->rollBack();
            return false;
        }
    }

    private function tryRemoveEvento($evento) {
        try {
            $this->entityManager->remove($evento);
            $this->entityManager->flush();
            return true;
        } catch (\Exception $e) {
            $this->entityManager->rollBack();
            return false;
        }
    }

}
