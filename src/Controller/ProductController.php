<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Cart;
use App\Repository\ProductRepository;
use App\Repository\CartRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
  private $productRepository;

  private $cartRepository;

  public function __construct(ProductRepository $productRepository, CartRepository $cartRepository)
  {
    $this->productRepository = $productRepository;
    $this->cartRepository = $cartRepository;
  }

  /**
   * @Route("/products/{id}", name="get_product_details", methods={"GET"})
   */
  public function getProduct($id): JsonResponse
  {
    $product_entity = $this->productRepository->findOneBy(['id' => $id]);

    if (null === $product_entity) {
      return new JsonResponse('Product not found', Response::HTTP_OK);
    }

    $data = [
      'id' => $product_entity->getId(),
      'name' => $product_entity->getName(),
      'price' => $product_entity->getPrice(),
    ];

    return new JsonResponse($data, Response::HTTP_OK);
  }

  /**
   * @Route("/productsList", name="get_products_list", methods={"GET"})
   */
  public function getProductsList(Request $request): JsonResponse
  {
    $collection = [];
    $products_list = $this->productRepository->findAll();

    foreach ($products_list as $product) {
      $item = [];
      $item['id'] = $product->getId();
      $item['name'] = $product->getName();
      $item['price'] = $product->getPrice();
      $collection[] = $item;
    }

    return new JsonResponse($collection, Response::HTTP_OK);
  }

  /**
   * @Route("/product/add", name="add_product", methods={"POST"})
   */
  public function addProduct(Request $request): JsonResponse
  {
    $data = json_decode($request->getContent(), true);

    $name = $data['name'] ?? null;
    $price = $data['price'] ?? null;

    if (
      null === $name ||
      null === $price
    ) {
      return new JsonResponse('Payload is not valid', Response::HTTP_OK);
    }

    $product_entity = new Product();
    $product_entity
      ->setName($name)
      ->setPrice($price);

    $this->productRepository->addOrUpdateProduct($product_entity);
    return new JsonResponse(['status' => 'Product added to the store.'], Response::HTTP_CREATED);
  }

  /**
   * @Route("/product/update", name="update_product", methods={"PUT"})
   */
  public function updateProduct(Request $request): JsonResponse
  {
    $data = json_decode($request->getContent(), true);

    $id = $data['id'] ?? -1;
    $name = $data['name'] ?? null;
    $price = $data['price'] ?? null;

    $product_entity = $this->productRepository->findOneBy(['id' => $id]);

    if (null === $product_entity) {
      return new JsonResponse('Product not found', Response::HTTP_OK);
    }

    $product_entity->setName(null !== $name ? $name : $product_entity->getName());
    $product_entity->setPrice(null !== $price ? $price : $product_entity->getPrice());

    $this->productRepository->addOrUpdateProduct($product_entity);
    return new JsonResponse(['status' => "Product no. {$id} has been updated."], Response::HTTP_CREATED);
  }

  /**
   * @Route("/product/delete/{id}", name="delete_product", methods={"DELETE"})
   */
  public function deleteProduct($id): JsonResponse
  {
    $product_entity = $this->productRepository->findOneBy(['id' => $id]);

    if (null === $product_entity) {
      return new JsonResponse('Product not found', Response::HTTP_OK);
    }

    $this->productRepository->removeProduct($product_entity);
    return new JsonResponse(['status' => "Product no. {$id} has been deleted."], Response::HTTP_OK);
  }

  /**
   * @Route("/cartList/{id}", name="get_products_list_from_cart", methods={"GET"})
   */
  public function getProductsListFromCart($id): JsonResponse
  {
    $collection = [];
    $cart_list = $this->cartRepository->findBy(['cartId' => $id]);

    foreach ($cart_list as $cart) {
      $item = [];
      $item['id'] = $cart->getCartId();
      $item['product'] = $cart->getProduct()->getName();
      $collection[] = $item;
    }

    return new JsonResponse($collection, Response::HTTP_OK);
  }

  /**
   * @Route("/cart/add", name="add_product_to_cart", methods={"POST"})
   */
  public function addProductToCart(Request $request): JsonResponse
  {
    $data = json_decode($request->getContent(), true);

    $cartId = $data['cartId'] ?? null;
    $product = !isset($data['productId']) ? null : $this->productRepository->findOneBy(['id' => $data['productId']]);

    if (
      null === $cartId ||
      null === $product
    ) {
      return new JsonResponse('Payload is not valid', Response::HTTP_OK);
    }

    $product_list = $this->cartRepository->findBy(['cartId' => $cartId]);

    if(\count($product_list) >= 3) {
      return new JsonResponse(['status' => 'The cart has exceeded the maximum products amount (3).'], Response::HTTP_OK);
    }

    $cart_entity = new Cart();
    $cart_entity
      ->setCartId($cartId)
      ->setProduct($product);


    $this->cartRepository->addOrUpdateToCart($cart_entity);
    return new JsonResponse(['status' => 'Product has been added to the cart.'], Response::HTTP_CREATED);
  }

  /**
   * @Route("/cart/delete/{id}", name="delete_product_from_cart", methods={"DELETE"})
   */
  public function deleteProductFromCart($id): JsonResponse
  {
    $cart_entity = $this->cartRepository->findOneBy(['id' => $id]);

    if (null === $cart_entity) {
      return new JsonResponse('Product not found', Response::HTTP_OK);
    }

    $this->cartRepository->removeFromCart($cart_entity);
    return new JsonResponse(['status' => "Product no. {$cart_entity->getProduct()->getName()} has been removed from cart."], Response::HTTP_OK);
  }
}