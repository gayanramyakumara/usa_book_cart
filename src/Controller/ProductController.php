<?php

namespace App\Controller;
 
use App\Entity\Book;
use App\Entity\Cart;
use App\Entity\Category;
use ProxyManager\ProxyGenerator\ValueHolder\MethodGenerator\Constructor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProductController extends AbstractController
{
    private $session;
    const  CART_CAT_CHILDREN    = "Children";
    const  CART_CAT_FICTION     = "Fiction";
    
    public function index()
    {
        $categoryList = null;

        try{

            $itemList       = $this->getDoctrine()->getRepository(Book::class)->findAll();
            $categoryList   = $this->getDoctrine()->getRepository(Category::class)->findAll();
            
            if ($itemList == null) {
                $this->addFlash('error', 'No Product Details Found.!');
            }

        } catch (\Exception $ex) {
            $this->addFlash('error', 'Exception Found - '. $ex->getMessage());
        }

        return $this->render('default/index.html.twig', [
            'controller_name' => 'ProductController',
            'data'  =>  $itemList,
            'categoryItem' => $categoryList
        ]);
    }

    public function category($id){
        $itemList       = $this->getDoctrine()->getRepository(Book::class)->findByCategoryId($id);
        $categoryList   = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('default/index.html.twig', [
            'controller_name' => 'ProductController',
            'data'  =>  $itemList,
            'categoryItem' => $categoryList
        ]);
    }

     /**
     * Description : This action use to update cart table.
     * @return array
     */
    public function carts(Request $request)
    {
        $itemList   = [];
        $itemList   = $this->getDoctrine()->getRepository(Cart::class)->findAll();
        $data        = [];
        $couponCode = null;

        $data['itemInfo'] = [];
        try {

            // Validate with the database Couple table

            if(!empty($request->request->get('couponCode'))){
                $couponCode =   $request->request->get('couponCode');
            }
             
            if($itemList != NULL){

                $totalBookCount = 0;
                $totalAmount    = 0;
                $additionalDiscount = 0;
                $couponDiscount = 0;

                $childBookCount = 0;
                $childBookTotal = 0;

                foreach ($itemList as $itemValue){

                    $categoryName   = $itemValue->getBookId()->getCategoryId()->getCategoryName();
                    $bookCount      = $itemValue->getCount();
                    $bookPrice      = $itemValue->getBookId()->getPrice();

                    $totalBookCount = $totalBookCount + $bookCount;
                    $totalAmount    =   $totalAmount + ($bookPrice * $bookCount);
                    $discountValue  = ($categoryName== self::CART_CAT_CHILDREN) ? $this->calculateChildrenDiscount($categoryName , $bookCount , $bookPrice) : 0;
                   
                    if($categoryName== self::CART_CAT_CHILDREN){

                        $childBookTotal =  $childBookTotal + $bookPrice;
                        $childBookCount = $childBookCount + $bookCount;

                    }

                   
                    if($totalBookCount >=10){
                        $additionalDiscount = 5;
                    }

                    if(!empty($couponCode)){
                        $discountValue = 0;
                        $additionalDiscount = 0;
                        $couponDiscount = 15;
                    }
                  
                    $arr2 = array(
                        'cartId'        => $itemValue->getId(),
                        'bookImgPath'   => $itemValue->getBookId()->getImagePath(),
                        'bookPrice'     => $bookPrice,
                        'bookName'      => $itemValue->getBookId()->getBookName(),
                        'bookCount'     => $bookCount,
                        'categoryName'  => $categoryName,
                        'discount'      => $discountValue,
                        'totalByCategory'  => ($bookPrice * $bookCount) - $discountValue
                    );
                    array_push($data['itemInfo'], $arr2);
                }

                if($childBookCount){
                     $childBookDiscount =  $this->calculateChildrenDiscount(self::CART_CAT_CHILDREN , $childBookCount , $childBookTotal);
                }
                

      
            }else{
                $this->addFlash('error', 'You have no items in your shopping cart.');
            }
        } catch (\Exception $ex) {
            $this->addFlash('error', 'Exception Found - '. $ex->getMessage());
        }
 
        return $this->render('product/carts.html.twig', [
            'controller_name'   => 'ProductController',
            'cartData'          => $data,
            'additionalDiscount'=> (!empty($additionalDiscount)) ? $additionalDiscount : 0,
            'totalAmount'       => (!empty($totalAmount)) ? $totalAmount: 0,
            'couponCode'        => $couponCode,
            'couponDiscount'    => (!empty($couponDiscount)) ? $couponDiscount : 0,
            'childDiscount'    => (!empty($childBookDiscount)) ? $childBookDiscount : 0
        ]);
    }

    public function updateCart($id){  

        $entityManager  = $this->getDoctrine()->getManager();
        $cartRecord     = $entityManager->getRepository(Cart::class)->findOneBy(['bookId' => $id]);
       
        try {
        if($cartRecord == NULL ){

            $entityManager = $this->getDoctrine()->getManager();
            $cart = new Cart();
            $cart->setBookId($this->getDoctrine()->getRepository(Book::class)->findOneById($id));
            $cart->setCount(1);
            $entityManager->persist($cart); 
        }else{
            $updateCount = $cartRecord->getCount();
            $cartRecord->setCount($updateCount+1);
            $entityManager->persist($cartRecord); 
        }  

        if($entityManager->flush() == NULL){
            $this->addFlash('success', 'You added item to your shopping cart.!');
        }else{
            $this->addFlash('error', 'Something went wrong.!');
        }

    } catch (\Exception $ex) {
        $this->addFlash('error', 'Exception Found - '. $ex->getMessage());
    }
 
     
        return $this->redirectToRoute('index');
        
    }


    public function deleteItem($id){
        $entityManager  = $this->getDoctrine()->getManager();
        $item           = $entityManager->getRepository(Cart::class)->find($id);

        try {

            if($item != NULL ){
                $entityManager->remove($item);
                $entityManager->flush();
                $this->addFlash('success', 'Item successfully deleted.!');
            }else{
                $this->addFlash('error', 'Something went wrong.!');
            }

        } catch (\Exception $ex) {
            $this->addFlash('error', 'Exception Found - '. $ex->getMessage());
        }
        return $this->redirectToRoute('carts');
    }


    public    function calculateChildrenDiscount($category , $count , $price){

        $discount = 0;
    
        if($category == self::CART_CAT_CHILDREN && $count >= 5){
            $discount = (($price * $count) * 10 / 100);
        }

        return $discount;
    }

 


    public function ShoppingCartDetails(){


        $itemList = $this->getDoctrine()->getRepository(Book::class)->findAll();

        try {

            if($itemList != NULL){

                return $this->render('default/index.html.twig', [
                    'controller_name' => 'ProductController',
                    'data'  =>  $itemList
                ]);

            }else{
                $this->addFlash('error', 'You have no items in your shopping cart.');
            }
        } catch (\Exception $ex) {
            $this->addFlash('error', 'Exception Found - '. $ex->getMessage());
        }
    }



}
