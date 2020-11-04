<?php


namespace App\Controller;


use App\Entity\User;
use App\Event\ResetPasswordEvent;
use App\Form\ResetPasswordFormType;
use App\Repository\UserRepository;
use App\Service\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/reset-password", name="app_reset_password")
 */
final class ResetPasswordController extends AbstractController
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
     * @param TokenGenerator $tokenGenerator
     * @param EventDispatcherInterface $eventDispatcher
     * @return Response
     * @throws \Exception
     */
    public function reset(Request $request, TokenGenerator $tokenGenerator, EventDispatcherInterface $eventDispatcher): Response
    {
        if(!$email = $request->get('email')) return $this->render('security/reset-password.html.twig');

        $user = $this->manager->getRepository(User::class)->findOneBy(['email' => $email]);

        if(!is_null($user)) {
            $user->setPasswordResetToken($tokenGenerator->generate());

            $this->manager->flush();
            $eventDispatcher->dispatch(new ResetPasswordEvent($user));
        }

        $this->addFlash('success', 'Un email vous a été envoyé.');


        return $this->render('security/reset-password.html.twig');
    }

    /**
     * @Route("/check/{id<\d+>}", name="_check")
     * @param User $user
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return RedirectResponse|Response
     */
    public function check(User $user, Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $token = $request->get('token');

        if(empty($token) || $user->getPasswordResetToken() !== $token)
        {
            return $this->redirectToRoute('app_login');
        }


        $form = $this->createForm(ResetPasswordFormType::class)->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user
                ->setPassword($passwordEncoder->encodePassword($user, $form->get('plainPassword')->getData()))
                ->setPasswordResetToken(null)
            ;

            $this->manager->flush();

            $this->addFlash('success', 'Vous venez de réinitialiser votre mot de passe avec succès.');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset-password-validate.html.twig',
        [
            'form' => $form->createView()
        ]);

    }

}