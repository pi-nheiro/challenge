<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Review;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;

class ProductFixtures extends Fixture
{
  public function load(ObjectManager $manager): void{
    $faker = Factory::create();
    
    $batchSize = 10;
    $totalProducts = 1000;
    $reviewsPerProduct = 10000;

    for ($i = 0; $i < $totalProducts; $i++){
      $product = new Product();
      $product->setName($faker->words(2, true));
      $product->setPrice($faker->randomFloat(2, 10, 2000));
      $product->setDescription($faker->paragraph(3));
      $product->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-2 years', 'now')));

      $manager->persist($product);

      for ($j = 0; $j < $reviewsPerProduct; $j++){
        $review = new Review();
        $review->setProduct($product);
        $review->setRating($faker->randomFloat(2, 0, 10));
        $review->setComment($faker->sentence(4));
        $review->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween($product->getCreatedAt()->format('Y-m-d H:i:s'), 'now')));

        $manager->persist($review);
      }

      if ($i %$batchSize === 0){
        $manager->flush();
        $manager->clear();
        echo "Inserted: $i products...\n";
      }      
    }
    $manager->flush();
  }
}

