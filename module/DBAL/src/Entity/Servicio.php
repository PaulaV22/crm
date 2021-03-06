<?php
namespace DBAL\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Description of Servicio
 *
 * This class represents a registered servicio.
 * @ORM\Entity()
 * @ORM\Table(name="SERVICIO")
 */
class Servicio
{

    //================================================================================
    // Properties
    //================================================================================

    /**
     * @ORM\Id
     * @ORM\Column(name="ID_SERVICIO", type="integer")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id_servicio;

    /**
     * @ORM\Column(name="NOMBRE", nullable=true, type="string", length=255)
     */
    protected $nombre;
    /**
     * @ORM\Column(name="DESCRIPCION", nullable=true, type="string", length=255)
     */
    protected $descripcion;

    /**
     * Many Services have One Type.
     * @ORM\ManyToOne(targetEntity="Categoria")
     * @ORM\JoinColumn(name="ID_CATEGORIA", referencedColumnName="ID")
     */
    private $categoria;

    /**
     * Many Services have One Proveedor.
     * @ORM\ManyToOne(targetEntity="Proveedor")
     * @ORM\JoinColumn(name="ID_PROVEEDOR", referencedColumnName="ID_PROVEEDOR")
     */
    private $proveedor;

    /**
     * @ORM\Column(name="PRECIO", nullable=true, type="decimal")
     */
    protected $precio;


    /**
     * Many Products have One Product.
     * @ORM\ManyToOne(targetEntity="Iva")
     * @ORM\JoinColumn(name="ID_IVA", referencedColumnName="ID")
     */
    protected $iva;

    /**
     * @ORM\Column(name="IVA_GRAVADO", nullable=true, type="decimal")
     */
    protected $iva_gravado;

    /**
     * @ORM\Column(name="DESCUENTO", nullable=true, type="decimal")
     */
    protected $descuento;

    /**
     * @ORM\Column(name="PRECIO_FINAL_DTO", nullable=true, type="decimal")
     */
    protected $precio_final_dto;

    /**
     * @ORM\Column(name="PRECIO_FINAL_IVA", nullable=true, type="decimal")
     */
    protected $precio_final_iva;

    /**
     * @ORM\Column(name="PRECIO_FINAL_IVA_DTO", nullable=true, type="decimal")
     */
    protected $precio_final_iva_dto;

    /**
     * @ORM\Column(name="ID_MONEDA", nullable=true, type="integer")
     */
    protected $moneda;

    /**
     * @ORM\ManyToOne(targetEntity="Bienes")
     * @ORM\JoinColumn(name="ID_BIEN", referencedColumnName="ID")
     */
    private $bien;

    //================================================================================
    // Methods
    //================================================================================

    /**
     * Get the value of id_servicio
     */
    public function getId()
    {
        return $this->id_servicio;
    }

    /**
     * Set the value of id_servicio
     *
     * @return  self
     */
    public function setId($id_servicio)
    {
        $this->id_servicio = $id_servicio;
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
     * Get the value of precio
     */
    public function getPrecio()
    {
        return $this->precio;
    }

    /**
     * Set the value of precio
     *
     * @return  self
     */
    public function setPrecio($precio)
    {
        $this->precio = $precio;
        return $this;
    }


    /**
     * Get the value of proveedor
     */
    public function getProveedor()
    {
        return $this->proveedor;
    }

    /**
     * Set the value of proveedor
     *
     * 
     */
    public function setProveedor($proveedor)
    {
        $this->proveedor = $proveedor;
        return $this;
    }

    /**
     * Get the object iva
     */
    public function getIva()
    {
        return $this->iva;
    }

    /**
     * Set the value of iva
     *
     * @return  self
     */
    public function setIva($iva)
    {
        $this->iva = $iva;
        return $this;
    }

    /**
     * Get the value of iva_gravado
     */
    public function getIva_gravado()
    {
        return $this->iva_gravado;
    }

    /**
     * Set the value of iva_gravado
     *
     * @return  self
     */
    public function setIva_gravado($iva_gravado)
    {
        $this->iva_gravado = $iva_gravado;
        return $this;
    }

    /**
     * Get the value of descuento
     */
    public function getDescuento()
    {
        return $this->descuento;
    }

    /**
     * Set the value of descuento
     *
     * @return  self
     */
    public function setDescuento($descuento)
    {
        $this->descuento = $descuento;
        return $this;
    }

    /**
     * Get the value of precio_final_iva
     */
    public function getPrecio_final_iva()
    {
        return $this->precio_final_iva;
    }

    /**
     * Set the value of precio_final_iva
     *
     * @return  self
     */
    public function setPrecio_final_iva($precio_final_iva)
    {
        $this->precio_final_iva = $precio_final_iva;
        return $this;
    }

    /**
     * Get the value of precio_final_iva_dto
     */
    public function getPrecio_final_iva_dto()
    {
        return $this->precio_final_iva_dto;
    }

    /**
     * Set the value of precio_final_iva_dto
     *
     * @return  self
     */
    public function setPrecio_final_iva_dto($precio_final_iva_dto)
    {
        $this->precio_final_iva_dto = $precio_final_iva_dto;
        return $this;
    }

    /**
     * Get the value of moneda
     */
    public function getMoneda()
    {
        return $this->moneda;
    }

    /**
     * Set the value of moneda
     *
     * @return  self
     */
    public function setMoneda($moneda)
    {
        $this->moneda = $moneda;
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

    /**
     * Get many Services have One Type.
     */
    public function getCategoria()
    {
        return $this->categoria;
    }

    public function getIdCategoria()
    {
        if (!is_null($this->bien)) {
            return $this->bien->getIdCategoria();
        }
        return null;
    }

    public function getNombreCategoria()
    {
        if (!is_null($this->bien)) {
            return $this->bien->getNombreCategoria();
        }
        return null;
    }
    /**
     * Set many Services have One Type.
     *
     * @return  self
     */
    public function setCategoria($categoria)
    {
        $this->categoria = $categoria;

        return $this;
    }

    public function getValorIVa()
    {
        if (is_null($this->iva)) {
            return null;
        }
        return $this->iva->getValor();
    }

    public function getCategoriaNombre()
    {
        if (is_null($this->bien)) {
            return null;
        }
        return $this->bien->getNombreCategoria();
    }

    /**
     * Get the value of precio_final_dto
     */
    public function getPrecio_final_dto()
    {
        return $this->precio_final_dto;
    }

    /**
     * Set the value of precio_final_dto
     *
     * @return  self
     */
    public function setPrecio_final_dto($precio_final_dto)
    {
        $this->precio_final_dto = $precio_final_dto;

        return $this;
    }

    /**
     * Get the value of bien
     */
    public function getBien()
    {
        return $this->bien;
    }

    /**
     * Set the value of bien
     *
     * @return  self
     */
    public function setBien($bien)
    {
        $this->bien = $bien;

        return $this;
    }
}
