<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\Type\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ContactController extends AbstractController
{
    /**
     * @Route("/",name="app_contact")
     */
    public function new(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $errors = $validator->validate($contact);

            if (count($errors) > 0) {
                return new Response((string) $errors, 400);
            }
            $contact = $form->getData();
            $entityManager->persist($contact);
            $entityManager->flush();

            return $this->redirectToRoute('app_contact_success');
        }
        return $this->render('contact/contact.html.twig',['form'=>$form->createView()]);
    }

    /**
     * @Route("contact_success",name="app_contact_success")
     */
    public function success(EntityManagerInterface $entityManager)
    {
        $contacts = $entityManager
            ->getRepository(Contact::class)
            ->findAll();
        var_dump($contacts);

        return $this->render('contact/contact_success.html.twig',['success'=>"Köszönjük szépen a kérdésedet. Válaszunkkal hamarosan keresünk a megadott e-mail címen."]);
    }
}