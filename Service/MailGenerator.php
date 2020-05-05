<?php

namespace Stewie\UserBundle\Service;

use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

class MailGenerator extends AbstractController
{
    protected $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function invite($user){

        $email = (new Email())
            ->from(new Address($this->getParameter('stewie_user.from_email'), $this->getParameter('stewie_user.from_name')))
            ->to($user->getEmail())
            ->subject('Your Invitation')
            ->text($this->renderView(
                '@StewieUser/emails/invitation.txt.twig', array(
                'name' => $user->getFirstName().' '.$user->getLastName(),
                'token' => $user->getToken(),
                'inviter' => $this->getUser()->getUsername()
                )),
            )
            ->html($this->renderView(
                '@StewieUser/emails/invitation.html.twig', array(
                'name' => $user->getFirstName().' '.$user->getLastName(),
                'token' => $user->getToken(),
                'inviter' => $this->getUser()->getUsername()
                ))
            );

        $this->mailer->send($email);
    }

    public function register($user){

        $email = (new Email())
            ->from(new Address($this->getParameter('stewie_user.from_email'), $this->getParameter('stewie_user.from_name')))
            ->to($user->getEmail())
            ->subject('Your Registration')
            ->text($this->renderView(
                '@StewieUser/emails/registration.txt.twig', array(
                'name' => $user->getFirstName().' '.$user->getLastName(),
                'token' => $user->getToken()
                )),
            )
            ->html($this->renderView(
                '@StewieUser/emails/registration.html.twig', array(
                'name' => $user->getFirstName().' '.$user->getLastName(),
                'token' => $user->getToken()
                ))
            );

        $this->mailer->send($email);
    }
}
