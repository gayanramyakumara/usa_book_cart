<?php

namespace App\Controller;
use App\Entity\Cart;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{

    public function index()
    {
        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
        ]);
    }


    public function cartItemCount(){
        $itemCount      =  0;
        $em             = $this->getDoctrine()->getManager();
        $getCartData    = $em->getRepository(Cart::class)->findAll();

        if($getCartData){
            $itemCount = count($getCartData);
        }

        return $this->render('default/cartItemCount.html.twig', [
            'count'         => $itemCount
        ]);
    }
}
