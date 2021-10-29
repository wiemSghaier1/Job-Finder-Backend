<?php

namespace App\Controller;

use App\StripeClient;
use Symfony\Component\HttpKernel\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Psr\Log\LoggerInterface;
use TeamTNT\Stripe\WebhookTester;
use Stripe\Stripe;

/**
 * @Route("api", name="api_")
 */
class IndexController extends AbstractFOSRestController
{
    /**
     * @Route("/logout", name="logout",methods={"POST"})
     */
    public function logout(): Response
    {
        unset($_COOKIE['BEARER']);
        unset($_COOKIE['REFRESH_TOKEN']);
        setcookie("BEARER", '', time() - 3000, "/");
        setcookie("REFRESH_TOKEN", '', time() - 3000, "/");

        return $this->json([
            'message' => 'Success'

        ], Response::HTTP_OK);
    }
    /**
     * @Route("/me", name="me")
     */
    public function checkAuth(): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'message' => 'not logged in'

            ], Response::HTTP_UNAUTHORIZED);
        }

        return $this->handleView($this->view($user, Response::HTTP_OK));
    }
    /**
     * @Route("/create-checkout-session", name="stripe-session")
     */
    public function stripeSession(StripeClient $stripeClient, Request $request): Response
    {
        $body = json_decode($request->getContent(), true);
        // $body = json_decode($request->getBody());
        try {
            // See https://stripe.com/docs/api/checkout/sessions/create
            // for additional parameters to pass.
            // {CHECKOUT_SESSION_ID} is a string literal; do not change it!
            // the actual Session ID is returned in the query parameter when your customer
            // is redirected to the success page.
            $checkout_session =  $stripeClient->createSession($body, $this->getUser());
        } catch (\Exception $e) {

            return $this->handleView($this->view([
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_BAD_REQUEST)); //returns json

        }

        // $tester = new \TeamTNT\Stripe\WebhookTester();
        // $tester->setVersion('2018-05-21');
        // $tester->setEndpoint('localhost:8000/api/webhooks');

        // $response = $tester->triggerEvent('customer.subscription.created');
        // echo ($response);
        return $this->handleView($this->view(['sessionId' => $checkout_session['id']]));
    }
    /**
     * @Route("/webhooks", name="stripe-webhooks",methods={"POST"})
     */
    public function webhooks(StripeClient $stripeClient, LoggerInterface $logger, Request $request): Response
    {
        $event = json_decode($request->getContent(), true);
        // Parse the message body and check the signature
        $webhookSecret = 'whsec_0SMRMO3yduOFkRabef7cdUYNqwihmE14';
        if ($webhookSecret) {
            try {
                $event = \Stripe\Webhook::constructEvent(
                    $request->getContent(),
                    $request->headers->get('stripe-signature'),
                    $webhookSecret
                );
            } catch (\Exception $e) {
                return $this->handleView($this->view(['error' => $e->getMessage()], 403));
            }
        } else {
            $event = json_decode($request->getContent(), true);
        }

        $type = $event['type'];
        $object = $event['data']['object'];
        $logger->info("event " . $type);
        $logger->info("customer" . $object->customer);

        switch ($type) {
            case 'checkout.session.completed':
                // Payment is successful and the subscription is created.
                // You should provision the subscription and save the customer ID to your database.
                $logger->info("session completed ");

                try {
                    $subscription = $stripeClient->createClient()->subscriptions->retrieve(
                        $object->subscription
                    );
                    $stripeClient->mapStripeCustomer($object->client_reference_id, $object->customer);
                    $stripeClient->mapStripeSubsciption($subscription->id, $object->customer, $subscription->current_period_end);
                } catch (\Throwable $e) {
                    throw $e;
                    return $this->handleView($this->view(['status' => 'error'], 500));
                }
                break;
            case 'customer.subscription.created':
                // Payment is successful and the subscription is created.
                // You should provision the subscription and save the customer ID to your database.
                break;
            case 'invoice.paid':
                // Continue to provision the subscription as payments continue to be made.
                // Store the status in your database and check when a user accesses your service.
                // This approach helps you avoid hitting rate limits.
                break;
            case 'invoice.payment_failed':
                // The payment failed or the customer does not have a valid payment method.
                // The subscription becomes past_due. Notify your customer and send them to the
                // customer portal to update their payment information.
                break;
                // ... handle other event types
            default:
                // Unhandled event type
        }
        return $this->handleView($this->view(['status' => 'success'], 200));
    }
}
