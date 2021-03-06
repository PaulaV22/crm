<?php
namespace Pedido\Controller;

use Transaccion\Controller\TransaccionController;
use Pedido\Service\PedidoManager;

use Zend\View\Model\ViewModel;
use DBAL\Entity\BienesTransacciones;

class PedidoController extends TransaccionController
{

    /**
     * Pedido manager.
     * @var User\Service\PedidoManager 
     */
    protected $pedidoManager;
    private $clientesManager;
    private $proveedorManager;
    private $bienesTransaccionesManager;
    private $bienesManager;
    
    public function __construct($pedidoManager,$monedaManager, $personaManager, $clientesManager, $proveedorManager, $bienesTransaccionesManager, $bienesManager, $formaPagoManager, $formaEnvioManager, $ivaManager,$empresaManager) {
        parent::__construct($pedidoManager, $personaManager,  $monedaManager,$ivaManager, $formaPagoManager, $formaEnvioManager, $empresaManager);
        $this->clientesManager = $clientesManager;
        $this->proveedorManager = $proveedorManager;
        $this->pedidoManager = $pedidoManager;
        $this->bienesTransaccionesManager = $bienesTransaccionesManager;
        $this->bienesManager = $bienesManager;
    }

    public function indexAction()
    {

    }

    private function getIdComprobante(){
        return 8;
    }

    public function getTipo(){
        return "pedido";
    }
    
    public function addAction()
    {
        $json="[]";
        if (isset($_SESSION['TRANSACCIONES']['PEDIDO'])) {
            $json = $_SESSION['TRANSACCIONES']['PEDIDO'];
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
            $data['tipo'] = $this->getTipo();
            $data['persona'] = $persona;
            $data['idComprobante'] = $this->getIdComprobante();

            $this->pedidoManager->add($data);
            if ($persona->getTipo() == "CLIENTE") {
                $this->redirect()->toRoute('clientes/ficha', ['action' => 'ficha', 'id' => $persona->getId()]);
            } else {
                $this->redirect()->toRoute('proveedores/ficha',['action'=>'ficha', 'id' =>$persona->getId()]);
            }
        }
        $numTransacciones = $this->pedidoManager->getTotalTransacciones() + 1;
        $numPedido = $this->pedidoManager->getTotalPedidos() + 1;
        $monedasJson = $this->getJsonMonedas();
        $formasPagoJson = $this->getJsonFormasPago();
        $formasEnvioJson = $this->getJsonFormasEnvio();
        $ivasJson = $this->getJsonIvas();
        $presupuestos = $this->pedidoManager->getTransaccionesPersonaTipo($persona->getId(),"PRESUPUESTO");
        $jsonPrespuestos = $this->getJsonFromObjectList($presupuestos);
        $empresaJson = $this->empresaManager->getEmpresa()->getJSON();


        $this->reiniciarParams();
        return new ViewModel([
            // 'items' => $items,
            'persona' => $persona,
            'tipoPersona' => $tipoPersona,
            'numTransacciones' => $numTransacciones,
            'numPedido' => $numPedido,
            'json' => $json,
            'json_bienes' => $json_bienes,
            'formasPagoJson' => $formasPagoJson,
            'formasEnvioJson' => $formasEnvioJson,
            'monedasJson' => $monedasJson,
            'ivasJson' => $ivasJson,
            'transaccionJson'=>"[]",
            'presupuestosJson' => $jsonPrespuestos,
            'empresaJson' => $empresaJson,
            'itemsTransaccionJson' =>"[]",

        ]);
    }

    public function editAction() {
        $id_transaccion = $this->params()->fromRoute('id');
        $pedido = $this->pedidoManager->getPedidoFromTransaccionId($id_transaccion);
        $items = array();

        if (!is_null($pedido)) {
            $items = $pedido->getTransaccion()->getBienesTransacciones();
        }
       
        $json = "";
        //SI HAY ITEMS CARGADOS EN LA SESION LOS TOMO DE AHI 
        if ((isset($_SESSION['TRANSACCIONES']['PEDIDO']))){
            $json = $_SESSION['TRANSACCIONES']['PEDIDO'];
        }
        //SINO LOS TOMO DEL PEDIDO Y GUARDO ESO EN LA SESION PARA CONTINUAR TRABAJANDO CON LA SESION
        else{
            $json = $this->getJsonFromObjectList($items);
            $_SESSION['TRANSACCIONES']['PEDIDO'] = $json;
        }
       
        $persona = $pedido->getTransaccion()->getPersona();
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

            // $data['items'] = $_SESSION['TRANSACCIONES']['PEDIDO'];
            $this->pedidoManager->edit($pedido, $data);
            $url = $data['url'];
            if ($persona->getTipo() == "CLIENTE") {
                $this->redirect()->toRoute('clientes/ficha', ['action' => 'ficha', 'id' => $persona->getId()]);
            } else {
                $this->redirect()->toRoute('proveedores/ficha',['action'=>'ficha', 'id' =>$persona->getId()]);
            }
        }
        $numTransacciones = $pedido->getTransaccion()->getNumero();
        $numPedido = $pedido->getNumero();
        $monedasJson = $this->getJsonMonedas();
        $formasPagoJson = $this->getJsonFormasPago();
        $formasEnvioJson = $this->getJsonFormasEnvio();
        $ivasJson = $this->getJsonIvas();
        $transaccion = $pedido->getTransaccion();
        $transaccionJson = $pedido->getTransaccion()->getJSON();
        $presupuestos = $this->pedidoManager->getTransaccionesPersonaTipo($persona->getId(),"PRESUPUESTO");
        $jsonPrespuestos = $this->getJsonFromObjectList($presupuestos);
        $empresaJson = $this->empresaManager->getEmpresa()->getJSON();
        
