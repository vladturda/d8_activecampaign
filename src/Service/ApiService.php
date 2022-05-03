<?php

namespace Drupal\d8_activecampaign\Service;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Config\ConfigFactoryInterface;
use InvalidArgumentException;
use Mediatoolkit\ActiveCampaign\Client;
use Mediatoolkit\ActiveCampaign\Contacts\Contacts;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ApiService.
 */
class ApiService implements ApiServiceInterface {

  /**
   * API settings for Activecampaign URL and Key.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $settings;

  /**
   * API client initialized with API Url and Key.
   *
   * @var \Mediatoolkit\ActiveCampaign\Client
   */
  protected $client;

  /**
   * API endpoints for interacting with contacts.
   *
   * @var \Mediatoolkit\ActiveCampaign\Contacts\Contacts
   */
  protected $contacts;

  /**
   * Constructs a new ApiService object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   *   The Configs for this module.
   */
  public function __construct(ConfigFactoryInterface $config) {
    $this->settings = $config->get('d8_activecampaign.settings');

    $this->client = new Client(
      $this->settings->get('api_url'),
      $this->settings->get('api_key')
    );

    $this->contacts = new Contacts($this->client);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
       $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function addToList(array $contact, int $list_id) {
    // Minimum contact information required is an email.
    if (isset($contact['email']) && filter_var($contact['email'], FILTER_VALIDATE_EMAIL)) {
      $syncArray = [
        'email' => $contact['email'],
      ];

      // Include any additional optional contact info provided.
      foreach (['firstname', 'lastname', 'phone'] as $key) {
        if (isset($contact[$key])) {
          $syncArray['contact'][$key] = $contact[$key];
        }
      }

      // Array representing an existing or newly created contact.
      $syncResponse = Json::decode($this->contacts->sync($syncArray));

      $contactListArray = [
        'list' => $list_id,
        'contact' => $syncResponse['contact']['id'],
        'status' => 1,
      ];

      // Attempt to add the contact to specified list_id.
      $contactListResponse = Json::decode($this->contacts->updateListStatus($contactListArray));

      // A valid list_id must be provided, no list was found.
      if (empty($contactListResponse)) {
        throw new InvalidArgumentException('A valid list_id must be provided, no list was found.');
      }

      return $contactListResponse;

    } else {
      throw new InvalidArgumentException('The Contact array must contain a valid email.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function addToListByEmail(string $email, int $list_id) {
    // A valid email is required.
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $syncArray = [
        'email' => $email,
      ];

      // Array representing an existing or newly created contact.
      $syncResponse = Json::decode($this->contacts->sync($syncArray));

      // Contact List update attempt.
      $contactListArray = [
        'list' => $list_id,
        'contact' => $syncResponse['contact']['id'],
        'status' => 1,
      ];

      // Attempt to add the contact to specified list_id.
      $contactListResponse = Json::decode($this->contacts->updateListStatus($contactListArray));

      // A valid list_id must be provided, no list was found.
      if (empty($contactListResponse)) {
        throw new InvalidArgumentException('A valid list_id must be provided, list not found.');
      }

      return $contactListResponse;

    } else {
      throw new InvalidArgumentException('A valid email address must be provided.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function loadContactByEmail(string $email) {
    // A valid email must be provided.
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

      // Array of contacts that match the email criteria.
      $response = Json::decode($this->contacts->listAll([
        'email' => $email,
      ]));

      // No contacts were found matching the email criteria.
      if (empty($response['contacts'])) {
        return NULL;
      }

      return $response;

    } else {
      throw new InvalidArgumentException('A valid email address must be provided.');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function addTagsByEmail(string $email, $tag_id) {
    // A valid email must be provided.
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
      // Array of contacts that match the email criteria.
      $contact = $this->loadContactByEmail($email);

      if (is_null($contact)) {
        return NULL;
      }

      $contactId = $contact['contacts'][0]['id'];

      $tagIds = is_array($tag_id) ? $tag_id : [];
      $tagIds = is_int($tag_id) ? [$tag_id] : $tagIds;

      if (!empty($tagIds)) {
        $tags = [];
        foreach ($tagIds as $tid) {
          if (is_int($tid)) {
            $tags[] = $tid;
          } else {
            throw new InvalidArgumentException('A valid tag ID must be provided.');
          }
        }
      } else {
        throw new InvalidArgumentException('A valid tag ID or array of tag IDs must must be provided.');
      }
    } else {
      throw new InvalidArgumentException('A valid email address must be provided.');
    }

    // Add each tag in array, one at time.
    $responses = [
      'tagList' => [],
    ];

    foreach ($tags as $tid) {
      $responses['tagList'][] = Json::decode($this->contacts->tag($contactId, $tid));
    }

    return $responses;
  }

}
