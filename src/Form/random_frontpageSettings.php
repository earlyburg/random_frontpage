<?php

namespace Drupal\random_frontpage\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;

/**
 *
 */
class random_frontpageSettings extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const RANDOM_FRONTPAGE_SETTINGS = 'random_frontpage.adminsettings';

  /**
   * Drupal config factory interface.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface
   */
  protected EntityDisplayRepositoryInterface $entityDisplayRepository;

  /**
   * Class constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory interface.
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entityDisplayRepository
   *   The entity display repository interface.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    EntityDisplayRepositoryInterface $entityDisplayRepository) {
    parent::__construct($config_factory);
    $this->entityDisplayRepository = $entityDisplayRepository;
  }

  /**
   * Creates the container.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The dependency injection container object.
   *
   * @return \Drupal\Core\Form\ConfigFormBase|random_frontpageSettings|static
   *   Returns a new instance of this class.
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_display.repository'),
    );
  }

  /**
   * Returns the form ID string.
   *
   * @return string
   *   The form id.
   */
  public function getFormId() {
    return 'random_frontpage_form';
  }

  /**
   * Returns a list of the editable config names.
   *
   * @return string[]
   *   The array of config names.
   */
  protected function getEditableConfigNames() {
    return [
      static::RANDOM_FRONTPAGE_SETTINGS,
    ];
  }

  /**
   * Gets all the configured display modes as an array for a dropdown.
   *
   * @return array
   *   An array of the display modes for a dropdown.
   */
  public function getModes() {
    $modes = [];
    $modes_array = $this->entityDisplayRepository->getViewModes('node');
    foreach ($modes_array as $key => $value) {
      $modes[$key] = $value['label'];
    }
    return $modes;
  }

  /**
   * The Form Builder.
   *
   * @param array $form
   *   The form build.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::RANDOM_FRONTPAGE_SETTINGS);
    $form['nodetypes'] = [
      '#type' => 'select',
      '#title' => $this->t('Node Type'),
      '#options' => node_type_get_names(),
      '#default_value' => $config->get('nodetypes'),
      '#description' => $this->t('Select the node type you wish to display at the URL "/frontpage"'),
      '#required' => TRUE,
    ];

    $form['displaymodes'] = [
      '#type' => 'select',
      '#title' => $this->t('Display Mode'),
      '#options' => $this->getModes(),
      '#default_value' => $config->get('displaymodes'),
      '#description' => $this->t('Select the display mode for the URL "/frontpage"'),
      '#required' => TRUE,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * Form submit handler.
   *
   * @param array $form
   *   The form build.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable(static::RANDOM_FRONTPAGE_SETTINGS)
      ->set('nodetypes', $form_state->getValue('nodetypes'))
      ->set('displaymodes', $form_state->getValue('displaymodes'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