        $this->reiniciarParams();
        return new ViewModel([
            // 'items' => $items,
            'persona' => $persona,
            'tipoPersona' => $tipoPersona,
            'numTransacciones' => $numTransacciones,
            'numPedido' => $numPedido,
            'json' => $json,
            'json_bienes' => $json_bienes,
            'formasPagoJson' => $formasPagoJson,
            'formasEnvioJson' => $formasEnvioJson,
            'monedasJson' => $monedasJson,
            'pedido' => $pedido,
            'transaccion' => $transaccion,
            'transaccionJson' => $transaccionJson,
            'ivasJson' => $ivasJson,
            'presupuestosJson' => $jsonPrespuestos,
            'empresaJson' => $empresaJson,
            'itemsTransaccionJson' =>"[]",
        ]);
    }

    public function setItemsAction(){
        $items = $_POST['json'];
        $_SESSION['TRANSACCIONES']['PEDIDO'] = $items;
    }

    public function eliminarItemAction(){
        $items = $_POST['json'];
        $index = $_POST['index'];
       
        $itemsArray = json_decode($items, true);
        $itemsArray = array_splice($itemsArray, $index, 1);
        $json = json_encode($itemsArray);
        
        $view = new ViewModel(['json'=>$json]);
        $view->setTemplate('layout/nulo');
        $view->setTerminal(true);
        return $view;
    }

    public function getJsonFormasEnvio()
   {
       $formasEnvio = $this->formaEnvioManager->getFormasEnvio();
       $json = $this->getJsonFromObjectList($formasEnvio);
       return $json;
   }

   public function getItemsTransaccionAction(){
        $this->layout()->setTemplate('layout/nulo');
        $idTransaccion = $this->params()->fromRoute('id');
        $transaccion = $this->pedidoManager->getTransaccionId($idTransaccion);
        $transaccionJson = $transaccion->getJSON();
        $items = $transaccion->getBienesTransacciones();
        $itemsTransaccionJson = $this->getJsonFromObjectList($items);
        $view = new ViewModel([
            'transaccion' => $transaccion,
            'itemsTransaccionJson'=>$itemsTransaccionJson,
            'transaccionJson' => $transaccionJson,
        ]);
        $view->setTerminal(true);
        return $view;
   }

    // PDF
    public function pdfAction() {
        $this->layout()->setTemplate('layout/nulo');
        $id_transaccion = $this->params()->fromRoute('id');
        $pedido = $this->pedidoManager->getPedidoFromTransaccionId($id_transaccion);
        $items = array();

        if (!is_null($pedido)) {
            $items = $pedido->getTransaccion()->getBienesTransacciones();
        }
       
        $json = "";
        $json = $this->getJsonFromObjectList($items);
       
        $persona = $pedido->getTransaccion()->getPersona();
        $tipoPersona = null;

        $empresa = $this->empresaManager->getEmpresa();

        $numTransacciones = $pedido->getTransaccion()->getNumero();
        $numPedido = $pedido->getNumero();
        $monedasJson = $this->getJsonMonedas();
        $formasPagoJson = $this->getJsonFormasPago();
        $formasEnvioJson = $this->getJsonFormasEnvio();
        $ivasJson = $this->getJsonIvas();
        $transaccion = $pedido->getTransaccion();
        $transaccionJson = $pedido->getTransaccion()->getJSON();
        $presupuestos = $this->pedidoManager->getTransaccionesPersonaTipo($persona->getId(),"PRESUPUESTO");
        $jsonPrespuestos = $this->getJsonFromObjectList($presupuestos);
        $this->reiniciarParams();
        return new ViewModel([
            'persona' => $persona,
            'tipoPersona' => $tipoPersona,
            'numTransacciones' => $numTransacciones,
            'numPedido' => $numPedido,
            'json' => $json,
            'formasPagoJson' => $formasPagoJson,
            'formasEnvioJson' => $formasEnvioJson,
            'monedasJson' => $monedasJson,
            'pedido' => $pedido,
            'transaccion' => $transaccion,
            'transaccionJson' => $transaccionJson,
            'ivasJson' => $ivasJson,
            'presupuestosJson' => $jsonPrespuestos,
            'itemsTransaccionJson' =>"[]",
            'empresa' => $empresa,
        ]);
    }

   
   public function getItemsPreviosAction(){
        $this->layout()->setTemplate('layout/nulo');
        $numPresupuesto = $this->params()->fromRoute('id');
        $presupuesto = $this->pedidoManager->getPresupuestoPrevio($numPresupuesto);
        $itemsTransaccionJson="[]";
        if ($presupuesto!=null){
            $transaccion = $presupuesto->getTransaccion();
            $items = $transaccion->getBienesTransacciones();
            $itemsTransaccionJson = $this->getJsonFromObjectList($items);
        }
        $view = new ViewModel(['itemsTransaccionJson'=>$itemsTransaccionJson]);
        $view->setTerminal(true);
        return $view;
    }   

    public function cambiarEstadoAction(){
        // $this->layout()->setTemplate('layout/nulo');
        $idTransaccion = $this->params()->fromRoute('id');
        $estado= $this->params()->fromRoute('id2');
        $this->pedidoManager->cambiarEstadoTransaccion($idTransaccion, $estado);
        $view = new ViewModel();
        $view->setTemplate('layout/nulo');
        $view->setTerminal(true);
        return $view;
    }
}