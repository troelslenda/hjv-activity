<?php

/**
 * Implementation of hook_ctools_plugin_api().
 */
function hjv_base_ctools_plugin_api() {
  list($module, $api) = func_get_args();
  if ($module == "strongarm" && $api == "strongarm") {
    return array("version" => 1);
  }
}

/**
 * Implementation of hook_node_info().
 */
function hjv_base_node_info() {
  $items = array(
    'activity' => array(
      'name' => t('Aktivitet'),
      'module' => 'features',
      'description' => '',
      'has_title' => '1',
      'title_label' => t('Title'),
      'has_body' => '0',
      'body_label' => '',
      'min_word_count' => '0',
      'help' => '',
    ),
  );
  return $items;
}

/**
 * Implementation of hook_views_api().
 */
function hjv_base_views_api() {
  return array(
    'api' => '2',
  );
}
