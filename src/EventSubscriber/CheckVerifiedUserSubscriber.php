<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;

class CheckVerifiedUserSubscriber implements EventSubscriberInterface
{
    public function onCheckPassport(CheckPassportEvent $event)
    {
        $passport = $event->getPassport();
        /** @var User $user */
        $user = $passport->getUser();

        if (!$user->isVerified()) {
            throw new CustomUserMessageAuthenticationException(
                'Bitte aktiviere deinen Account um dich einzuloggen. Bei Problemen, wende dich gern an unseren Support.',
                [$user->getEmail()]
            );
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CheckPassportEvent::class => ['onCheckPassport', -10],
        ];
    }
}