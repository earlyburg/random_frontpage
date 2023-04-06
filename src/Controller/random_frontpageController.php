<?php
/**
 * @file
 * Contains Drupal\random_frontpage\Controller\random_frontpageController.
 */
namespace Drupal\random_frontpage\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Psr\Container\ContainerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Class random_frontpageController
 * @package Drupal\random_frontpage\Controller
 *
 */
class random_frontpageController extends ControllerBase {

  /**
   * The config factory interface.
   *
   * @var ConfigFactoryInterface $config
   */
  private $config;

 /**
  * The renderer interface.
  *
  * @var RendererInterface $renderer
  */
  private $renderer;

  /**
   * The kill switch.
   *
   * @var KillSwitch $killSwitch
   */
  private $killSwitch;

  /**
   * The entity type manager.
   *
   * @var EntityTypeManagerInterface $entityTypeManager
   */
   protected $entityTypeManager;

  /**
   * @param ConfigFactoryInterface $config
   * @param RendererInterface $renderer
   * @param KillSwitch $killSwitch
   * @param EntityTypeManagerInterface $entity_manager
   *
   */
  public function __construct(
    ConfigFactoryInterface $config,
    RendererInterface $renderer,
    KillSwitch $killSwitch,
    EntityTypeManagerInterface $entity_manager) {
    $this->config = $config;
    $this->renderer = $renderer;
    $this->killSwitch = $killSwitch;
    $this->entityTypeManager = $entity_manager;
  }

  /**
   * @param ContainerInterface $container
   * @return random_frontpageController|static
   * @throws \Psr\Container\ContainerExceptionInterface
   * @throws \Psr\Container\NotFoundExceptionInterface
   *
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('renderer'),
      $container->get('page_cache_kill_switch'),
      $container->get('entity_type.manager'),
    );
  }

  /**
   * Creates a rendered view of a selected node-type.
   *
   * @return array|string[]
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   *
   */
	public function randomFrontpageView() {
		$nodetype = $this->config->get('random_frontpage.adminsettings')->get('nodetypes');
		$displaymode = $this->config->get('random_frontpage.adminsettings')->get('displaymodes');
		$view_mode = ($displaymode == "") ? 'full' : $displaymode;
		$setNodeTypeText = 'Please set a node type to randomly display at <a href="/admin/config/random_frontpage/adminsettings">/admin/config/random_frontpage/adminsettings</a>.';
		$createNodeTypeText = 'There are no nodes of the selected type "' . $nodetype . '" to display. Please create some.';
		if (empty($nodetype)) {
			$element = array("#markup" => $setNodeTypeText);
		}
		else {
      $nids = $this->entityTypeManager->getStorage('node')->getQuery()->condition('type', $nodetype)->execute();
			if (count($nids) != 0) {
			  if ( count($nids) >= 2 ) {
					$key = array_rand($nids, 1);
					$nid = $nids[$key];
			  } else {
          $nid = $nids[0];
        }
				/* load and display the random node after clearing the page cache */
        $this->killSwitch->trigger();
				$node = $this->entityTypeManager->getStorage('node')->load($nid);
				$build = $this->entityTypeManager->getViewBuilder('node')->view($node, $view_mode);
				$markup = $this->renderer->render($build);
				$element = array("#markup" => $markup);
			} else {
				$element = array("#markup" => $createNodeTypeText);
			}
		}
		return $element;
	}
}

