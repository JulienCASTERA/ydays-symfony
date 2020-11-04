<?php


namespace App\Service;


use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class Mailer
{
    private string $from;
    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    public function __construct(string $from, MailerInterface $mailer)
    {
        $this->from = $from;
        $this->mailer = $mailer;
    }

    final public function buildEmail(string $subject, string $to, string $htmlTemplate, array $data = []): TemplatedEmail
    {
        return (new TemplatedEmail())
            ->from($this->from)
            ->to($to)
            ->htmlTemplate($htmlTemplate)
            ->subject($subject)
            ->context($data)
        ;
    }

    final public function send(TemplatedEmail $email): void
    {
        $this->mailer->send($email);
    }

}