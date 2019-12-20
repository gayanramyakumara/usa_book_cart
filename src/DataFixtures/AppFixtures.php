<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //SETUP CATEGORIES
        $categoryArray = array( 'Children', 'Fiction');
        foreach($categoryArray as  $val){
           // $category = $manager->getRepository(\App\Entity\Category::class)->findOneBy(array('categoryName' =>$val));

            //if(!$category){
                $newCategory = new \App\Entity\Category();
                $newCategory->setCategoryName($val);
                $manager->persist($newCategory);
                $manager->flush();
            //}
        }

        //SETUP BOOKS
        $bookArray = array(
           array(
            'bookName'=>'Bibile Story',
            'price'=>'35',
            'stock'=>'10',
            'author'=>'karen jones',
            'imagePath'=>'http://images-eu.ssl-images-amazon.com/images/I/51Blaqtt0WL.jpg',
            'categoryId'=>'Children',
           ),
           array(
            'bookName'=>'Bibile Story 2',
            'price'=>'30',
            'stock'=>'20',
            'author'=>'karen jones',
            'imagePath'=>'http://images-eu.ssl-images-amazon.com/images/I/51gHvbCSOOL.jpg',
            'categoryId'=>'Fiction',
           ),
           array(
            'bookName'=>'Defending Boyhood',
            'price'=>'130',
            'stock'=>'210',
            'author'=>'Anthony Esolen',
            'imagePath'=>'http://127.0.0.1:8000/img/book/child/DefendingBoyhood.jpeg',
            'categoryId'=>'Children',
           ),
           array(
            'bookName'=>'The Story of Civilization',
            'price'=>'190',
            'stock'=>'32',
            'author'=>'Anthony Esolen',
            'imagePath'=>'http://127.0.0.1:8000/img/book/child/civilization.jpg',
            'categoryId'=>'Children',
           ),
           array(
            'bookName'=>' Children Picture Books',
            'price'=>'60',
            'stock'=>'10',
            'author'=>'Anthony Esolen',
            'imagePath'=>'http://127.0.0.1:8000/img/book/child/ChildrenPictureBooks.jpg',
            'categoryId'=>'Children',
           ),
           array(
            'bookName'=>'The Lion Children Bible',
            'price'=>'130',
            'stock'=>'60',
            'author'=>'Anthony Esolen',
            'imagePath'=>'http://127.0.0.1:8000/img/book/child/TheLionChildrenBible.jpg',
            'categoryId'=>'Children',
           ),
           array(
            'bookName'=>'The Land_Founding',
            'price'=>'30',
            'stock'=>'20',
            'author'=>'Anthony Esolen',
            'imagePath'=>'http://127.0.0.1:8000/img/book/fiction/TheLand_Founding.jpg',
            'categoryId'=>'Fiction',
           ),
           array(
            'bookName'=>'Underworld - Blood Wars',
            'price'=>'30',
            'stock'=>'20',
            'author'=>'Anthony Esolen',
            'imagePath'=>'http://127.0.0.1:8000/img/book/fiction/udnerw.jpg',
            'categoryId'=>'Fiction',
           ),
           array(
            'bookName'=>'A Swirl of Ocean',
            'price'=>'30',
            'stock'=>'20',
            'author'=>'Melissa Sarno',
            'imagePath'=>'http://127.0.0.1:8000/img/book/fiction/ASwirlofOcean.jpg',
            'categoryId'=>'Fiction',
           ) ,
           array(
            'bookName'=>'Wilbur smith courtney series',
            'price'=>'30',
            'stock'=>'20',
            'author'=>'Anthony Esolen',
            'imagePath'=>'http://127.0.0.1:8000/img/book/fiction/Wilburcourtneyseries.jpg',
            'categoryId'=>'Fiction',
           ) ,
           array(
            'bookName'=>'The Sacred History',
            'price'=>'30',
            'stock'=>'20',
            'author'=>'Anthony Esolen',
            'imagePath'=>'http://127.0.0.1:8000/img/book/fiction/TheSacredHistory.jpg',
            'categoryId'=>'Fiction',
           )
        );
        foreach($bookArray as $val){
            $newBook= new \App\Entity\Book();
            $newBook->setBookName($val['bookName']);
            $newBook->setPrice($val['price']);
            $newBook->setStock($val['stock']);
            $newBook->setAuthor($val['author']);
            $newBook->setImagePath($val['imagePath']);
            $newBook->setCategoryId($manager->getRepository(\App\Entity\Category::class)->findOneByCategoryName($val['categoryId']));
            $manager->persist($newBook);
            $manager->flush();
        }

        //SETUP COUPON
        $couponArray = array('1001','1002','1003','1004','1005','1006');
        foreach($couponArray as $val){
            $newCoupon = new \App\Entity\Coupon();
            $newCoupon->setCouponNumber($val);
            $manager->persist($newCoupon);
            $manager->flush();
        }

    }
}
