<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Cart;
use App\Entity\Category;
use App\Entity\Invoice;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\ProductController;
use App\Entity\Coupon;
use App\Entity\InvoiceItem;
use Symfony\Component\HttpFoundation\Request;

class CheckoutController extends AbstractController
{

    private $childDiscount      = 0 ;
    private $couponDiscount     = 0;
    private $additionalDiscount = 0;
    private $coupon             = null;

    /**
     * Description : This action use to update checkout invoice from the cart.after update the invice table succesfully the cart bucket  will be deleted.
     * @return array
     */
    public function index(Request $request)
    {
        $em             = $this->getDoctrine()->getManager();
        $getCartData    = $em->getRepository(Cart::class)->findAll();
        $getInvoiceData = $em->getRepository(Invoice::class)->findOneBy(array('status' => 0), array('id'=>'desc'));
        $totalBookCount = 0;
     
        if($getCartData){
            
            $productC = new ProductController();

            foreach ($getCartData as $itemValue){
                $categoryName   = $itemValue->getBookId()->getCategoryId()->getCategoryName();
                $bookCount      = $itemValue->getCount();
                $bookPrice      = $itemValue->getBookId()->getPrice();
                $totalBookCount = $totalBookCount + $bookCount;

                $this->childDiscount      = ($categoryName== ProductController::CART_CAT_CHILDREN) ?  $productC->calculateChildrenDiscount($categoryName , $bookCount , $bookPrice) : 0;
                $this->additionalDiscount = $this->getCartAdditionalDiscount($totalBookCount);
                 
            }

            $subTotal   = $this->getCartTotal($getCartData);
            $discount   = $this->getDiscount() ;
            $total      = $subTotal - $discount;

            $newInvoice = new Invoice();
            $newInvoice->setSubTotal($subTotal);
            $newInvoice->setChildernDiscount($this->childDiscount);
            $newInvoice->setAdditionalDiscount($this->additionalDiscount);
            $newInvoice->setCouponDiscount($this->couponDiscount);
            $newInvoice->setDiscountAmount($discount);
            $newInvoice->setTotal($total);
            $newInvoice->setCouponId($this->coupon);
            $newInvoice->setStatus(0);

            $em->persist($newInvoice);
            $em->flush();

            foreach ($getCartData as $itemValue){
                
                $newInvoiceItem = new InvoiceItem();
                $newInvoiceItem->setInvoiceId($newInvoice);
                $newInvoiceItem->setBookId($itemValue->getBookId());
                $newInvoiceItem->setCount($itemValue->getCount());
                $em->persist($newInvoiceItem);
                $em->flush();

                //remove from the Cart table
                $em->remove($itemValue);
                $em->flush();
            }

            return $this->redirect($this->generateUrl('checkout'));

        }
        
        return $this->render('checkout/index.html.twig', [
            'controller_name' => 'CheckoutController',
            'invoice' => $getInvoiceData,
        ]);
    }

    /**
     * Description : This method use to redeem the invoice from the cart.
     */
    public function redeemCoupon(Request $request){

        $em             = $this->getDoctrine()->getManager();
        $invoiceData    = $em->getRepository(Invoice::class)->findOneBy(array('status' => 0), array('id'=>'desc'));
        $this->getCouponDiscount($request , $invoiceData->getInvoiceItem());
            
        $subTotal   = $this->getCartTotal($invoiceData->getInvoiceItem());
        $discount   = $this->getDiscount() ;
        $total      = $subTotal - $discount;

        if($this->coupon){
            $invoiceData->setCouponId($this->coupon);
            $invoiceData->setStatus(1);
        }
        
        $invoiceData->setCouponDiscount($this->couponDiscount);
        $invoiceData->setDiscountAmount($this->couponDiscount);
        $invoiceData->setTotal($total);
        $em->flush();
        
        return $this->redirect($this->generateUrl('checkout'));

    }

    /**
     * Description : This method use to pay the cart without any coupon.
     * 
     */
    public function payNow(){

        $em             = $this->getDoctrine()->getManager();
        $invoiceData    = $em->getRepository(Invoice::class)->findOneBy(array('status' => 0), array('id'=>'desc'));
            $invoiceData->setStatus(1);
        $em->flush();

        $this->addFlash('success', 'Successfully paid !');
        return $this->redirect($this->generateUrl('index'));
    }

    /**
     * Description : This method use to get the common disount.
     */
    private function getDiscount(){

        if($this->couponDiscount){
            return $this->couponDiscount;
        }else{

            return $this->childDiscount + $this->additionalDiscount ;
        }

    }

    /**
     * Description : This method use to get the total in the cart.
     * @return $total
     */
    private function getCartTotal($getCartData){
        $total = 0 ;

        foreach($getCartData as $getCartValue){
            $total = $total +  ($getCartValue->getBookId()->getPrice() * $getCartValue->getCount());
        }

        return $total;
    }

    /**
     * Description : This method use to get the additional discount in the cart.
     * If user buy 10 books from each category you get 5% additional discount from the total bill.
     * 
     * @return $discount
     */
    private function getCartAdditionalDiscount($totalBookCount){

        $discount = 0;
        if($totalBookCount >=10){
            $discount = 5;
        }
        return $discount;

    }

    /**
     * Description : This method use to get the coupon discount in the cart.
     * If user have a coupon code (which you can enter and redeem from the invoice page itself) you get a 15% discount for the total bill. 
     * In this case, all other discounts will be invalidated.
     */
    private function getCouponDiscount($request ,$getCartData){

        if(!empty($request->request->get('couponCode'))){
            $couponCode     = $request->request->get('couponCode');
            $entityManager  = $this->getDoctrine()->getManager();
            $coupon         = $entityManager->getRepository(Coupon::class)->findOneBy(['couponNumber' => $couponCode]);

            if($coupon && !$coupon->getInvoice()){

                $this->addFlash('success', $couponCode.' coupon successfully paid / redeemed!');

                $this->couponDiscount       = ($this->getCartTotal($getCartData) * 15) / 100;
                $this->additionalDiscount   = 0;
                $this->childDiscount        = 0;
                $this->coupon               = $coupon;
            }else{
                $this->addFlash('error','Invalid coupon number!');
            }
        }
    }
}
