<?php

namespace TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use TestBundle\Entity\Vente;
use TestBundle\Form\VenteType;

class DefaultController extends Controller {

    public function indexAction(Request $request)
    {
        /*
        Action appeller sur la page principal
        Affche la page qui permet de creer une vente
        et calculer le nombre de point de chque user
        */

        $em = $this->getDoctrine()->getManager();

        $produits = $em->getRepository('TestBundle:Produit')->findAll();
        $users = $em->getRepository('TestBundle:User')->findAll();
        $ventes = $em->getRepository('TestBundle:Vente')->findAll();

        /*la somme totale des ventes */
        $total = 0;

        foreach($ventes as $vente){
            $total = $total + ($vente->getProduit()->getPrix() * $vente->getQuantite());
        }

        return $this->render('TestBundle:Default:index.html.twig', array(
            'produits' => $produits,
            'users' => $users,
            'ventes' => $ventes,
            'total' => $total,
        ));
    }

    public function venteAction(Request $request)
    {
        /*
        Action appeller a chaque nouvelle vente
        Doit creer une vente dans la BDD
        */

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

    public function calculPointAction(Request $request)
    {
        /*
        Action appeller a chaque calcul de point
        Doit calculer le nombre de point pour chaque user
        et l'enregistre (ne doit pas effacer le precent calcul)
        */
    }

    public function affichagePointAction(Request $request)
    {
        /*
        Action appeller pour l'affichage des points
        Doit afficher un tableau avec la liste des user
        et leur point du dernier calcul.
        */


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

}

