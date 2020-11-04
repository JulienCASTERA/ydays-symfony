<?php

namespace App\EventSubscriber;

use App\Event\ResetPasswordEvent;
use App\Event\UserCreatedEvent;
use App\Service\Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AuthSubscriber implements EventSubscriberInterface
{
    private Mailer $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            UserCreatedEvent::class => 'onRegister',
            ResetPasswordEvent::class => 'onResetPassword'
        ];
    }

    public function onRegister(UserCreatedEvent $event): void
    {
        $user = $event->getUser();

        $email = $this->mailer
            ->buildEmail(
                'SymfoCorps | Confirmation du compte',
                $user->getEmail(),
                'emails/register.html.twig',
                ['user' => $user]
            );

        $this->mailer->send($email);
    }

    public function onResetPassword(ResetPasswordEvent $event): void
    {
        $user = $event->getUser();

        $email = $this->mailer
            ->buildEmail(
                'SymfoCorps | Réinitialisation du mot de passe',
                $user->getEmail(),
                'emails/reset-password.html.twig',
                ['user' => $user]
            );

        $this->mailer->send($email);
    }
}