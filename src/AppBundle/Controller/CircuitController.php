<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Circuit;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\Etape;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use AppBundle\Entity\Comment;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;



/**
 * Circuit controller.
 */
class CircuitController extends Controller
{
    /**
     * Lists all Circuit entities.
     * Afficher tous les circuits autorisés par les collaborateurs.
     *
     * @Route("/circuit/", name="circuit_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
        	$circuits = $em->getRepository('AppBundle:Circuit')->findAll();
        } else {
          // Si l'utilisateur non connecté, on n'affiche que les circuits autorisés.
        	$circuits = $em->getRepository('AppBundle:Circuit')->findBycheckbox(true);
        }

        return $this->render('circuit/index.html.twig', array(
            'circuits' => $circuits,
        ));
    }

    /**
     * Finds and displays a Circuit entity.
     * Afficher les détails d'un circuit caractérisé par son id.
     *
     * @Route("/circuit/{id}", name="circuit_show", requirements={
	 *              "id": "\d+"
	 *     })
     */
    public function showAction(Circuit $circuit, Request $request)
    {

    	if (!$circuit) {
    		// Erreur 404 Not Found, id erroné
    		throw $this->createNotFoundException();
    	}
      // Calcul et mise à jour de la valeur de durée.
    	$duree = 0;
    	foreach ($circuit->getEtapes() as $iterator){
    		$duree = $duree + $iterator->getNombreJours();
    	}
      // Mise à jour des variables
    	$arr_etapes = $circuit->getEtapes();
    	$villeDepart = $arr_etapes[0]->getVilleEtape();
    	$villeArrivee = $arr_etapes[sizeof($arr_etapes)-1]->getVilleEtape();
    	$circuit->setDureeCircuit($duree);
    	$circuit->setVilleArrivee($villeArrivee);
    	$circuit->setVilleDepart($villeDepart);
      // Creation du formulaire des commentaires
    	$newcomment = new Comment();

    	$commentformBuilder = $this->createFormBuilder($newcomment);
      // Si l'utilisateur est connecté, le commentaire contiendra automatiquement son adresse email
      // Sinon on ajoute un champs pour la saisie de l'adresse
    	if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
    		$commentformBuilder->add('authorEmail', EmailType::class);
    	}
    	$commentform = $commentformBuilder->add('content', TextareaType::class)
    	->add('save', SubmitType::class, array('label' => 'Commenter'))
    	->getForm();
    	$commentform->handleRequest($request);

    	if ($commentform->isSubmitted() && $commentform->isValid()) {
        // Si utilisateur connecté, le commentaire contiendra son adresse email
    		if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')){
    			$newcomment->setAuthorEmail($this->getUser()->getEmail());
    		}
    		$entityManager = $this->getDoctrine()->getManager();

    		$circuit->addComment($newcomment);

    		$entityManager->persist($newcomment);
    		$entityManager->persist($circuit);

    		$entityManager->flush();

    		$this->addFlash('success', 'Commentaire '. $newcomment->getId() .' créé avec succès');
    		return $this->redirectToRoute('circuit_show', ['id' => $circuit->getId()]);

    	}

	    return $this->render('circuit/show.html.twig', array('circuit' => $circuit,
	    		'commentform' => $commentform->createView()
	    ));


    }


    /**
     * Creates a new Circuit entity, or modify an existing one.
     *
     * Create a new circuit
     * Créer un nouveau circuit
     * @Route("/circuit/new", name="admin_circuit_new")
     *
     * Edit contents of an existing circuit
     * Ou modifier un circuit existant
     * @Route("/circuit/{id}/edit", name="admin_circuit_edit", requirements={
	 *              "id": "\d+"
	 *     })
     */
    public function newAction($id = null, Request $request)
    {
      // Si on choisit de modifier un circuit
    	if($id){
    		$circuit = $this->getDoctrine()->getRepository('AppBundle:Circuit')->find($id);

    		if(!$circuit){
    			// 404 Not found
    			throw $this->createNotFoundException();
    		}
    	} else {
    		$circuit = new Circuit();
    	}

    	//Construire le formulaire
    	$formBuilder = $this->createFormBuilder($circuit)
    	->add('Description', TextareaType::class)
    	->add('Paysdepart', TextareaType::class, array('label'=>'Pays'))
    	->add('Villedepart', TextareaType::class, array('label'=>'ville depart'))
    	->add('Villearrivee', TextareaType::class, array('label'=>'ville arrivee'))
    	->add('checkbox', ChoiceType::class, array('label'=>'Confirmer le circuit
    			', 'choices'  => array(
        'Oui' => true,
        'Non' => false,)
    )) ;

    	$form = $formBuilder->add('save', SubmitType::class, array('label' => 'Enregistrer'))
    	->getForm();

    	$form->handleRequest($request);

    	if($form->isSubmitted() && $form->isValid()){

    		$em = $this->getDoctrine()->getManager();


    		if(!$id){
          // Si création d'un nouveau circuit, on met deux étapes par défaut
          // pour avoir une ville de départ et une ville d'arrivée
      		$etape1 = new Etape();
      		$etape1->setCircuit($circuit);
      		$etape1->setNombreJours(1);
      		$etape1->setNumeroEtape(1);
      		$etape1->setVilleEtape($circuit->getVilleDepart());
      		$etape1->setCircuit($circuit);
      		$circuit->addEtape($etape1);
      		$em->persist($etape1);


      		$etape2 = new Etape();
      		$etape2->setCircuit($circuit);
      		$etape2->setNombreJours(1);
      		$etape2->setNumeroEtape(2);
      		$etape2->setVilleEtape($circuit->getVilleArrivee());
      		$etape2->setCircuit($circuit);
      		$circuit->addEtape($etape2);
      		$em->persist($etape2);

    		}

    		$em->persist($circuit);
    		$em->flush();

    		$message = 'Circuit créé avec succès';
    		$this->addFlash('success', $message);

    		return $this->redirectToRoute('circuit_show', ['id' => $circuit->getId()]);
    	}
    	return $this->render('circuit/new_circuit.html.twig', ['id' => $circuit->getId(),
    			'form' => $form->createView()]);
    }


    /**
     * Supprimer un circuit
     *
     * @Route("/delete/{id}", name="delete_id", requirements={
     *              "id": "\d+"
     *     })
     * @Method("GET")
     */
    public function deleteAction(Circuit $circuit)
    {

    	$em = $this->getDoctrine()->getManager();
    	$em->remove($circuit);
    	$em->flush();

    	return $this->redirectToRoute('circuit_index');
    }


}
