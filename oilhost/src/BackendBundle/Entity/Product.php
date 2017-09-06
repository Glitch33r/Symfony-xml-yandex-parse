<?php

namespace BackendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="product_table")
 * @ORM\Entity(repositoryClass="BackendBundle\Entity\Repository\ProductRepository")
 */
class Product
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @var string - json array of paths to product images
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;
      /**
     * @var integer - product original price
     *
     * @ORM\Column(name="price", type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="Currency", inversedBy="products")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id")
     */
    private $currency;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor", type="text", length=255, nullable=true)
     */
    private $vendor;

    /**
     * @var string
     *
     * @ORM\Column(name="vendorCode", type="text", length=255, nullable=true)
     */
    private $vendorCode;

    /**
     * @var bool
     *
     * @ORM\Column(name="delivery", type="boolean", nullable=true)
     */
    private $delivery;

    /**
     * @var bool
     *
     * @ORM\Column(name="available", type="boolean", nullable=true)
     */
    private $available;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="products")
     */
    private $category;


    /**
     * @var string - json array of paths to product images
     *
     * @ORM\Column(name="image", type="text", nullable=true)
     */
    private $image;


    public function __toString()
    {
        return (string)$this->title ? $this->title : 'new';
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param int $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }


    public function setCurrency(\BackendBundle\Entity\Currency $currency = null)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return string
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * @param string $vendor
     */
    public function setVendor($vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     * @return string
     */
    public function getVendorCode()
    {
        return $this->vendorCode;
    }

    /**
     * @param string $vendorCode
     */
    public function setVendorCode($vendorCode)
    {
        $this->vendorCode = $vendorCode;
    }

    /**
     * @return bool
     */
    public function isDelivery()
    {
        return $this->delivery;
    }

    /**
     * @param bool $delivery
     */
    public function setDelivery($delivery)
    {
        $this->delivery = $delivery;
    }

    /**
     * @return bool
     */
    public function isAvailable()
    {
        return $this->available;
    }

    /**
     * @param bool $available
     */
    public function setAvailable($available)
    {
        $this->available = $available;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategory()
    {
        return $this->category;
    }


    public function setCategory(\BackendBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

}
