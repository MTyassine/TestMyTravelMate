<?php

namespace TestBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use TestBundle\Entity\User;
use TestBundle\Form\UserType;

/**
 * User controller.
 *
 */
class UserController extends Controller
{
    /**
     * Lists all User entities.
     *
     * meme fonction que Default:affichagePointAction
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('TestBundle:User')->findAll();

        /*retourne le nombre total des points des le debut */

        foreach($users as $user){

            $ventes = $em->getRepository('TestBundle:Vente')->findVenteByUser($user);

            $point = 0;

            foreach($ventes as $vente){
                $point = $point + ($vente->getQuantite() * $vente->getProduit()->getPoint());
            }

            $user->setPoint($point);
        }

        /*formulaire pour retourner le total des point suivant une plage de date */


        if ($request->getMethod() == 'POST'){

            /*convetire la date from string to dateTime*/
            $debut = new \DateTime($request->get("debut"));
            $fin = new \DateTime($request->get("fin"));

            /*on met h:m:s a 0:0:0 et on ajout 1 jour pour avoir la date a minuit pour couvrire tout la journée*/
            $fin->setTime(0, 0, 0);
            $fin->format('Y-m-d');
            $fin->modify('+1 day');


            foreach($users as $user) {

                $ventes = $em->getRepository('TestBundle:Vente')->findVenteByUserDate($user, $debut, $fin);

                $point = 0;

                foreach ($ventes as $vente) {
                    $point = $point + ($vente->getQuantite() * $vente->getProduit()->getPoint());
                }

                $user->setPoint($point);

            }

            return $this->render('TestBundle:user:index.html.twig', array(
                'users' => $users,
            ));

        }



        return $this->render('TestBundle:user:index.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * Creates a new User entity.
     *
     */
    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm('TestBundle\Form\UserType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_show', array('id' => $user->getId()));
        }

        return $this->render('TestBundle:user:new.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a User entity.
     *
     */
    public function showAction(User $user)
    {
        $deleteForm = $this->createDeleteForm($user);

        return $this->render('TestBundle:user:show.html.twig', array(
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     */
    public function editAction(Request $request, User $user)
    {
        $deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm('TestBundle\Form\UserType', $user);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('user_edit', array('id' => $user->getId()));
        }

        return $this->render('TestBundle:user:edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a User entity.
     *
     */
    public function deleteAction(Request $request, User $user)
    {
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * Creates a form to delete a User entity.
     *
     * @param User $user The User entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
