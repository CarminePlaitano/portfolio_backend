<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use App\Service\ContactTableService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/contact-me')]
#[IsGranted('ROLE_ADMIN')]
class ContactMeController extends AbstractController
{
    public function __construct(
        private readonly ContactTableService $contactTableService,
        private readonly EntityManagerInterface $entityManager,
        private readonly ContactRepository $contactRepository,
    ) {
    }

    #[Route('/', name: 'app_contact_me_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $itemsPerPage = (int) $request->query->get('perPage', 15);

        $filters = [
            'type' => $request->query->get('type'),
            'contact_by' => $request->query->get('contact_by'),
            'search' => $request->query->get('search'),
        ];

        $result = $this->contactTableService->list($filters, $page, $itemsPerPage);

        return $this->render('pages/contact-me/index.html.twig', [
            'contacts' => $result['data'],
            'totalContacts' => $result['total'],
            'currentPage' => $page,
            'itemsPerPage' => $itemsPerPage,
            'totalPages' => (int) ceil($result['total'] / $itemsPerPage),
            'distinctContactByValues' => $this->contactTableService->getDistinctContactBy(),
        ]);
    }

    #[Route('/list', name: 'app_contact_me_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = (int) $request->query->get('perPage', 15);

        $filters = [
            'type' => $request->query->get('type'),
            'contact_by' => $request->query->get('contact_by'),
            'search' => $request->query->get('search'),
        ];

        $result = $this->contactTableService->list($filters, $page, $perPage);

        $contactsAsArray = array_map(function ($contactEntity) {
            return [
                'id' => $contactEntity->getId(),
                'type' => $contactEntity->getType(),
                'value' => $contactEntity->getValue(),
                'contactBy' => $contactEntity->getContactBy(),
                'label' => $contactEntity->getLabel(),
            ];
        }, $result['data']);

        return $this->json([
            'data' => $contactsAsArray,
            'total' => $result['total'],
            'page' => $page,
            'perPage' => $perPage,
        ]);
    }

    #[Route('/new', name: 'app_contact_me_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($contact);
            $this->entityManager->flush();

            $this->addFlash('success', 'Contact created successfully.');

            return $this->redirectToRoute('app_contact_me_index');
        }

        return $this->render('pages/contact-me/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'app_contact_me_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        $contact = $this->contactRepository->find($id);
        if (!$contact) {
            throw $this->createNotFoundException('No contact found for id '.$id);
        }

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Contact updated successfully.');

            return $this->redirectToRoute('app_contact_me_index');
        }

        return $this->render('pages/contact-me/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'app_contact_me_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Contact $contact): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contact->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($contact);
            $this->entityManager->flush();
            $this->addFlash('success', 'Contact deleted successfully.');
        }

        return $this->redirectToRoute('app_contact_me_index');
    }
}
