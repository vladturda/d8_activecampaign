<?php

namespace Drupal\d8_activecampaign\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class RecurlySuccessfulPaymentSubscriber.
 */
class RecurlySuccessfullPaymentSubscriber implements EventSubscriberInterface {

  /**
   * Constructs a new RecurlySuccessfulPaymentSubscriber object.
   */
  public function __construct() {

  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events['recurly.successful_payment'] = ['recurlySuccessfulPayment'];

    return $events;
  }

  /**
   * This method is called when the recurly.successful_payment is dispatched.
   *
   * @param \Symfony\Component\EventDispatcher\Event $event
   *   The dispatched event.
   */
  public function recurlySuccessfulPayment(Event $event) {
    \Drupal::messenger()->addMessage('Event recurly.successful_payment thrown by Subscriber in module d8_activecampaign.', 'status', TRUE);
  }

}
