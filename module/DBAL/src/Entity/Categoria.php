<?php
namespace DBAL\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Categoria
 *
 * This class represents a registered categoriaEvento.
 * @ORM\Entity()
 * @ORM\Table(name="CATEGORIA")
 */
class Categoria
{
    //================================================================================
    // Properties
    //================================================================================

    /**
     * @ORM\Id
     * @ORM\Column(name="ID", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @ORM\Column(name="NOMBRE", nullable=true, type="string")
     */
    protected $nombre;

    /**
     * @ORM\Column(name="DESCRIPCION", nullable=true, type="string")
     */
    protected $descripcion;


    /**
     * @ORM\Column(name="TIPO", nullable=true, type="string")
     */
    protected $tipo;

    //================================================================================
    // Methods
    //================================================================================

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
     * @return self
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
        return (($this->nombre));
    }

    /**
     * Set the value of nombre
     *
     * @return self
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
        return (($this->descripcion));
    }

    /**
     * Set the value of descripcion
     *
     * @return self
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    /**
     * Get the value of tipo
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set the value of tipo
     *
     * @return  self
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    //================================================================================
    // JSON
    //================================================================================

    public function getJSON()
    {

        $output = "";
        $output .= '"Id": "' . $this->getId() . '", ';
        $output .= '"Nombre": "' . $this->getNombre() . '", ';
        $output .= '"Descripcion": "' . $this->getDescripcion() . '", ';
        $output .= '"Tipo": "' . $this->getTipo() . '" ';

        return  '{' . $output . '}';
    }
}
