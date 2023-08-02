<?php

namespace App\Service;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;

class ProductService
{

    private EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Method to return the query to fetch all the products in the database
     * to use it in the paginator
     * @return Query
     */
    public function getAllProductsQuery(): Query
    {
        return $this->entityManager->getRepository(Product::class)->getAllProductsQuery();
    }

    /**
     * Method to get a product by its id
     *
     * @param $productId
     * @return Product|mixed|object|null
     */
    public function getProductById($productId)
    {
        return $this->entityManager->getRepository(Product::class)->find($productId);
    }

    /**
     * Method to save on the database the updates made on a product
     *
     * @param Product $product
     * @return void
     */
    public function saveUpdatedProduct(Product $product)
    {
        // We save the updates on the product in the database
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }
}