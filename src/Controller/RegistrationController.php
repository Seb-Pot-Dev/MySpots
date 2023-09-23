<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        // Initialisation d'un nouvel utilisateur
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Sécurité : Hachage du mot de passe avant stockage
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // Pour définir la date/heure actuelle comme date d'inscription + date de dernière activité
            $now = new \DateTime();
            $user->setRegistrationDate($now);
            $user->setLastActivityAt($now);

            // Tente d'enregistrer l'utilisateur
            try{
            $entityManager->persist($user);
            $entityManager->flush();

            // Sécurité : Vérification de l'e-mail par l'envoi d'un lien de confirmation
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('skatespot@admin.com', 'Seb (admin de Skate Spot)'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // Gestion des erreurs
        }catch(\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e){
            $this->addFlash('error', 'The provided pseudo or email is already in use.');
        return $this->render('registration/register.html.twig', [
            'registrationFormType' => $form->createView(),
        ]);
        }
        // Authentification et redirection de l'utilisateur après inscription réussie

        return $userAuthenticator->authenticateUser(
            $user,
            $authenticator,
            $request
        );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        // Sécurité : S'assurer que l'utilisateur est entièrement authentifié avant la vérification
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // Valider le lien de confirmation d'e-mail et mettre à jour le statut de vérification de l'utilisateur
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
        // Gérer les erreurs de manière sécurisée sans divulguer de détails spécifiques
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        // Message de réussite pour l'utilisateur
        $this->addFlash('success', 'Votre adresse a été vérifiée correctement. Vous pouvez désormais vous connecter');

        return $this->redirectToRoute('app_login');
    }
}
