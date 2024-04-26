<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\CustomerType;
use App\Repository\CaseStudyRepository;
use App\Repository\CustomerRepository;
use App\Service\ErrorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/kunden', name: 'app_customer_')]
class CustomerController extends AbstractController
{
    #[Route('/', name: 'browse', methods: ['GET'])]
    public function browse(CustomerRepository $customerRepository, CaseStudyRepository $caseStudyRepository): Response
    {
        $filter = $this->getUser() ? [] : ['active' => true];
        $items = $customerRepository->findBy($filter, ['name' => 'asc']);

        foreach ($items as $item) {
            $item->setNumberCaseStudies(count($caseStudyRepository->findBy(['customer' => $item])));
        }

        return $this->render('pages/customer/browse.html.twig', ['items' => $items]);
    }

    #[Route('/{id}', name: 'read', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function read(Customer $customer, CaseStudyRepository $caseStudyRepository): Response
    {
        if (!$this->getUser() && !$customer->isActive()) {
            return $this->redirectToRoute('app_customer_browse');
        }

        $caseStudies = $caseStudyRepository->findBy(['customer' => $customer], ['title' => 'asc']);
        return $this->render('pages/customer/read.html.twig', ['customer' => $customer, 'caseStudies' => $caseStudies]
        );
    }

    #[Route('/{id}/bearbeiten', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        ErrorService $errorService,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        Customer $customer,
    ): Response {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_customer_browse');
        }

        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->persist($customer);
                $entityManager->flush();
                $this->addFlash('message', 'Der Kunde wurde gespeichert.');

                return $this->redirectToRoute('app_customer_browse', [], Response::HTTP_SEE_OTHER);
            } else {
                $errors = $errorService->parseViolations($validator->validate($form));
                return $this->render('pages/customer/edit.html.twig', [
                    'form' => $form,
                    'errors' => $errors,
                    'customer' => $customer,
                ]);
            }
        }

        return $this->render('pages/customer/edit.html.twig', ['form' => $form, 'customer' => $customer]);
    }

    #[Route('/neu', name: 'add', methods: ['GET', 'POST'])]
    public function add(
        Request $request,
        ErrorService $errorService,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_customer_browse');
        }

        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $errors = $errorService->parseViolations($validator->validate($form));
                return $this->render('pages/customer/add.html.twig', [
                    'form' => $form,
                    'errors' => $errors,
                ]);
            }

            // Store the image later, wait for id generation first. The image is
            // placed in a directory named by the ID of the customer.
            $imageFile = $customer->getImageFile();
            $customer->setImageFile(null);

            $entityManager->persist($customer);
            $entityManager->flush();

            if ($imageFile !== null) {
                $customer->setImageFile($imageFile);
                $entityManager->persist($customer);
                $entityManager->flush();
            }

            $this->addFlash('message', 'Der Kunde wurde gespeichert.');

            return $this->redirectToRoute('app_customer_browse', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pages/customer/add.html.twig', ['form' => $form]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(
        Request $request,
        EntityManagerInterface $entityManager,
        Customer $customer,
        CaseStudyRepository $caseStudyRepository
    ): Response {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_customer_browse');
        }

        if ($this->isCsrfTokenValid('delete' . $customer->getId(), $request->getPayload()->get('_token'))) {
            if (count($caseStudyRepository->findBy(['customer' => $customer])) > 0) {
                $this->addFlash(
                    'message',
                    'Der Kunde kann nicht gelöscht werden, da es noch verknüpfte Case Studies gibt.'
                );

                return $this->redirectToRoute('app_customer_read', ['id' => $customer->getId()]);
            }

            $entityManager->remove($customer);
            $entityManager->flush();
            $this->addFlash('message', 'Der Kunde wurde gelöscht.');
        }

        return $this->redirectToRoute('app_customer_browse', [], Response::HTTP_SEE_OTHER);
    }
}