<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Phone;
use AppBundle\Form\PhoneType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/phone")
 */
class PhoneController extends Controller
{
    /**
     * @Route("/new/{contact_id}")
     * @Template("AppBundle:Contact:new.html.twig")
     */
    public function newAction(Request $request, $contact_id) {
        $phone=new Phone();
        $contact=$this->getDoctrine()->getRepository("AppBundle:Contact")->find($contact_id);
        $phone->setContact($contact);
        $form=$this->createForm(new PhoneType(),
            $phone);
        $form->add("submit", "submit");
        $form->handleRequest($request);
        if($form->isValid()) {
            $em=$this->getDoctrine()->getManager();
            $em->persist($phone);
            $em->flush();
            return $this->redirectToRoute("app_contact_show", ["id"=>$phone->getContact()->getId()]);
        }
    return ['form'=>$form->createView()];
    }

    /**
     * @Route("/modify/{id}")
     * @Template("AppBundle:Contact:new.html.twig")
     */
    public function modifyAction(Request $request, $id) {
        $phone=$this->getDoctrine()->getRepository('AppBundle:Phone')->find($id);
        if(!$phone) {
            throw $this->createNotFoundException('Phone not found');
        }
        $form=$this->createForm(
            new PhoneType(),
            $phone
        );
        $form->add('submit', 'submit');
        $form->handleRequest($request);
        if($form->isValid()) {
            $em=$this->getDoctrine()->getManager();

            $em->flush();
            return $this->redirectToRoute('app_contact_show', ['id'=>$phone->getContact()->getId()]);
        }
        return ['form'=>$form->createView()];
    }

    /**
     * @Route("/delete/{id}")
     */
    public function deleteAction($id) {
        $phone=$this->getDoctrine()->getRepository('AppBundle:Phone')->find($id);
        if(!$phone) {
            throw $this->createNotFoundException('Phone not found');
        }
        $em=$this->getDoctrine()->getManager();
        $em->remove($phone);
        $em->flush();
        return $this->redirectToRoute("app_contact_show", ['id'=>$phone->getContact()->getId()]);
    }
}
