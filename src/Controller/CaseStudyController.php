<?php

namespace App\Controller;

use App\Entity\CaseStudy;
use App\Form\CaseStudyType;
use App\Repository\CaseStudyRepository;
use App\Service\ErrorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/case-studies', name: 'app_case_study_')]
class CaseStudyController extends AbstractController
{
    #[Route('/', name: 'browse', methods: ['GET'])]
    public function browse(CaseStudyRepository $caseStudyRepository): Response
    {
        $items = $caseStudyRepository->findByAuthState($this->getUser() !== null);
        
        return $this->render('pages/case_study/browse.html.twig', ['items' => $items]);
    }

    #[Route('/{id}', name: 'read', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function read(CaseStudy $customer): Response
    {
        return $this->render('pages/case_study/read.html.twig', ['case_study' => $customer]);
    }

    #[Route('/{id}/bearbeiten', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        ErrorService $errorService,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        CaseStudy $caseStudy,
    ): Response {
        $form = $this->createForm(CaseStudyType::class, $caseStudy);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->persist($caseStudy);
                $entityManager->flush();
                $this->addFlash('message', 'Die Case Study wurde gespeichert.');

                return $this->redirectToRoute('app_case_study_browse', [], Response::HTTP_SEE_OTHER);
            } else {
                $errors = $errorService->parseViolations($validator->validate($form));
                return $this->render('pages/case_study/edit.html.twig', [
                    'form' => $form,
                    'errors' => $errors,
                    'case_study' => $caseStudy,
                ]);
            }
        }

        return $this->render('pages/case_study/edit.html.twig', ['form' => $form, 'case_study' => $caseStudy]);
    }

    #[Route('/neu', name: 'add', methods: ['GET', 'POST'])]
    public function add(
        Request $request,
        ErrorService $errorService,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager
    ): Response {
        $customer = new CaseStudy();
        $form = $this->createForm(CaseStudyType::class, $customer);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $errors = $errorService->parseViolations($validator->validate($form));
                return $this->render('pages/case_study/add.html.twig', [
                    'form' => $form,
                    'errors' => $errors,
                ]);
            }

            $entityManager->persist($customer);
            $entityManager->flush();
            $this->addFlash('message', 'Die Case Study wurde gespeichert.');

            return $this->redirectToRoute('app_case_study_browse', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pages/case_study/add.html.twig', ['form' => $form]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, CaseStudy $customer): Response
    {
        if ($this->isCsrfTokenValid('delete' . $customer->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($customer);
            $entityManager->flush();
            $this->addFlash('message', 'Die Case Study wurde gelöscht.');
        }

        return $this->redirectToRoute('app_case_study_browse', [], Response::HTTP_SEE_OTHER);
    }
}