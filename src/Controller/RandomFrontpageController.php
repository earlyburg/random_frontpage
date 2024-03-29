<?php

namespace Drupal\random_frontpage\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Psr\Container\ContainerInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\PageCache\ResponsePolicy\KillSwitch;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * The page controller for the random_frontpage module.
 *
 * @package Drupal\random_frontpage\Controller
 */
class RandomFrontpageController extends ControllerBase {

  /**
   * The config factory interface.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  private ConfigFactoryInterface $config;

  /**
   * The renderer interface.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  private RendererInterface $renderer;

  /**
   * The kill switch.
   *
   * @var \Drupal\Core\PageCache\ResponsePolicy\KillSwitch
   */
  private KillSwitch $killSwitch;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The constructor function for an instance.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config
   *   The config factory interface.
   * @param \Drupal\Core\Render\RendererInterface $renderer
   *   The renderer interface.
   * @param \Drupal\Core\PageCache\ResponsePolicy\KillSwitch $killSwitch
   *   The killswitch object.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   *   The entity type manager interface.
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
   * The container create function.
   *
   * @param \Psr\Container\ContainerInterface $container
   *   The container interface.
   *
   * @return RandomFrontpageController|static
   *   The random_frontpage page controller.
   *
   * @throws \Psr\Container\ContainerExceptionInterface
   * @throws \Psr\Container\NotFoundExceptionInterface
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
   *   Creates a render array with random content of a specific type.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function randomFrontpageView() {
    $nodetype = $this->config->get('random_frontpage.adminsettings')->get('nodetypes');
    $displaymode = $this->config->get('random_frontpage.adminsettings')->get('displaymodes');
    $view_mode = ($displaymode == "") ? 'full' : $displaymode;
    if (empty($nodetype)) {
      $setNodeTypeText = $this->t('Please set a node type to randomly display at <a href="/admin/config/random_frontpage/adminsettings">/admin/config/random_frontpage/adminsettings</a>.');
      $element = ["#markup" => $setNodeTypeText];
    }
    else {
      $nids = $this->entityTypeManager
        ->getStorage('node')
        ->getQuery()
        ->condition('type', $nodetype)
        ->accessCheck(TRUE)
        ->execute();
      if (count($nids) != 0) {
        if (count($nids) >= 2) {
          $key = array_rand($nids, 1);
          $nid = $nids[$key];
        }
        else {
          $nid = $nids[0];
        }
        /* load and display the random node after clearing the page cache */
        $this->killSwitch->trigger();
        $node = $this->entityTypeManager->getStorage('node')->load($nid);
        $build = $this->entityTypeManager->getViewBuilder('node')->view($node, $view_mode);
        $markup = $this->renderer->render($build);
        $element = ["#markup" => $markup];
      }
      else {
        $createNodeTypeText = $this->t('There are no nodes of the selected type @nodetype to display. Please create some.', [
          '@nodetype' => $nodetype,
        ]);
        $element = ["#markup" => $createNodeTypeText];
      }
    }
    return $element;
  }

}
