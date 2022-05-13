<?php
/**
 * @file
 * Contains \Drupal\random_frontpage\Form\random_frontpageSettings.
 */
namespace Drupal\random_frontpage\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal;

class random_frontpageSettings extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const RANDOM_FRONTPAGE_SETTINGS = 'random_frontpage.adminsettings';

  /**
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
   * @return array
   *
   */
	public function getModes() {
	  $modes = [];
		$modes_array = Drupal::service('entity_display.repository')->getViewModes('node');
		foreach ($modes_array as $key => $value) {
			$modes[$key] = $value['label'];
		}
		return $modes;
	}

  /**
   * @param array $form
   * @param FormStateInterface $form_state
   * @return array
   *
   */
	public function buildForm(array $form, FormStateInterface $form_state) {

    $config = $this->config(static::RANDOM_FRONTPAGE_SETTINGS);

    $form['nodetypes'] = array(
      '#type' => 'select',
      '#title' => t('Node Type'),
      '#options' => node_type_get_names(),
      '#default_value' => $config->get('nodetypes'),
      '#description' => "Select the node type you wish to display at the URL '/frontpage'",
      '#required' => TRUE,
    );

		$form['displaymodes'] = array(
			'#type' => 'select',
			'#title' => t('Display Mode'),
			'#options' => $this->getModes(),
			'#default_value' => $config->get('displaymodes'),
			'#description' => "Select the display mode for the URL '/frontpage'",
      '#required' => TRUE,
		);
		return parent::buildForm($form, $form_state);
	}

  /**
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

