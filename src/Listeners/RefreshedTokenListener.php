<?php

namespace App\Listeners;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\EntityManager;


class RefreshedTokenListener implements EventSubscriberInterface
{

    private $ttl;

    private $cookieSecure = false;
    /* @var Doctrine\ORM\EntityManager $em */
    protected $em;
    public function __construct($ttl, EntityManager $em)
    {
        $this->ttl = $ttl;
        $this->em = $em;
    }

    public function setRefreshToken(AuthenticationSuccessEvent $event)
    {
        $refreshToken = $event->getData()['refresh_token'];
        $response = $event->getResponse();

        if ($refreshToken) {
            $response->headers->setCookie(new Cookie('REFRESH_TOKEN', $refreshToken, (new \DateTime())
                ->add(new \DateInterval('PT' . $this->ttl . 'S')), '/', null, $this->cookieSecure));
        }
        $rsm = new ResultSetMapping();
        // build rsm here
        $user = $event->getUser();
        $query = $this->em->createNativeQuery('DELETE FROM refresh_tokens
        WHERE username = ? AND refresh_token <> ?', $rsm);
        $query->setParameter(1, $user->getEmail());
        $query->setParameter(2, $refreshToken);
        $query->getResult();
    }

    public static function getSubscribedEvents()
    {
        return [
            'lexik_jwt_authentication.on_authentication_success' => [
                ['setRefreshToken']
            ]
        ];
    }
}
