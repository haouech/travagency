<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('accueil.html.twig', [
           
        ]);
    }
    
    /**
     * @Route("/contact", name="contact")
     */
    public function Contact(Request $request)
    {
    	// replace this example code with whatever you need
    	return $this->render('contact.html.twig', [
    			 
    	]);
    }
    
    
    
}
