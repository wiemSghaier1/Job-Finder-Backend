<?php

namespace App;

// use App\Entity\User;
use App\Entity\JobSeeker;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Stripe\Charge;
use Stripe\Error\Base;
use Stripe\Stripe;

class StripeClient
{
    private $secret;
    private $em;
    private $logger;

    public function __construct($secretKey, EntityManagerInterface $em, LoggerInterface $logger)
    {
        \Stripe\Stripe::setApiKey($secretKey);
        $this->em = $em;
        $this->logger = $logger;
        $this->secret = $secretKey;
    }
    public function createClient()
    {
        return  new \Stripe\StripeClient($this->secret);
    }

    public function createSession($body, $user)
    {
        $this->logger->info($user->getId());
        return  \Stripe\Checkout\Session::create([
            'success_url' => 'http://localhost:3000/subscription/success?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => 'http://localhost:3000',
            'client_reference_id' => $user->getId(),
            'payment_method_types' => ['card'],
            'mode' => 'subscription',
            'line_items' => [[
                'price' => $body['priceId'],
                // For metered billing, do not pass quantity
                'quantity' => 1,
            ]]

        ]);
    }
    public function mapStripeCustomer($userId, $customerId)
    {
        $this->logger->info("updating!! " . $userId . " " . $customerId);
        $repository = $this->em->getRepository(JobSeeker::class);
        $user = $repository->find($userId);
        $user->setStripeId($customerId);
        $this->em->flush();
    }
    public function mapStripeSubsciption($subscriptionId, $customerId, $endsAt)
    {
        $this->logger->info("updating!! " . $subscriptionId . " " . $customerId . " " . $endsAt);
        $repository = $this->em->getRepository(JobSeeker::class);
        $user = $repository->findOneBy(
            ['stripeId' => $customerId]
        );
        $user->setSubsciptionId($subscriptionId);
        $subsciptionEndsAt = date_create();
        date_timestamp_set($subsciptionEndsAt, $endsAt);
        $user->setSubscriptionEndAt($subsciptionEndsAt);
        $this->em->flush();
    }
}
