<?php

namespace BackendBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Category
 *
 * @ORM\Table(name="category_table")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="BackendBundle\Entity\Repository\CategoryRepository")
 *
 */
class Category
{

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     **/
    public function categoryTree()
    {
        $result = $this->getTitle();


        $parent = $this->getParent();

        $categories = $this->getCategories();
        while($parent != null)
        {
            $result = $parent->getTitle() . '>' . $result;
            $parent = $parent->getParent();
        }

        $this->setTreeTitle($result);

        if($categories != null)
            $this->setChildTreeTitle($this->getTreeTitle(), $categories);
    }

    public function  setChildTreeTitle($parentTreeTitle, $categories)
    {
        foreach ($categories as $val)
        {
            $val->setTreeTitle($parentTreeTitle . '>' . $val->getTitle());

            if($val->getCategories() != null)
                $this->setChildTreeTitle($val->getTreeTitle(), $val->getCategory());
        }
    }

       /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @var string
     * @ORM\Column(name="title", type="text", nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="tree_title", type="text", nullable=true)
     */
    private $treeTitle;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Product", mappedBy="category", cascade={"remove"})
     */
    private $products;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Category", mappedBy="parent", cascade={"remove"})
     */
    private $categories;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="categories", cascade={"remove"})
     * @ORM\JoinColumn(name="parent", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     **/
    private $parent;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public  function __toString()
    {
        return (string)$this->getTitle();
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
    public function getTreeTitle()
    {
        return $this->treeTitle;
    }

    /**
     * @param string $treeTitle
     */
    public function setTreeTitle($treeTitle)
    {
        $this->treeTitle = $treeTitle;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }


    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getParent()
    {
        return $this->parent;
    }


    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
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

}
