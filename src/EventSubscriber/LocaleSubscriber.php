<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class LocaleSubscriber implements EventSubscriberInterface
{
    private $em;
    private $tokenStorage;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        EntityManagerInterface $em
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $locale = $request->attributes->get('_locale');

        if (!$token = $this->tokenStorage->getToken()) {
            return ;
        }

        if (!$user = $token->getUser()) {
            return ;
        }

        if (!$default_language = $user->getLanguage()) {
            return ;
        }

        if ($locale != $default_language and in_array($locale, [User::ENG, User::RUS])) {
            $user->setLanguage($locale);

            $this->em->flush();
        }

    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }
}
