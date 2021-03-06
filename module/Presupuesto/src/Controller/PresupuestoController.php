<?php

/**
 * Clase actualmente sin uso
 */

namespace Presupuesto\Controller;

use Transaccion\Controller\TransaccionController;
use Presupuesto\Service\PresupuestoManager;
use DBAL\Entity\Empresa;

use Zend\View\Model\ViewModel;

class PresupuestoController extends TransaccionController{

    /**
     * Presupuesto manager.
     * @var User\Service\PresupuestoManager 
     */
    protected $presupuestoManager;
    private $clientesManager;
    private $proveedorManager;
    private $tipo;
    private $bienesTransaccionesManager;
    private $bienesManager;
    private $items;

    public function __construct(
        $presupuestoManager,
        $monedaManager,
        $personaManager,
        $clientesManager,
        $proveedorManager,
        $bienesTransaccionesManager,
        $bienesManager, 
        $formaPagoManager,
        $formaEnvioManager,
        $empresaManager,
        $ivaManager
    ) {
        parent::__construct($presupuestoManager, $personaManager,  $monedaManager,$ivaManager, $formaPagoManager, $formaEnvioManager, $empresaManager);
        $this->clientesManager = $clientesManager;
        $this->proveedorManager = $proveedorManager;
        $this->presupuestoManager = $presupuestoManager;
        $this->bienesTransaccionesManager = $bienesTransaccionesManager;
        $this->bienesManager = $bienesManager;
        // $this->itemsSeteados="";
    }


    public function indexAction() {
    }
       
    public function getTipo(){
        return "presupuesto";
    }
    

    private function getIdComprobante(){
        return 9;
    }

    public function addAction() {
        $json="[]";
        if (isset($_SESSION['TRANSACCIONES']['PRESUPUESTO'])) {
            $json = $_SESSION['TRANSACCIONES']['PRESUPUESTO'];

        }
        // Obtengo todos los Bienes
        $bienes = $this->bienesManager->getBienes();

        // Creo JSON con Nombres de todos los Productos y Servicios
        $json_bienes = "";

        // $response[] = array("value"=>"1","label"=>"Soporte");
        foreach ($bienes as $bien) {
            $json_bienes .= $bien->getJsonBien() . ',';
        }

        $json_bienes = substr($json_bienes, 0, -1);
        $json_bienes = '[' . $json_bienes . ']';

        $id_persona = $this->params()->fromRoute('id');
        $persona = $this->personaManager->getPersona($id_persona);
        $tipoPersona = null;

        if ($persona->getTipo() == "CLIENTE") {
            $tipoPersona = $this->clientesManager->getClienteIdPersona($id_persona);
        } elseif ($persona->getTipo() == "PROVEEDOR") {
            $tipoPersona = $this->proveedorManager->getProveedorIdPersona($id_persona);
        }

        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $data['tipo'] = $this->getTipo($persona->getTipo());
            $data['persona'] = $persona;
            $data['idComprobante'] = $this->getIdComprobante();

            $this->presupuestoManager->add($data);
            if ($persona->getTipo() == "CLIENTE") {
                $this->redirect()->toRoute('clientes/ficha', ['action' => 'ficha', 'id' => $persona->getId()]);
            } else {
                $this->redirect()->toRoute('proveedores/ficha',['action'=>'ficha', 'id' =>$persona->getId()]);
            }
        }
        $numTransacciones = $this->presupuestoManager->getTotalTransacciones() + 1;
        $numPresupuesto = $this->presupuestoManager->getTotalPresupuestos() + 1;
        $monedasJson = $this->getJsonMonedas();
        $formasPagoJson = $this->getJsonFormasPago();
        $formasEnvioJson = $this->getJsonFormasEnvio();
        $ivasJson = $this->getJsonIvas();
        $empresaJson = $this->empresaManager->getEmpresa()->getJSON();

