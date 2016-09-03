<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Address;
use AppBundle\Form\AddressType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/address")
 */
class AddressController extends Controller
{
    /**
     * @Route("/new/{user_id}")
     * @Template
     */
    public function newAction(Request $request, $user_id) {

        $address=new Address();
        $contact=$this->getDoctrine()->getRepository('AppBundle:Contact')->find($user_id);

        if(!$contact) {
            throw $this->createNotFoundException('Contact not found');
        }
        $address->setContact($contact);

        $form=$this->createForm(
            new AddressType(),
            $address
        );

        $form->add("submit", "submit");
        $form->handleRequest($request);
        if($form->isValid()) {
            $em=$this->getDoctrine()->getManager();
            $em->persist($address);
            $em->flush();
            return $this->redirectToRoute('app_contact_show',
                ['id'=>$address->getContact()->getId()]);
        }
        return ["form"=>$form->createView()];
    }

    /**
     * @Route("/modify/{id}")
     * @Template("AppBundle:Address:new.html.twig")
     */
    public function modifyAction(Request $request, $id) {
        $address=$this->getDoctrine()->getRepository("AppBundle:Address")->find($id);

        $form=$this->createForm(
            new AddressType(),
            $address
        );

        $form->add("submit", "submit");
        $form->handleRequest($request);
        if($form->isValid()) {
            $em=$this->getDoctrine()->getManager();

            $em->flush();
            return $this->redirectToRoute('app_contact_show',
                ['id'=>$address->getContact()->getId()]);
        }
        return ["form"=>$form->createView()];
    }

    /**
     * @Route("/delete/{id}")
     */
    public function deleteAction($id) {
        $address=$this->getDoctrine()->getRepository("AppBundle:Address")->find($id);
        if(!$address) {
            throw $this->createNotFoundException("Address not found");
        }

        $em=$this->getDoctrine()->getManager();
        $em->remove($address);
        $em->flush();
        return $this->redirectToRoute("app_contact_show", ['id'=>$address->getContact()->getId()]);
    }
}
