<?php
/**
 * @file
 * Contains Drupal\random_frontpage\Controller\random_frontpageController.
 */
namespace Drupal\random_frontpage\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for /frontpage route output
 */
class random_frontpageController extends ControllerBase {

	public function randomFrontpageView() {

		$entity_type = 'node';
		$config = \Drupal::config('random_frontpage.adminsettings');
		$nodetype = $config->get('nodetypes');
		$displaymode = $config->get('displaymodes');

		/* set the view mode for the output */
		$view_mode = ($displaymode == "") ? 'full' : $displaymode;

		$setNodeTypeText = "Please set a node type to randomly display at <a href='/admin/config/random_frontpage/adminsettings'>/admin/config/random_frontpage/adminsettings</a>.";
		$createNodeTypeText = 'There are no nodes of the selected type "' . $nodetype . '" to display. Please create some.';
		/* check to see if the user has configured a node type choice in admin 
		 * otherwise output a placeholder page with a message
		 */
	  	if ( empty($nodetype) || !isset($nodetype) ) {

			$element = array("#markup" => $setNodeTypeText);

	  	} else {

			$nids = \Drupal::entityQuery('node')->condition('type', $nodetype)->execute();

			/* make sure that we have nodes of the selected node type to randomize and display  
			 * otherwise output a placeholder page with a message
			 */
	    		if (count($nids) != 0) {

				/* if there are two or more nodes */
	      			if ( count($nids) > 1 ) {

					/* pick a random NID */
					$key = array_rand($nids, 1);
					$nid = $nids[$key];
	      			}
				/* if there is only one node */
	      			if ( count($nids) == 1 ) {

					$nid = $nids[0];
	      			}
				/* load and display the node */
				$view_builder = \Drupal::entityTypeManager()->getViewBuilder($entity_type);
				$node_storage = \Drupal::entityTypeManager()->getStorage($entity_type);
				$node = $node_storage->load($nid);
				$build = $view_builder->view($node, $view_mode);
				$markup = render($build);
		    		$element = array("#markup" => $markup);

			} else {

				$element = array("#markup" => $createNodeTypeText);
			}
	  	}

		return $element;
	} 
}

