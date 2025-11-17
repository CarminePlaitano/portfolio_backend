<?php

namespace App\Controller\Admin;

use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ContactMeController extends AbstractController
{
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/contact-me', name: 'app_contact_me', methods: ['GET'])]
    public function index(ContactRepository $contactRepository): Response
    {
        $contacts = $contactRepository->findAll();

        dump($contacts);

        return $this->render('pages/contact-me/index.html.twig', [
            'contacts' => $contacts
        ]);
    }
} 
