<?php

namespace Stewie\UserBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SetFromListener implements EventSubscriberInterface
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return [
            MessageEvent::class => 'onMessage',
        ];
    }
    public function onMessage(MessageEvent $event)
    {
        $email = $event->getMessage();
        dump($email->getSubject());
        if (!$email instanceof Email || !$this->container->getParameter('stewie_user.set_from')) {
            return;
        }
        $email->from(new Address($this->container->getParameter('stewie_user.from_email'), $this->container->getParameter('stewie_user.from_name')));
    }
}
