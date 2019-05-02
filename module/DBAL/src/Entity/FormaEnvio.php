<?php
namespace DBAL\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of FormaEnvio
 *
 * This class represents a registered forma de pago.
 * @ORM\Entity()
 * @ORM\Table(name="FORMA_DE_ENVIO")
 */
class FormaEnvio
{
    //put your code here

    /**
     * @ORM\Id
     * @ORM\Column(name="ID", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */

    protected $id;

    /**
     * @ORM\Column(name="FORMA_DE_ENVIO", nullable=true, type="string")
     */
    protected $nombre;

    /**
     * @ORM\Column(name="DESCRIPCION", nullable=true, type="string")
     */
    protected $descripcion;


    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of nombre
     */ 
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set the value of nombre
     *
     * @return  self
     */ 
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }
  
    /**
     * Get the value of descripcion
     */ 
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set the value of descripcion
     *
     * @return  self
     */ 
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }



    public function getJSON(){

        $output = "";
        $output .= '"Id": "' . $this->getId() .'", ';
        $output .= '"Nombre": "' . $this->getNombre() .'", ';
        $output .= '"Descripcion": "' . $this->getDescripcion() .'" ';

        return  '{'.$output.'}' ;
    }

}