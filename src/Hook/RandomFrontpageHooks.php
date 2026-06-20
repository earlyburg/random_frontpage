<?php

namespace Drupal\random_frontpage\Hook;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Url;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Hook implementations for random_frontpage.
 */
class RandomFrontpageHooks {
  use StringTranslationTrait;
  /**
   * Implements hook_help().
   */

  #[Hook('help')]

  /**
   * The help function.
   *
   * @param string $route_name
   *   The route name for the help page being rendered.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match object for the current page.
   *
   * @return string
   *   The help text to be displayed for the given route.
   */
  public function help($route_name, RouteMatchInterface $route_match) {
    switch ($route_name) {
      /* Main module help for the random_frontpage module. */
      case 'help.page.random_frontpage':
        $output = '';
        $output .= '<h3>' . $this->t('About') . '</h3>';
        $output .= '<div>' . $this->t('This module Creates a page at the URL "/frontpage" which displays a different, random node of a selected type every time it\'s accessed.') . '</div>';
        $output .= '<div>' . $this->t('To configure, select a node type to display, and a display format at <a href=":aliases">Random Frontpage Settings</a>, and save your preferences.', [
          ':aliases' => Url::fromRoute('random_frontpage.admin_settings_form')->toString(),
        ]) . '</div>';
        return $output;
    }
  }

}
