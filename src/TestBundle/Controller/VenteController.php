<?php

namespace TestBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use TestBundle\Entity\Vente;
use TestBundle\Form\VenteType;

/**
 * Vente controller.
 *
 */
class VenteController extends Controller
{
    /**
     * Lists all Vente entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $ventes = $em->getRepository('TestBundle:Vente')->findAll();

        return $this->render('TestBundle:vente:index.html.twig', array(
            'ventes' => $ventes,
        ));
    }

    /**
     * Creates a new Vente entity.
     *
     * meme fonction que Default:venteAction
     */
    public function newAction(Request $request)
    {
        $vente = new Vente();
        $form = $this->createForm('TestBundle\Form\VenteType', $vente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($vente);
            $em->flush();

            return $this->redirectToRoute('vente_show', array('id' => $vente->getId()));
        }

        return $this->render('TestBundle:vente:new.html.twig', array(
            'vente' => $vente,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a Vente entity.
     *
     */
    public function showAction(Vente $vente)
    {
        $deleteForm = $this->createDeleteForm($vente);

        return $this->render('TestBundle:vente:show.html.twig', array(
            'vente' => $vente,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Vente entity.
     *
     */
    public function editAction(Request $request, Vente $vente)
    {
        $deleteForm = $this->createDeleteForm($vente);
        $editForm = $this->createForm('TestBundle\Form\VenteType', $vente);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($vente);
            $em->flush();

            return $this->redirectToRoute('vente_edit', array('id' => $vente->getId()));
        }

        return $this->render('TestBundle:vente:edit.html.twig', array(
            'vente' => $vente,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Vente entity.
     *
     */
    public function deleteAction(Request $request, Vente $vente)
    {
        $form = $this->createDeleteForm($vente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($vente);
            $em->flush();
        }

        return $this->redirectToRoute('vente_index');
    }

    /**
     * Creates a form to delete a Vente entity.
     *
     * @param Vente $vente The Vente entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Vente $vente)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('vente_delete', array('id' => $vente->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
