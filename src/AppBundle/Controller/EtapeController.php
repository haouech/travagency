<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Circuit;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Etape;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

/**
 * Circuit controller.
 */
class EtapeController extends Controller
{
	/**
	 * Création d'une nouvelle étape d'un circuit caractérisé par id
	 * @Route("/circuit/{id}/new_etape", name="new_etape", requirements={
	 *              "id": "\d+"
	 *     })
	 *
	 * Ou modification d'une étape avec id = "etape_id"
	 * @Route("/circuit/{id}/edit_etape/{etape_id}", name="edit_etape", requirements={
	 * 				"id": "\d+"
	 * 		})
	 *
	 */
	public function showAction(Circuit $circuit, $etape_id = null, Request $request)
	{

		if (!$circuit) { 	// Erreur 404 Not Found, circuit n'existe pas
			throw $this->createNotFoundException();
		}

		if($etape_id){
			// Si modification d'étape
			$newEtape = $this->getDoctrine()->getRepository("AppBundle:Etape")->find($etape_id);
			if(!$newEtape){
				// 404 Not found
				throw $this->createNotFoundException();
			}
		} else {
			$newEtape = new Etape();
		}


		$etapeForm = $this->createFormBuilder($newEtape)
		->add('numeroEtape', TextareaType::class)
		->add('villeEtape', TextareaType::class)
		->add('nombreJours', TextareaType::class)
		->add('save', SubmitType::class, array('label' => 'Ajouter'))
		->getForm();
		$etapeForm->handleRequest($request);

		if ($etapeForm->isSubmitted() && $etapeForm->isValid()) {
			// Gestion de la soumission du formulaire
			$entityManager = $this->getDoctrine()->getManager();
			if(!$etape_id){ // Si nouvelle étape, on spécifie le circuit dont elle appartient
				$newEtape->setCircuit($circuit);
				$circuit->addEtape($newEtape);
			}

			$entityManager->persist($newEtape);
			$entityManager->persist($circuit);

			$entityManager->flush();

			$this->addFlash('success', 'Etape '. $newEtape->getId() .' créée avec succès');
			return $this->redirectToRoute('circuit_show', ['id' => $circuit->getId()]);

		}

		return $this->render('etape/new.html.twig', array(
				'form' => $etapeForm->createView()
		));
	}


	/**
	 * Suppression d'une étape "etape_id" d'un circuit ayant un id = "id"
	 * @Route("/circuit/{id}/delete_etape/{etape_id}", name="delete_etape", requirements={
	 *              "id": "\d+"
	 *     })
	 *
	 */
	public function deleteAction($id ,$etape_id, Request $request)
	{
		$etape = $this->getDoctrine()->getRepository('AppBundle:Etape')->find($etape_id);
		$em = $this->getDoctrine()->getManager();
		$em->remove($etape);
		$em->flush();

		return $this->redirectToRoute('circuit_show', array('id' => $id));
	}
}