        $this->reiniciarParams();
        return new ViewModel([
            // 'items' => $items,
            'persona' => $persona,
            'tipoPersona' => $tipoPersona,
            'numTransacciones' => $numTransacciones,
            'numPresupuesto' => $numPresupuesto,
            'json' => $json,
            'json_bienes' => $json_bienes,
            'formasPagoJson' => $formasPagoJson,
            'formasEnvioJson' => $formasEnvioJson,
            'monedasJson' => $monedasJson,
            'ivasJson' => $ivasJson,
            'empresaJson' => $empresaJson,
            'transaccionJson'=>"[]",
        ]);
    }

    public function editAction() {
        $id_transaccion = $this->params()->fromRoute('id');
        $presupuesto = $this->presupuestoManager->getPresupuestoFromTransaccionId($id_transaccion);
        $items = array();

        if (!is_null($presupuesto)) {
            $items = $presupuesto->getTransaccion()->getBienesTransacciones();
        }
        $json = "";
        //SI HAY ITEMS CARGADOS EN LA SESION LOS TOMO DE AHI 
        if ((isset($_SESSION['TRANSACCIONES']['PRESUPUESTO']))){
            $json = $_SESSION['TRANSACCIONES']['PRESUPUESTO'];
        }
        //SINO LOS TOMO DEL PRESUPUESTO Y GUARDO ESO EN LA SESION PARA CONTINUAR TRABAJANDO CON LA SESION
        else{
            $json = $this->getJsonFromObjectList($items);
            $_SESSION['TRANSACCIONES']['PRESUPUESTO'] = $json;
        }
       
        $persona = $presupuesto->getTransaccion()->getPersona();
        $tipoPersona = null;
        // Obtengo todos los Bienes
        $bienes = $this->bienesManager->getBienes();

        // Creo JSON con Nombres de todos los Productos y Servicios
        $json_bienes = "";

        // $response[] = array("value"=>"1","label"=>"Soporte");
        foreach ($bienes as $bien) {
            $json_bienes .= $bien->getJsonBien() . ',';
        }
        $json_bienes = substr($json_bienes, 0, -1);
        $json_bienes = '[' . $json_bienes . ']';

        if ($persona->getTipo() == "CLIENTE") {
            $tipoPersona = $this->clientesManager->getClienteIdPersona($persona->getId());
        } elseif ($persona->getTipo() == "PROVEEDOR") {
            $tipoPersona = $this->proveedorManager->getProveedorIdPersona($persona->getId());
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $data['tipo'] = $this->getTipo($persona->getTipo());
            $data['persona'] = $persona;
            $data['idComprobante'] = $this->getIdComprobante();

            $this->presupuestoManager->edit($presupuesto, $data);
            $url = $data['url'];
            if ($persona->getTipo() == "CLIENTE") {
                $this->redirect()->toRoute('clientes/ficha', ['action' => 'ficha', 'id' => $persona->getId()]);
            } else {
                $this->redirect()->toRoute('proveedores/ficha',['action'=>'ficha', 'id' =>$persona->getId()]);
            }
        }
        $numTransacciones = $presupuesto->getTransaccion()->getNumero();
        $numPresupuesto = $presupuesto->getNumero();
        $monedasJson = $this->getJsonMonedas();
        $formasPagoJson = $this->getJsonFormasPago();
        $formasEnvioJson = $this->getJsonFormasEnvio();
        $ivasJson = $this->getJsonIvas();
        $transaccion = $presupuesto->getTransaccion();
        $transaccionJson = $presupuesto->getTransaccion()->getJSON();
        $empresaJson = $this->empresaManager->getEmpresa()->getJSON();

        $this->reiniciarParams();
        return new ViewModel([
            // 'items' => $items,
            'persona' => $persona,
            'tipoPersona' => $tipoPersona,
            'numTransacciones' => $numTransacciones,
            'numPresupuesto' => $numPresupuesto,
            'json' => $json,
            'json_bienes' => $json_bienes,
            'formasPagoJson' => $formasPagoJson,
            'formasEnvioJson' => $formasEnvioJson,
            'monedasJson' => $monedasJson,
            'transaccion' => $transaccion,
            'transaccionJson' => $transaccionJson,
            'ivasJson' => $ivasJson,
            'empresaJson' => $empresaJson
        ]);
    }

    public function eliminarItemAction(){
        // $this->layout()->setTemplate('layout/nulo');
        $pos = $this->params()->fromRoute('id');
        $array = json_decode($_SESSION['TRANSACCIONES']['PRESUPUESTO']);
        array_splice($array, $pos, 1);
        $json = json_encode($array);
        $_SESSION['TRANSACCIONES']['PRESUPUESTO']= $json;
        
        $view = new ViewModel();
        $view->setTemplate('layout/nulo');
        $view->setTerminal(true);

        return $view;
    }

    public function setItemsAction(){
        $items = $_POST['json'];
        $_SESSION['TRANSACCIONES']['PRESUPUESTO'] = $items;
    }

    public function getJsonFormasEnvio()
   {
       $formasEnvio = $this->formaEnvioManager->getFormasEnvio();
       $json = $this->getJsonFromObjectList($formasEnvio);
       return $json;
   }

   // PDF
   public function pdfAction() {
        $this->layout()->setTemplate('layout/nulo');
        $id_transaccion = $this->params()->fromRoute('id');
        $presupuesto = $this->presupuestoManager->getPresupuestoFromTransaccionId($id_transaccion);
        $items = array();

        if (!is_null($presupuesto)) {
            $items = $presupuesto->getTransaccion()->getBienesTransacciones();
        }
    
        $json = "";
        $json = $this->getJsonFromObjectList($items);
        $persona = $presupuesto->getTransaccion()->getPersona();
        $tipoPersona = null;

        $empresa = $this->empresaManager->getEmpresa();
        
        $numTransacciones = $presupuesto->getTransaccion()->getNumero();
        $numPresupuesto = $presupuesto->getNumero();
        $monedasJson = $this->getJsonMonedas();
        $formasPagoJson = $this->getJsonFormasPago();
        $formasEnvioJson = $this->getJsonFormasEnvio();
        $ivasJson = $this->getJsonIvas();
        $transaccion = $presupuesto->getTransaccion();
        $transaccionJson = $presupuesto->getTransaccion()->getJSON();
        $presupuestos = $this->presupuestoManager->getTransaccionesPersonaTipo($persona->getId(),"PRESUPUESTO");
        $jsonPrespuestos = $this->getJsonFromObjectList($presupuestos);
        $this->reiniciarParams();
        return new ViewModel([
            'persona' => $persona,
            'tipoPersona' => $tipoPersona,
            'numTransacciones' => $numTransacciones,
            'numPresupuesto' => $numPresupuesto,
            'json' => $json,
            'formasPagoJson' => $formasPagoJson,
            'formasEnvioJson' => $formasEnvioJson,
            'monedasJson' => $monedasJson,
            'presupuesto' => $presupuesto,
            'transaccion' => $transaccion,
            'transaccionJson' => $transaccionJson,
            'ivasJson' => $ivasJson,
            'presupuestosJson' => $jsonPrespuestos,
            'itemsTransaccionJson' =>"[]",
            'empresa' => $empresa,
            ]);
    }
    public function cambiarEstadoAction(){
        // $this->layout()->setTemplate('layout/nulo');
        $idTransaccion = $this->params()->fromRoute('id');
        $estado= $this->params()->fromRoute('id2');
        $this->presupuestoManager->cambiarEstadoTransaccion($idTransaccion, $estado);
        $view = new ViewModel();
        $view->setTemplate('layout/nulo');
        $view->setTerminal(true);
        return $view;
    }
}
