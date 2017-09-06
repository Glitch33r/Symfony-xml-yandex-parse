<?php

namespace BackendBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Currency
 *
 * @ORM\Table(name="currency_table")
 * @ORM\Entity(repositoryClass="BackendBundle\Entity\Repository\CurrencyRepository")
 */
class Currency
{

    /**
     * @ORM\OneToMany(targetEntity="BackendBundle\Entity\Product", mappedBy="currency")
    */
    private $products;


    /**
     * @var string
     *
     * @ORM\Column(name="currency", type="text", length=255, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="rate", type="integer", nullable=true)
     */
    private $rate;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

   public function addProduct(\BackendBundle\Entity\Product $product)
    {
        $this->products[] = $product;

        return $this;
    }

    public function removeProduct(\BackendBundle\Entity\Product $product)
    {
        $this->products->removeElement($product);
    }

    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }


    /**
     * @return int
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * @param int $rate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function __toString()
    {
        return (string)$this->getName();
    }
}

