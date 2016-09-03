<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Contact;
use AppBundle\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/contact")
 */
class ContactController extends Controller
{
    /**
     * @Route("/new")
     * @Template
     */
    public function newAction() {
        $contact=new Contact();
        $user=$this->getUser();
        if(!$user) {
            $this->redirectToRoute('form_login');
        }
        $form=$this->createForm(
            new ContactType(),
            $contact,
            [
                'action' => $this->generateUrl('app_contact_create')
            ]
        );
        $form->add('submit', 'submit');

        return ["form"=>$form->createView()];
    }

    /**
     * @Route("/create")
     * @Template
     */
    public function createAction(Request $request) {
        $contact=new Contact();
        $user=$this->getUser();

        $form=$this->createForm(
            new ContactType(),
            $contact,
            [
                'action' => $this->generateUrl('app_contact_create')
            ]
        );
        $form->add('submit', 'submit');
        $form->handleRequest($request);
        if($form->isValid()) {
            $contact->setUser($user);
            $em=$this->getDoctrine()->getManager();
            $em->persist($contact);
            $em->flush();
            return $this->redirectToRoute('app_contact_show',
                ['id'=>$contact->getId()]);
        }
        return ["form"=>$form->createView()];
    }

    /**
     * @Route("/show/{id}")
     * @Template
     */
    public function showAction($id) {
        $contact = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Contact')
            ->find($id);
        $user = $this->getUser();

        if($contact->getUser()!==$user) {
            throw $this->createNotFoundException('Contact not found');
        }

        if(!$contact) {
            throw $this->createNotFoundException('Contact not found');
        }

        return ["contact"=>$contact];
    }

    /**
     * @Route("/showAll")
     * @Template
     */
    public function showAllAction() {
        $user = $this->getUser();

        $contacts = $this->getDoctrine()->getRepository('AppBundle:Contact')->findBy(['user'=>$user]);

        return ["contacts"=>$contacts];
    }

    /**
     * @Route("/modify/{id}")
     * @Template("AppBundle:Contact:create.html.twig")
     */
    public function modifyAction(Request $request, $id) {
        $contact=$this->getDoctrine()->getRepository("AppBundle:Contact")->find($id);
        if(!$contact) {
            throw $this->createNotFoundException("Contact not found");
        }
        $user = $this->getUser();
        if($contact->getUser()!==$user) {
            throw $this->createNotFoundException('Contact not found');
        }

        $form=$this->createForm(
            new ContactType(),
            $contact
        );
        $form->add('submit', 'submit');
        $form->handleRequest($request);
        if($form->isValid()) {
            $em=$this->getDoctrine()->getManager();

            $em->flush();
            return $this->redirectToRoute('app_contact_show',
                ['id'=>$contact->getId()]);
        }
        return ["form"=>$form->createView()];
    }
    /**
     * @Route("/delete/{id}")
     */
    public function deleteAction($id) {
        $contact=$this->getDoctrine()->getRepository("AppBundle:Contact")->find($id);
        if(!$contact) {
            throw $this->createNotFoundException("Contact not found");
        }

        $user = $this->getUser();
        if($contact->getUser()!==$user) {
            throw $this->createNotFoundException('Contact not found');
        }

        $em=$this->getDoctrine()->getManager();
        $em->remove($contact);
        $em->flush();
        return $this->redirectToRoute("app_contact_showall");
    }

    /**
     * @Route("/addToGroup/{id}")
     * @Template("AppBundle:Contact:new.html.twig")
     */
    public function addToGroupAction(Request $request, $id) {
        $contact = $this->getDoctrine()->getRepository("AppBundle:Contact")->find($id);

        $user = $this->getUser();
        if($contact->getUser()!==$user) {
            throw $this->createNotFoundException('Contact not found');
        }

        $form = $this->createFormBuilder($contact)
            ->add('groups', 'entity', array(
                'class'=>'AppBundle\Entity\ContactGroup',
                'choice_label'=>'name',
                'multiple'=>true,
                'expanded'=>true
            ))
            ->add('submit', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if($form->isValid()) {
            foreach ($contact->getGroups() as $group) {
                if (!$group->getContacts()->contains($contact)) {
                    $group->addContact($contact);
                }
            }

            $em=$this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('app_contact_show', ['id'=>$contact->getId()]);
        }

        return ['form'=>$form->createView()];
    }
}
