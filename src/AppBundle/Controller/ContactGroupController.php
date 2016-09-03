<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ContactGroup;
use AppBundle\Form\ContactGroupType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/group")
 */
class ContactGroupController extends Controller
{
    /**
     * @Route("/new")
     * @Template("AppBundle:Contact:new.html.twig")
     */
    public function newAction(Request $request) {
        $group=new ContactGroup();
        $form=$this->createForm(
            new ContactGroupType(),
            $group
        );
        $form->add('submit', 'submit');
        $form->handleRequest($request);
        if($form->isValid()) {
            $em=$this->getDoctrine()->getManager();
            $em->persist($group);
            $em->flush();
            return $this->redirectToRoute('app_contactgroup_addcontacttogroup', ['id'=>$group->getId()]);
        }
        return ["form"=>$form->createView()];
    }

    /**
     * @Route("/addContact/{id}")
     * @Template
     */
    public function addContactToGroupAction(Request $request, $id) {
        $group=$this->getDoctrine()->getRepository('AppBundle:ContactGroup')->find($id);
        $form=$this->createFormBuilder($group)
            ->add('contacts', 'entity', array(
               'class'=>'AppBundle\Entity\Contact',
                'choice_label'=>'surname',
                'multiple'=>true,
                'expanded'=>true
            ))
            ->add('submit', 'submit')
            ->getForm();
        $form->handleRequest($request);
        if($form->isValid()) {
            $group=$form->getData();
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute('app_contactgroup_show', ['id'=>$group->getId()]);
        }
        return ['form'=>$form->createView()];

    }

    /**
     * @Route("/show/{id}")
     * @Template
     */
    public function showAction($id) {
        $group=$this->getDoctrine()->getRepository("AppBundle:ContactGroup")->find($id);
        if(!$group) {
            throw $this->createNotFoundException('Group not found');
        }

        return ['group'=>$group];
    }



}
