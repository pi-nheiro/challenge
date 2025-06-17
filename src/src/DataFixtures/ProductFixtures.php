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
    
    $batchSize = 50;
    $totalProducts = 10;
    $reviewsPerProduct = 5;

    for ($productData = 0; $productData < $totalProducts; $productData++){
      $product = new Product();
      $product->setName($faker->words(2, true));
      $product->setPrice($faker->randomFloat(2, 10, 2000));
      $product->setDescription($faker->paragraph(3));
      $product->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-2 years', 'now')));

      $manager->persist($product);

      for ($reviewData = 0; $reviewData < $reviewsPerProduct; $reviewData++){
        $review = new Review();
        $review->setProduct($product);
        $review->setRating($faker->randomFloat(2, 2, 5));
        $review->setComment($faker->sentence(4));
        $review->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween($product->getCreatedAt()->format('Y-m-d H:i:s'), 'now')));

        $manager->persist($review);
        $manager->flush();
        $manager->detach($review);

        if($reviewData % $batchSize == 0){
          $manager->flush();
          echo "Inserted: $reviewData reviews no product $productData...\n";
        }
      } 
      $manager->flush();
      $manager->detach($product);
      $manager->clear();
    }
    $manager->flush();
    $manager->clear();
  }

  // public function load(ObjectManager $manager): void{ 
        
  //   $faker = \Faker\Factory::create();
  //   $handle = fopen('fixtures.sql', 'w');

  //   for ($i = 0; $i < 100; $i++) {
  //       $name = $faker->words(2, true);
  //       $price = $faker->randomFloat(2, 10, 2000);
  //       $description = $faker->paragraph(3);
  //       $createdAt = $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d H:i:s');

  //       fwrite($handle, "INSERT INTO product (name, price, description, created_at) VALUES ('$name', $price, '$description', '$createdAt');\n");

  //       $productId = $i + 1; // simula ID autoincrementado

  //       for ($j = 0; $j < 200; $j++) {
  //           $rating = $faker->randomFloat(2, 0, 10);
  //           $comment = $faker->sentence(4);
  //           $reviewDate = $faker->dateTimeBetween($createdAt, 'now')->format('Y-m-d H:i:s');
  //           fwrite($handle, "INSERT INTO review (product_id, rating, comment, created_at) VALUES ($productId, $rating, '$comment', '$reviewDate');\n");
  //           echo "Review $j para o produto $i...\n";
  //       }
  //   }
  //   fclose($handle);
  // }


}

