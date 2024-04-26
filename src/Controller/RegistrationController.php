<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Security\UserAuthenticator;
use App\Service\ErrorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier)
    {
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        ErrorService $errorService,
        ParameterBagInterface $parameterBag
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $entityManager->persist($user);
                $entityManager->flush();

                // generate a signed url and email it to the user
                $this->emailVerifier->sendEmailConfirmation(
                    'app_verify_email',
                    $user,
                    (new TemplatedEmail())
                        ->from(
                            new Address(
                                $parameterBag->get('mailer_sender_address'),
                                $parameterBag->get('mailer_sender_name')
                            )
                        )
                        ->to($user->getEmail())
                        ->subject('Bitte bestätige deine E-Mail')
                        ->htmlTemplate('emails/registration/confirmation.html.twig')
                );

                // do anything else you need here, like send an email
                return $this->render('pages/registration/received.html.twig', ['email' => $user->getEmail()]);
            } else {
                $errors = $errorService->parseViolations($validator->validate($form));

                return $this->render('pages/registration/register.html.twig', [
                    'form' => $form,
                    'errors' => $errors,
                ]);
            }
        }

        return $this->render('pages/registration/register.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/register/resend', name: 'app_register_resend')]
    public function resendVerification(
        Request $request,
        UserRepository $userRepository,
        ParameterBagInterface $parameterBag
    ): Response {
        $email = $request->get('email');
        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            $this->addFlash('message', 'Der Redakteur wurde nicht gefunden.');
            return $this->redirectToRoute('app_default_index');
        }

        if ($user->isVerified()) {
            $this->addFlash('message', 'Der Redakteur ist bereits verifiziert. Bitte logge dich ein.');
            return $this->redirectToRoute('app_login');
        }

        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(
                    new Address(
                        $parameterBag->get('mailer_sender_address'),
                        $parameterBag->get('mailer_sender_name')
                    )
                )
                ->to($user->getEmail())
                ->subject('Bitte bestätige deine E-Mail')
                ->htmlTemplate('emails/registration/confirmation.html.twig')
        );

        return $this->render('pages/registration/received.html.twig', ['email' => $email]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(
        Request $request,
        TranslatorInterface $translator,
        UserRepository $userRepository,
        Security $security,
    ): Response {
        $id = $request->query->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        $this->addFlash(
            'message',
            'Die E-Mail-Adresse wurde erfolgreich verifiziert. Du wurdest automatisch eingeloggt.'
        );

        return $security->login($user, UserAuthenticator::class, 'main');
    }
}
