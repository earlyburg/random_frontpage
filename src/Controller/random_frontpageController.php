<?php
/**
 * @file
 * Contains Drupal\random_frontpage\Controller\random_frontpageController.
 */
namespace Drupal\random_frontpage\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal;

/**
 * Class random_frontpageController
 * @package Drupal\random_frontpage\Controller
 *
 */
class random_frontpageController extends ControllerBase {

	public function randomFrontpageView() {
    $renderer = Drupal::service('renderer');
		$config = Drupal::config('random_frontpage.adminsettings');
		$nodetype = $config->get('nodetypes');
		$displaymode = $config->get('displaymodes');
		$view_mode = ($displaymode == "") ? 'full' : $displaymode;
		$setNodeTypeText = 'Please set a node type to randomly display at <a href="/admin/config/random_frontpage/adminsettings">/admin/config/random_frontpage/adminsettings</a>.';
		$createNodeTypeText = 'There are no nodes of the selected type "' . $nodetype . '" to display. Please create some.';
		if ( empty($nodetype) || !isset($nodetype) ) {
			$element = array("#markup" => $setNodeTypeText);
		}
		else {
			$nids = Drupal::entityQuery('node')->condition('type', $nodetype)->execute();
			if (count($nids) != 0) {
			  if ( count($nids) >= 2 ) {
					$key = array_rand($nids, 1);
					$nid = $nids[$key];
			  } else {
          $nid = $nids[0];
        }
				/* load and display the node */
        Drupal::service('page_cache_kill_switch')->trigger();
				$view_builder = Drupal::entityTypeManager()->getViewBuilder('node');
				$node_storage = Drupal::entityTypeManager()->getStorage('node');
				$node = $node_storage->load($nid);
				$build = $view_builder->view($node, $view_mode);
				$markup = $renderer->render($build);
				$element = array("#markup" => $markup);
			} else {
				$element = array("#markup" => $createNodeTypeText);
			}
		}
		return $element;
	}
}

