<?php

/**
 * Implementation of hook_menu_default_menu_links().
 */
function hjv_base_menu_default_menu_links() {
  $menu_links = array();

  // Exported menu link: primary-links:activities
  $menu_links['primary-links:activities'] = array(
    'menu_name' => 'primary-links',
    'link_path' => 'activities',
    'router_path' => 'activities',
    'link_title' => 'Aktiviteter',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '0',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-49',
  );
  // Exported menu link: primary-links:calendar
  $menu_links['primary-links:calendar'] = array(
    'menu_name' => 'primary-links',
    'link_path' => 'calendar',
    'router_path' => 'calendar',
    'link_title' => 'Aktivitets kalender',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '0',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-50',
  );
  // Exported menu link: primary-links:userlist
  $menu_links['primary-links:userlist'] = array(
    'menu_name' => 'primary-links',
    'link_path' => 'userlist',
    'router_path' => 'userlist',
    'link_title' => 'Medlems Overblik',
    'options' => array(
      'attributes' => array(
        'title' => '',
      ),
    ),
    'module' => 'menu',
    'hidden' => '0',
    'external' => '0',
    'has_children' => '0',
    'expanded' => '0',
    'weight' => '-48',
  );
  // Translatables
  // Included for use with string extractors like potx.
  t('Aktiviteter');
  t('Aktivitets kalender');
  t('Medlems Overblik');


  return $menu_links;
}
