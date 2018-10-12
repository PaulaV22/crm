<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */


namespace Evento\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as DoctrineAdapter;
use Doctrine\ORM\Tools\Pagination\Paginator as ORMPaginator;
use Zend\Paginator\Paginator;
use Application\Entity\Post;
use DBAL\Entity\Evento;
use Evento\Form\EventoForm;
use DBAL\Entity\Cliente;
use DBAL\Entity\TipoEvento;



class EventoVentaController extends EventoController
{
    /**
     * @var DoctrineORMEntityManager
     */
    protected $entityManager;
    
     /**
     * Evento manager.
     * @var User\Service\EventoManager 
     */
    
    protected $eventoManager;
    

    /*public function __construct($entityManager, $eventoManager)
    {
        $this->entityManager = $entityManager;
        $this->eventoManager = $eventoManager;
    }
    */
    
    private $tipos;
    
    public function __construct($entityManager, $eventoManager)
    {
        $this->entityManager = $entityManager;
        $this->eventoManager = $eventoManager;
        $this->tipos = $this->getArrayTipos();
    }
    
   private function getArrayTipos(){
       $tipos = $this->entityManager->getRepository(TipoEvento::class)->findAll();
       $array = array();
       foreach ($tipos as $tipo){
           $array2 = array($tipo->getId() => $tipo->getNombre());
           $array= $array + $array2;       
       }
       
       return $array;
   }
   
   
    public function indexAction()
    {
        return $this->procesarIndexAction();
             
    }
    
    private function procesarIndexAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $parametros = $this->params()->fromPost();
            
        }
        else{
            $parametros = array('apellido_cliente'=>'', 'ejecutivo'=>'', 'fecha'=>'');
        }
        $apellido_cliente=$parametros['apellido_cliente'];
       // print_r($parametros);
       // $parametros = $this->params()->fromPost();
        $paginator = $this->eventoManager->getEventosFiltrados($parametros);
        $mensaje = "";
        $page = 1;
        if ($this->params()->fromRoute('id')) {
            $page = $this->params()->fromRoute('id');
        }
        $paginator->setCurrentPageNumber((int) $page)
                ->setItemCountPerPage(10);
       
        return new ViewModel([
            'eventos' => $paginator,
            'mensaje' => $mensaje,
            'parametros' =>$parametros

        ]);
    }
    
  
}