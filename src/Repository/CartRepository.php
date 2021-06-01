<?php

namespace App\Repository;

use App\Entity\Cart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Cart|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cart|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cart[]    findAll()
 * @method Cart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartRepository extends ServiceEntityRepository
{
  private $manager;

  public function __construct(
    ManagerRegistry $registry,
    EntityManagerInterface $manager
  ) {
    parent::__construct($registry, Cart::class);
    $this->manager = $manager;
  }

  public function addOrUpdateToCart(Cart $cart_entity)
  {
    $this->manager->persist($cart_entity);
    $this->manager->flush();
  }

  public function removeFromCart(Cart $cart_entity)
  {
    $this->manager->remove($cart_entity);
    $this->manager->flush();
  }
}
