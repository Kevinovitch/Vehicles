<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\ProductService;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{

    private ProductService $productService;
    private PaginatorInterface $paginator;

    public function __construct(
        ProductService $productService,
        PaginatorInterface $paginator
    )
    {
        $this->productService = $productService;
        $this->paginator = $paginator;
    }

    /**
     * Method to display all the products of the database with pagination
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/products', name: 'app_products', methods: ['GET'])]
    public function index(Request $request)
    {
        // We get the query to get all the products of the database
        $AllProductsQuery = $this->productService->getAllProductsQuery();

        // We use the paginator to paginate the results of the query
        $products = $this->paginator->paginate(
        // Doctrine Query, not results
            $AllProductsQuery,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            8
        );

        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * Method to show the details of one product selected by its id
     *
     * @param Request $request
     * @return Response
     */
    #[Route('/products/{id}', name: 'app_products_show', requirements: ["id"=>"\d+"], methods: ['GET'])]
    public function show(Request $request)
    {
        // We get the id from the request
        $productId = $request->get('id');

        // We get the product of the database with the id $productId
        $product = $this->productService->getProductById($productId);

        return $this->render('product/product.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * Method to handle the AJAX request to increase the
     * number of likes
     *
     * @param Product $product
     * @return JsonResponse
     */
    #[Route('/products/like/{id}', name: 'app_products_like', methods: ['POST'])]
    public function like(Product $product)
    {
        // We increase the number of likes of 1
        $likes = $product->getLikes();
        $product->setLikes($likes + 1);

        // We save the new amount of likes for the product
        // in the database
        $this->productService->saveUpdatedProduct($product);

        // We return the JsonResponse with the new total of likes
        return new JsonResponse(['likes' => $product->getLikes()]);


    }
}