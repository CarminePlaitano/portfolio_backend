<?php

namespace App\Controller\Admin;

use App\Domain\Contact\Command\CreateContactCommand;
use App\Domain\Contact\Command\DeleteContactCommand;
use App\Domain\Contact\Command\UpdateContactCommand;
use App\Entity\Contact;
use App\Form\ContactFormType;
use App\Repository\ContactRepository;
use App\Service\ContactTableService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/contact-me')]
#[IsGranted('ROLE_USER')]
class ContactMeController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        private readonly ContactTableService $contactTableService,
        private readonly ContactRepository $contactRepository,
        #[Autowire(service: 'command.bus')]
        MessageBusInterface $messageBus,
    ) {
        $this->messageBus = $messageBus;
    }

    #[Route('/', name: 'app_contact_me_index', methods: ['GET', 'POST'])]
    public function index(Request $request): Response
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $itemsPerPage = (int) $request->query->get('perPage', 15);

        $search = $request->query->get('search');

        $result = $this->contactTableService->getAllForTableBySearch($search, $page, $itemsPerPage);

        return $this->render('pages/contact-me/index.html.twig', [
            'contacts' => $result['data'],
            'search' => $search,
            'totalContacts' => $result['total'],
            'currentPage' => $page,
            'itemsPerPage' => $itemsPerPage,
            'totalPages' => (int) ceil($result['total'] / $itemsPerPage),
        ]);
    }

    #[Route('/new', name: 'app_contact_me_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactFormType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new CreateContactCommand(
                $contact->getType(),
                $contact->getValue(),
                $contact->getLabel()
            );

            $this->handle($command);
            $this->addFlash('success', 'Contact created successfully.');

            return $this->redirectToRoute('app_contact_me_index');
        }

        return $this->render('pages/contact-me/_partials/modal-form.html.twig', [
            'form' => $form->createView(),
            'contact' => $contact,
        ]);
    }

    #[Route('/edit/{id}', name: 'app_contact_me_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id): Response
    {
        $contact = $this->contactRepository->find($id);
        if (!$contact) {
            throw $this->createNotFoundException('No contact found for id '.$id);
        }

        $form = $this->createForm(ContactFormType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $command = new UpdateContactCommand(
                $contact->getId(),
                $contact->getType(),
                $contact->getValue(),
                $contact->getLabel()
            );

            $this->handle($command);
            $this->addFlash('success', 'Contact updated successfully.');

            return $this->redirectToRoute('app_contact_me_index');
        }

        return $this->render('pages/contact-me/_partials/modal-form.html.twig', [
            'form' => $form->createView(),
            'contact' => $contact,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_contact_me_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Contact $contact): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contact->getId(), $request->request->get('_token'))) {
            $command = new DeleteContactCommand($contact->getId());
            $this->handle($command);
            $this->addFlash('success', 'Contact deleted successfully.');
        }

        return $this->redirectToRoute('app_contact_me_index');
    }
}
