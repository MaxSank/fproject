<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;
use DateTimeZone;

class LoginSubscriber implements EventSubscriberInterface
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function onLoginSuccessEvent(LoginSuccessEvent $event)
    {
        $user = $event->getUser();

        $currentTime = new DateTime('now', new DateTimeZone('Europe/Minsk'));
        $user->setLastLoginDate($currentTime);

        $this->em->flush();
    }

    public static function getSubscribedEvents()
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccessEvent',
        ];
    }
}
