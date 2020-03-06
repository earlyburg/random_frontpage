<?php
/**
 * @file
 * Contains \Drupal\random_frontpage\Form\random_frontpageSettings.
 */
namespace Drupal\random_frontpage\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class random_frontpageSettings extends ConfigFormBase {

	/**  
	 * {@inheritdoc}  
	 */  
	protected function getEditableConfigNames() {  
		return [  
			'random_frontpage.adminsettings',  
		];  
	}  


	/**  
	 * {@inheritdoc}  
	 */  
	public function getFormId() {  
		return 'random_frontpage_form';  
	}


	/**  
	 * {@inheritdoc}  
	 */  
	public function getTypes() {

	  	$nodetypes = array();
		$nodetypes = node_type_get_names();

	  	return $nodetypes;
	}


	/**  
	 * {@inheritdoc}  
	 */  
	public function getModes() {

		$modes_array = $modes = array();
		$modes_array = \Drupal::service('entity_display.repository')->getViewModes('node');

		foreach ($modes_array as $key => $value) {

			$modes[$key] = $value['label'];
		}

		return $modes;
	}


	/**  
	 * {@inheritdoc}  
	 */  
	public function buildForm(array $form, FormStateInterface $form_state) {

		$config = $this->config('random_frontpage.adminsettings'); 

	  	$form['nodetypes'] = array(
	    		'#type' => 'select',
	    		'#title' => t('Node Type'),
	    		'#options' => $this->getTypes(),
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
	 * {@inheritdoc}  
	 */  
	public function submitForm(array &$form, FormStateInterface $form_state) {  
		parent::submitForm($form, $form_state);  

		$this->config('random_frontpage.adminsettings')  
			->set('nodetypes', $form_state->getValue('nodetypes'))
			->set('displaymodes', $form_state->getValue('displaymodes'))  
			->save();  
	}  

}

