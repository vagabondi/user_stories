<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Email;
use AppBundle\Form\EmailType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/email")
 */
class EmailController extends Controller
{
    /**
     * @Route("/new/{contact_id}")
     * @Template("AppBundle:Contact:new.html.twig")
     */
    public function newAction(Request $request, $contact_id) {
        $email=new Email();
        $contact=$this->getDoctrine()->getRepository('AppBundle:Contact')->find($contact_id);
        $email->setContact($contact);
        $form=$this->createForm(
            new EmailType(),
            $email);
        $form->add('submit', 'submit');
        $form->handleRequest($request);
        if($form->isValid()) {
            $em=$this->getDoctrine()->getManager();
            $em->persist($email);
            $em->flush();
            return $this->redirectToRoute('app_contact_show', ['id'=>$email->getContact()->getId()]);
        }
        return ['form'=>$form->createView()];
    }

    /**
     * @Route("/modify/{id}")
     * @Template("AppBundle:Address:new.html.twig")
     */
    public function modifyAction(Request $request, $id) {
        $email=$this->getDoctrine()->getRepository("AppBundle:Email")->find($id);

        $form=$this->createForm(
            new EmailType(),
            $email
        );

        $form->add("submit", "submit");
        $form->handleRequest($request);
        if($form->isValid()) {
            $em=$this->getDoctrine()->getManager();

            $em->flush();
            return $this->redirectToRoute('app_contact_show',
                ['id'=>$email->getContact()->getId()]);
        }
        return ["form"=>$form->createView()];
    }

    /**
     * @Route("/delete/{id}")
     */
    public function deleteAction($id) {
        $email=$this->getDoctrine()->getRepository("AppBundle:Email")->find($id);
        if(!$email) {
            throw $this->createNotFoundException("Email not found");
        }

        $em=$this->getDoctrine()->getManager();
        $em->remove($email);
        $em->flush();
        return $this->redirectToRoute("app_contact_show", ['id'=>$email->getContact()->getId()]);
    }
}
