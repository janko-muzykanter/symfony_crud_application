<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
  private $manager;

  public function __construct(
    ManagerRegistry $registry,
    EntityManagerInterface $manager
  ) {
    parent::__construct($registry, Product::class);
    $this->manager = $manager;
  }

  public function addOrUpdateProduct(Product $product_entity)
  {
    $this->manager->persist($product_entity);
    $this->manager->flush();
  }

  public function removeProduct(Product $product_entity)
  {
    $this->manager->remove($product_entity);
    $this->manager->flush();
  }
}