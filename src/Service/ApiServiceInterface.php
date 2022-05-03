<?php

namespace Drupal\d8_activecampaign\Service;

/**
 * Provides an Active Campaign API Service Interface.
 */
interface ApiServiceInterface {

  /**
   * Adds a contact to a list, Creates the contact if it doesn't exist.
   *
   * @param array $contact
   *   Data used to update or create a contact, email key required.
   *
   *   $contact = array(
   *     'email' => 'johnDoe@gmail.com',
   *     'firstname' => 'John',
   *     'lastname' => 'Doe',
   *     'phone' => '1234567890'
   *    )
   * @param int $list_id
   *   An integer corresponding to an existing list id.
   *
   * @return array
   *   Array indicating operation status.
   *
   * @throws \InvalidArgumentException
   *   A valid email and exisiting list_id must be provided.
   */
  public function addToList(array $contact, int $list_id);

  /**
   * Adds a contact to a list, Creates the contact if it doesn't exist.
   *
   * @param string $email
   *   The email address of contact to update or create.
   * @param int $list_id
   *   An integer corresponding to an existing list id.
   *
   * @return array
   *   Array indicating operation status.
   *
   * @throws \InvalidArgumentException
   *   A valid email and exisiting list_id must be provided.
   */
  public function addToListByEmail(string $email, int $list_id);

  /**
   * Load an ActiveCampaign Contact by email.
   *
   * @param string $email
   *   The email of the contact to be loaded.
   *
   * @return array|null
   *   Returns Contact data array or null if contact not found.
   *
   * @throws \InvalidArgumentException
   *   A valid email must be provided.
   */
  public function loadContactByEmail(string $email);

  /**
   * Add one or more tags to an ActiveCampaign contacts.
   *
   * @param string $email
   *   The email of the contact to be tagged.
   * @param mixed|int|array $tag_id
   *   Tags to add to the contact.
   *   Pass a string (for one tag) or an array (for multiple tags).
   *
   * @return array|null
   *   Returns Tag data array or null if contact not found
   *
   * @throws \InvalidArgumentException
   *   A valid email must be provided.
   */
  public function addTagsByEmail(string $email, $tag_id);

}
