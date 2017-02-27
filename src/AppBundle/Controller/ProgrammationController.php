<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Circuit;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use AppBundle\Entity\ProgrammationCircuit;
use Doctrine\DBAL\Types\DateType;

/**
 * Programmation controller.
 */
class ProgrammationController extends Controller
{
	/**
	 * Création d'une nouvelle programmation d'un circuit caractérisé par id
	 * @Route("/circuit/{id}/new_prog", name="new_prog", requirements={
	 *              "id": "\d+"
	 *     })
	 *
	 * Ou modification d'une programmation avec id = "prog_id"
	 * @Route("/circuit/{id}/edit_prog/{prog_id}", name="edit_prog", requirements={
	 * 				"id": "\d+"
	 * 		})
	 *
	 */
	public function showAction(Circuit $circuit, $prog_id = null, Request $request)
	{

		if (!$circuit) { // Erreur 404 Not Found, circuit n'existe pas
			throw $this->createNotFoundException();
		}
		if($prog_id){
			// Si modification d'une programmation
			$newProg = $this->getDoctrine()->getRepository("AppBundle:ProgrammationCircuit")->find($prog_id);
			if(!$newProg){
				// 404 Not found
				throw $this->createNotFoundException();
			}
		} else {
			$newProg = new ProgrammationCircuit();
		}

		// Création du formulaire d'ajout ou de modification
		$progForm = $this->createFormBuilder($newProg)
		->add('dateDepart', \Symfony\Component\Form\Extension\Core\Type\DateType::class, array(
				"widget" => "choice",
		))
		->add('nombrePersonnes', TextareaType::class)
		->add('prix', TextareaType::class)
		->add('save', SubmitType::class, array('label' => 'Ajouter'))
		->getForm();
		$progForm->handleRequest($request);

		if ($progForm->isSubmitted() && $progForm->isValid()) {
			// Gestion de la soumission du formulaire
			$entityManager = $this->getDoctrine()->getManager();
			if(!$prog_id){ // Si nouvelle programmation, on spécifie le circuit dont elle appartient
				$newProg->setCircuit($circuit);

				$circuit->addProgrammation($newProg);
			}

			$entityManager->persist($newProg);
			$entityManager->persist($circuit);

			$entityManager->flush();

			$this->addFlash('success', 'Programmation Circuit '. $newProg->getId() .' créée avec succès');
			return $this->redirectToRoute('circuit_show', ['id' => $circuit->getId()]);

		}

		return $this->render('ProgrammationCircuit/new.html.twig', array(
				'form' => $progForm->createView()
		));
	}


	/**
	 * Suppression d'une programmation ayant un id "prog_id"
	 * @Route("/circuit/{id}/delete_prog/{prog_id}", name="delete_prog", requirements={
	 *              "id": "\d+"
	 *     })
	 *
	 */
	public function deleteAction($id ,$prog_id, Request $request)
	{
		$prog = $this->getDoctrine()->getRepository('AppBundle:ProgrammationCircuit')->find($prog_id);
		$em = $this->getDoctrine()->getManager();
		$em->remove($prog);
		$em->flush();

		return $this->redirectToRoute('circuit_show', array('id' => $id));
	}
}
