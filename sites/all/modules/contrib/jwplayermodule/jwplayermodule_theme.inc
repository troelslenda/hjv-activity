<?php
// $Id: jwplayermodule_theme.inc,v 1.2 2010/04/16 17:59:47 longtailvideo Exp $

/**
 * @file
 *
 * This file contains the theme functions for the module.
 */

/**
 * Theme function for getting the URL for a file attached on the node.  Can use
 * the description or its index.
 */
function theme_jwplayermodule_get_attachment_url($fid, $node, $use_ID = FALSE) {
  $count = 0;
  if (isset($node) && isset($node->files)) {
    foreach ($node->files as $file) {
      if ($fid == $file->description || ($fid == $count && $use_ID)) {
        $path_array = explode("/", $file->filepath);
        $path_array[count($path_array) - 1] = $file->filename;
        return $GLOBALS["base_url"] . "/" . implode("/", $path_array);
      }
      $count++;
    }
  }
  return FALSE;
}

/**
 * Theme function for generating the SWFObject embed code for a player.
 */
function theme_jwplayermodule_render_player($config, $flash_vars) {
  static $done = FALSE;
  global $_jwplayermodule_framework;
  $swf = $_jwplayermodule_framework->generateSWFObject($config, $flash_vars);
  if (!$done) {
    $external_js = "http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js";
    //Drupal 6.x cannot add external javascript normally.  This hack circumvents that.
    drupal_add_js('document.write(unescape("%3Cscript src=\''. $external_js . '\' type=\'text/javascript\'%3E%3C/script%3E"));', 'inline');
    drupal_add_js(drupal_get_path("module", "jwplayermodule") . "/jwplayermodule.js", "module", "footer");
    $done = TRUE;
  }
  drupal_add_js($swf->getConfig(), "setting", "footer", FALSE, TRUE, FALSE);
  return $swf->generateDiv();
}