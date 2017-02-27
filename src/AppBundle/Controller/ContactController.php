<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
//use AppBundle\Entity\Circuit;

/**
 * Contact controller.
 */
class ContactController extends Controller
{
    /**
     * Formulaire de contact
     *
     * @Route("/contact", name="contact_index")
     * @Method("GET")
     */
    public function indexAction()
    {
		$email = '';
		
        return $this->render('contact.html.twig', array(
        	'email' => $email
        ));
    }


}
