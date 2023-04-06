<?php
/**
 * @file
 * Contains \Drupal\random_frontpage\Form\random_frontpageSettings.
 */
namespace Drupal\random_frontpage\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use \Drupal\Core\Entity\EntityDisplayRepositoryInterface;

class random_frontpageSettings extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const RANDOM_FRONTPAGE_SETTINGS = 'random_frontpage.adminsettings';

  /**
   * @var EntityDisplayRepositoryInterface $entityDisplayRepository
   *
   */
  protected EntityDisplayRepositoryInterface $entityDisplayRepository;

  /**
   * Class constructor.
   *
   * @param ConfigFactoryInterface $config_factory
   * @param EntityDisplayRepositoryInterface $entityDisplayRepository
   *
   */
  public function __construct(ConfigFactoryInterface $config_factory, EntityDisplayRepositoryInterface $entityDisplayRepository) {
    parent::__construct($config_factory);
    $this->entityDisplayRepository = $entityDisplayRepository;
  }

  /**
   * Creates the container.
   *
   * @param ContainerInterface $container
   * @return ConfigFormBase|random_frontpageSettings|static
   *
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
   *
   */
  public function getFormId() {
    return 'random_frontpage_form';
  }

  /**
   * @return string[]
   *
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
   *
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
   * @param FormStateInterface $form_state
   * @return array
   *
   */
	public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::RANDOM_FRONTPAGE_SETTINGS);
    $form['nodetypes'] = array(
      '#type' => 'select',
      '#title' => $this->t('Node Type'),
      '#options' => node_type_get_names(),
      '#default_value' => $config->get('nodetypes'),
      '#description' => $this->t('Select the node type you wish to display at the URL "/frontpage"'),
      '#required' => TRUE,
      );

    $form['displaymodes'] = array(
      '#type' => 'select',
      '#title' => $this->t('Display Mode'),
      '#options' => $this->getModes(),
      '#default_value' => $config->get('displaymodes'),
      '#description' => $this->t('Select the display mode for the URL "/frontpage"'),
      '#required' => TRUE,
      );
    return parent::buildForm($form, $form_state);
  }

  /**
   * Form submit handler.
   *
   * @param array $form
   * @param FormStateInterface $form_state
   *
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->configFactory->getEditable(static::RANDOM_FRONTPAGE_SETTINGS)
      ->set('nodetypes', $form_state->getValue('nodetypes'))
      ->set('displaymodes', $form_state->getValue('displaymodes'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
