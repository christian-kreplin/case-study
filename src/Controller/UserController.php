<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\ErrorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/redakteure', name: 'app_user_')]
class UserController extends AbstractController
{
    #[Route('/', name: 'browse', methods: ['GET'])]
    public function browse(UserRepository $userRepository): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_default_index');
        }

        $items = $userRepository->findBy([], ['email' => 'asc']);
        return $this->render('pages/user/browse.html.twig', ['items' => $items]);
    }

    #[Route('/{id}', name: 'read', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function read(User $user): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_default_index');
        }

        return $this->render('pages/user/read.html.twig', ['user' => $user]);
    }

    #[Route('/{id}/bearbeiten', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        ErrorService $errorService,
        ValidatorInterface $validator,
        EntityManagerInterface $entityManager,
        User $user,
    ): Response {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_default_index');
        }

        $form = $this->createForm(UserType::class, $user, ['isNew' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('message', 'Der Redakteur wurde gespeichert.');

                return $this->redirectToRoute('app_user_browse', [], Response::HTTP_SEE_OTHER);
            } else {
                $errors = $errorService->parseViolations($validator->validate($form));
                return $this->render('pages/user/edit.html.twig', [
                    'form' => $form,
                    'errors' => $errors,
                    'user' => $user,
                ]);
            }
        }

        return $this->render('pages/user/edit.html.twig', ['form' => $form, 'isNew' => false, 'user' => $user]);
    }

    #[Route('/neu', name: 'add', methods: ['GET', 'POST'])]
    public function add(
        Request $request,
        ErrorService $errorService,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_default_index');
        }

        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['isNew' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $errors = $errorService->parseViolations($validator->validate($form));
                return $this->render('pages/user/add.html.twig', ['form' => $form, 'errors' => $errors]);
            }

            $encodedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user
                ->setPassword($encodedPassword)
                ->setVerified(true);

            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('message', 'Der Redakteur wurde gespeichert.');

            return $this->redirectToRoute('app_user_browse', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pages/user/add.html.twig', ['form' => $form]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, User $user): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_default_index');
        }

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('message', 'Der Redakteur wurde gelöscht.');
        }

        return $this->redirectToRoute('app_user_browse', [], Response::HTTP_SEE_OTHER);
    }
}