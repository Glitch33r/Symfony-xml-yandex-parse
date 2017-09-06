<?php

namespace BackendBundle\Services;

use BackendBundle\Entity\Currency;
use BackendBundle\Entity\Product;
use BackendBundle\Entity\Category;

class ConfigXMLReader extends AbstractXMLReader
{
    protected function parseCurrency()
    {
        $this->existCurrenciesArray['currencies'] = array();
        $em = $this->container->get('doctrine');
        $this->existCurrenciesArray['currencies'] = $em->getRepository('BackendBundle:Currency')->getAllCurrenciesInAssocArray();

        if ($this->reader->nodeType == \XMLREADER::ELEMENT && $this->reader->localName == 'currency')
        {
            $currency = array(
                'id' => $this->reader->getAttribute('id'),
                'rate' => $this->reader->getAttribute('rate'),
            );

            $this->result['currencies'][] = $currency;
        }
        $this->addCurrency();
    }

    protected function parseCategory()
    {
        $this->existCategoriesArray['categories'] = array();
        $em = $this->container->get('doctrine');
        $this->existCategoriesArray['categories'] = $em->getRepository('BackendBundle:Category')->getAllCategoriesInAssocArray();

        if ($this->reader->nodeType == \XMLREADER::ELEMENT && $this->reader->localName == 'category')
        {
            $category = array(
                'id' => (int)$this->reader->getAttribute('id'),
                'parentId' => (int)$this->reader->getAttribute('parentId'),
            );

            $this->reader->read();

            if ($this->reader->nodeType == \XMLREADER::TEXT)
            {
                $category['name'] = $this->reader->value;
            }

            $this->result['categories'][] = $category;
        }
        $this->addCategories();
    }

    protected function parseOffer()
    {
        $this->moveToStart();
        $this->existProductsArray['products'] = array();
        $em = $this->container->get('doctrine');
        $this->existProductsArray['products'] = $em->getRepository('BackendBundle:Product')->getAllProductsInAssocArray();

        while ($this->reader->read() && $this->reader->name !== 'offer');

        while ($this->reader->name == 'offer')
        {
            $offer['available'] = (string)$this->reader->getAttribute('available');

            $node = new \SimpleXMLElement($this->reader->readOuterXML());

            $offer['price'] = (double)$node->price;
            $offer['currencyId'] = (string)$node->currencyId;
            $offer['categoryId'] = (int)$node->categoryId;
            $offer['picture'] = (string)$node->picture;
            $offer['delivery'] = (string)$node->delivery;
            $offer['name'] = (string)$node->name;
            $offer['vendor'] = (string)$node->vendor;
            $offer['vendorCode'] = (string)$node->vendorCode;
            $offer['description'] = (string)strip_tags($node->description);

            $this->result['offers'][] = $offer;

            $this->reader->next('offer');
        }
        $this->addProduct();
    }

    protected function addProduct()
    {
        $em = $this->container->get('doctrine')->getManager();

        foreach ($this->result['offers'] as $offer)
        {
            if (!array_key_exists($offer['name'], $this->existProductsArray['products']))
            {
                $newProduct = new Product();
                $newProduct->setPrice($offer['price']);
                $newProduct->setDescription($offer['description']);
                /*$imageFile = $this->uploadFile($offer['picture']);*/
                $newProduct->setImage($offer['picture']);
                $newProduct->setTitle($offer['name']);

                if( $offer['delivery'] == 'true' )
                {
                    $newProduct->setDelivery(true);
                }
                else
                    $newProduct->setDelivery(false);

                if( $offer['available'] == 'true' )
                {
                    $newProduct->setAvailable(true);
                }
                else
                    $newProduct->setAvailable(false);


                $newProduct->setVendor($offer['vendor']);
                $newProduct->setVendorCode($offer['vendorCode']);

                foreach ($this->result['currencies'] as $currency)
                {
                    if( $offer['currencyId'] == $currency['id'] )
                    {
                        $prodCurrency = $em->getRepository('BackendBundle:Currency')
                                                ->findOneBy(array('name' => $currency['id'] ));

                        $newProduct->setCurrency($prodCurrency);
                    }
                }

                foreach ($this->result['categories'] as $category)
                {
                    if($offer['categoryId'] == $category['id'])
                    {
                        $prodCategory =  $em->getRepository('BackendBundle:Category')
                                                ->findOneBy(array('title' => $category['name']));

                        $newProduct->setCategory($prodCategory);
                    }
                }

                $this->existProductsArray['products'] = $newProduct;
                $em->persist($newProduct);
            }
        }
        $em->flush();
    }

    protected function addCategories()
    {
        $em = $this->container->get('doctrine')->getManager();

        foreach ($this->result['categories'] as $category)
        {
            if (!array_key_exists($category['name'], $this->existCategoriesArray['categories']))
            {
                $newCategory = new Category();
                $newCategory->setTitle($category['name']);
                $em->persist($newCategory);

                $this->existCategoriesArray['categories'] = $category;
            }
        }
        $em->flush();
        $this->addParent();
    }

    protected function addParent()
    {
        foreach ($this->result['categories'] as $mainCategory)
        {
            $mainId = $mainCategory['id'];

            foreach ($this->result['categories'] as $subCategory)
            {
                $parentId = $subCategory['parentId'];

                if( $parentId === $mainId )
                {
                    $em = $this->container->get('doctrine');
                    $mainCategoryId = $em->getRepository('BackendBundle:Category')->getCategoryIdByName($mainCategory['name']);

                    $em->getRepository('BackendBundle:Category')->setCategoryParent($mainCategoryId, $subCategory['name']);
                }
            }
        }
    }

    protected function addCurrency()
    {
        $em = $this->container->get('doctrine')->getManager();

        foreach ($this->result['currencies'] as $currency)
        {
            if(!array_key_exists($currency['id'], $this->existCurrenciesArray['currencies']))
            {
                $newCurrency = new Currency();
                $newCurrency->setName($currency['id']);
                $newCurrency->setRate($currency['rate']);

                $em->persist($newCurrency);

                $this->existCurrenciesArray['currencies'] =  $newCurrency;
            }
        }
        $em->flush();
    }


 /*   private function uploadFile ($path)
    {
        $file = $path;

        $name = basename($file);

        $fileSize = getimagesize($file);

        $fileType = $fileSize['mime'];

        $fileHandler = $this->container->get('upbeat_file_upload.handler');

        $dir = $this->container->getParameter('xml_img_save_dir');

        $kernelDir = $this->container->getParameter('kernel.project_dir');

        $fullPath = $kernelDir.'/..'.$dir.$name;

        if (!is_dir($kernelDir.'/..'.$dir))
        {
            mkdir($kernelDir.'/..'.$dir, 0777, true);
        }

        chmod($kernelDir.'/..'.$dir, 0777);

        switch($fileType)
        {
            case "image/gif":
                $img = imagecreatefromgif($file);
                imagegif($img, $fullPath.$name);
                break;

            case "image/jpeg":
                $img = imagecreatefromjpeg($file);
                imagejpeg($img, $fullPath.$name);
                break;

            case "image/png":
                $img = imagecreatefrompng($file);
                imagepng($img, $fullPath.$name);
                break;
        }


        $tempUploadDir = $this->container->getParameter('xml_img_temp_upload_dir');

        $fileHandler->clearDirectory($tempUploadDir);

        return $dir.$name;
    }*/

}