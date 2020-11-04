<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Service\Mailer;
use App\Service\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/register", name="app_register")
 */
class RegistrationController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->manager = $entityManager;
    }

    /**
     * @Route("", name="")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Mailer $mailer
     * @param TokenGenerator $tokenGenerator
     * @return Response
     * @throws \Exception
     */
    final public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        Mailer $mailer,
        TokenGenerator $tokenGenerator
    ): Response
    {
        $user = new User();
        $form = $this
            ->createForm(RegistrationFormType::class, $user)
            ->handleRequest($request)
        ;

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setConfirmationToken($tokenGenerator->generate());

            $this->manager->persist($user);
            $this->manager->flush();

            $email = $mailer->buildEmail(
                "SymfonyCorps | Confirmation de votre compte",
                $user->getEmail(),
                'emails/register.html.twig',
                ['user' => $user]
            );

            $mailer->send($email);

            $this->addFlash(
                'success',
                'Un email de confirmation vous a été envoyé.'
            );

            return $this->redirectToRoute('app_home');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/check/{id<\d+>}", name="_check")
     * @param User $user
     * @param Request $request
     */
    final public function verifyEmail(User $user, Request $request): Response
    {
        $token = $request->get('token');

        if(empty($token) || $user->getConfirmationToken() !== $token) {
            $this->addFlash(
                'error',
                'Ce token n\'est pas valide.'
            );

            return $this->redirectToRoute('app_login');
        }


        if($user->getCreatedAt() < new \DateTime(User::TOKEN_VALIDITY)){
            $this->addFlash(
                'error',
                'Votre lien d\'activation a expiré.'
            );

            return $this->redirectToRoute('app_login');
        }

        $user
            ->setConfirmationToken(null)
            ->setIsVerified(true);

        //$this->manager->persist($user);
        $this->manager->flush();

        $this->addFlash('success', 'Votre compte a été activé.');

        return $this->redirectToRoute('app_login');
    }
}
