<?php
// $Id: jwplayermodule_admin.inc,v 1.2 2010/04/16 17:59:47 longtailvideo Exp $

/**
 * @file
 *
 * This file contains the hook implementations for rendering the admin
 * pages, as well as the AHAH implementation, for the module.
 */

/**
 * Javascript callback function for AHAH implementation.
 */
function jwplayermodule_js() {
  global $_jwplayermodule_framework;
  drupal_get_messages();
  $form_state = array("storage" => NULL, "submitted" => FALSE);
  $form_build_id = $_POST["form_build_id"];
  $form = form_get_cache($form_build_id, $form_state);
  $args = $form["#parameters"];
  $form_id = array_shift($args);
  $form["#post"] = $_POST;
  $form["#redirect"] = FALSE;
  $form["#programmed"] = FALSE;
  $form_state["post"] = $_POST;
  drupal_process_form($form_id, $form, $form_state);
  $form = drupal_rebuild_form($form_id, $form_state, $args, $form_build_id);
  $config_form = $form["Details"];
  unset($config_form["#prefix"], $config_form["#suffix"]);
  $output = drupal_render($config_form);
  print drupal_json(array("status" => TRUE, "data" => $output));
  exit();
}

/**
 * Constructs the administration form for customizing Players.
 * This monstrosity of a method needs to be refactored, but will likely be
 * replaced entirely in the next version.
 */
function jwplayermodule_admin($form_state = NULL) {
  $form = array();

  global $_jwplayermodule_framework;

  if (!file_exists($_jwplayermodule_framework->getPlayerPath())) {
    drupal_set_message("The JW Player is unavailable and must be downloaded.", "error", FALSE);
  }

  $config = variable_get(LONGTAIL_KEY . CONFIG, "");
  $configs = $_jwplayermodule_framework->getConfigs();
  $flash_vars = $_jwplayermodule_framework->getPlayerFlashVars($config);
  $plugins = $_jwplayermodule_framework->getPlugins($config);
  $ltas = $_jwplayermodule_framework->getLTASConfig($config);

  //Add custom css to prevent text shifting due to AHAH progress bar.
  $module_path = drupal_get_path("module", "jwplayermodule");
  $css_path = $module_path . "/jwplayermodule.css";
  drupal_add_css($css_path);
  jwplayermodule_add_js();
  jwplayermodule_add_delete_check();

  $form["Details"] = array(
    "#description" => t(JW_SETUP_DESC),
    "#prefix" => "<div class=\"description\">" . JW_SETUP_DESC . "</div><div id=\"jwplayermodule-config\">",
    "#suffix" => "</div>",
  );
  //Start of Manage Players section
  $form["Details"]["Configuration"] = array(
    "#type" => "fieldset",
    "#title" => t("Manage Players"),
    "#description" => t(JW_PLAYER_DESC),
    "#collapsible" => FALSE,
  );
  $form["Details"]["Configuration"]["Configs"] = array(
    "#type" => "select",
    "#title" => "Players",
    "#options" => $configs,
    "#description" => t("Select a Player."),
    "#default_value" => jwplayermodule_get_config_index($configs, $config),
    "#ahah" => array(
      "event" => "change",
      "path" => "admin/settings/jwplayermodule/js",
      "wrapper" => "jwplayermodule-config",
      "method" => "replace",
      "progress" => array("type" => "bar", "message" => t("Loading your Player...")),
    ),
  );
  $form["Details"]["Configuration"]["get_configs"] = array(
    "#type" => "submit",
    "#value" => "Configs",
    "#submit" => array("jwplayer_config_submit"),
    "#prefix" => "<div style=\"display:none\">",
    "#suffix" => "</div>",
  );
  if ($config && $config != "New Player") {
    $form["Details"]["Configuration"]["delete"] = array(
      "#type" => "submit",
      "#value" => t("Delete"),
    );
  }
  else {
    $form["Details"]["Configuration"]["delete"] = array(
      "#prefix" => "<div>",
      "#suffix" => "</div>",
    );
  }
  //Start of Player Details section.
  $form["Details"]["Players"] = jwplayermodule_generate_details($config, $flash_vars);
  //Start of LTAS subsection.
  $form["Details"]["Players"]["LTAS"] = array(
    "#type" => "fieldset",
    "#title" => "LongTail AdSolution",
    "#collapsible" => TRUE,
    "#collapsed" => TRUE,
  );
  $form["Details"]["Players"]["LTAS"]["jwplayermodule_plugin_ltas" . "_enable"] = array(
    "#type" => "checkbox",
    "#title" => "Enable LTAS",
    "#default_value" => $ltas["enabled"],
    "#description" => t(JW_PLAYER_LTAS_DESC),
  );
  $form["Details"]["Players"]["LTAS"]["jwplayermodule_plugin_ltas" . "_cc"] = array(
    "#type" => "textfield",
    "#title" => "ltas.cc",
    "#default_value" => $ltas["channel_code"],
    "#description" => t("Your LTAS channel code."),
  );
  //Start of plugins section.
  $form["Details"]["Players"]["Plugins"] = jwplayermodule_generate_plugins($plugins);
  $form["Details"]["Players"]["submit-2"] = array(
    "#type" => "submit",
    "#value" => t("Save"),
  );
  return $form;
}

/**
 * Generate the player details.
 * @param string $config The name of the Player configuration
 * @param array $flash_vars The list of flashvars for the player
 * @return The form array of Player details
 */
function jwplayermodule_generate_details($config, $flash_vars) {
  $detail_array = array(
    "#type" => "fieldset",
    "#title" => t("Player Details"),
    "#description" => t(JW_PLAYER_DETAILS_DESC),
    "#collapsible" => FALSE,
  );
  $detail_array["submit-1"] = array(
    "#type" => "submit",
    "#value" => t("Save"),
  );
  if ($config && $config != "New Player") {
    $detail_array["discard"] = array(
      "#type" => "submit",
      "#value" => t("Discard"),
    );
  }
  $detail_array["Name"] = array(
    "#type" => "textfield",
    "#title" => t("Name"),
    "#default_value" => !$config || $config == "New Player" ? "" : $config,
    "#description" => t("The name of this configuration."),
  );
  //Generate fields for player flashvars.  This includes Basic and Advanced Player Settings.
  foreach (array_keys($flash_vars) as $key) {
    $collapsed = TRUE;
    $detail_array[$key] = array(
      "#type" => "fieldset",
      "#title" => t($key),
      "#collapsible" => TRUE,
      "#collapsed" => $collapsed,
    );
    foreach (array_keys($flash_vars[$key]) as $groups) {
      $detail_array[$key][$groups] = array(
        "#type" => "fieldset",
        "#title" => t($groups),
        "#collapsible" => TRUE,
        "#collapsed" => $collapsed,
      );
      foreach ($flash_vars[$key][$groups] as $fvar) {
        $f_var_array = array();
        $f_var_array["#title"] = t($fvar->getName());
        $f_var_array["#default_value"] = $fvar->getDefaultValue();
        $f_var_array["#description"] = t($fvar->getDescription());
        if ($fvar->getType() == FlashVar::SELECT) {
          $options = array();
          foreach ($fvar->getValues() as $val) {
            $options[$val] = $val;
          }
          $f_var_array["#type"] = "select";
          $f_var_array["#options"] = $options;
        }
        else {
          $f_var_array["#type"] = "textfield";
        }
        $detail_array[$key][$groups]["jwplayermodule_player_". $fvar->getName()] = $f_var_array;
      }
    }
  }
  return $detail_array;
}

/**
 * Generate the plugin details.
 * @param array $plugins The array of plugins
 * @return The form array of plguin details
 */
function jwplayermodule_generate_plugins($plugins) {
  $plugin_array = array(
    "#type" => "fieldset",
    "#title" => t("Player Plugins"),
    "#collapsible" => TRUE,
    "#collapsed" => TRUE,
  );
  foreach ($plugins as $plugin) {
    $plugin_array[$plugin->getRepository()] = array(
      "#type" => "fieldset",
      "#title" => t($plugin->getTitle()),
      "#collapsible" => TRUE,
      "#collapsed" => TRUE,
    );
    $plugin_array[$plugin->getRepository()]["jwplayermodule_plugin_" . $plugin->getRepository() . "_enable"] = array(
      "#type" => "checkbox",
      "#title" => "Enable Plugin",
      "#default_value" => $plugin->isEnabled(),
      "#description" => t($plugin->getDescription() . "  Please visit the <a href=\"". $plugin->getPage() . "/" . JW_PLAYER_GA_VARS . "\" target=\"_blank\">Plugin Page</a> for more information."),
    );
    foreach (array_keys($plugin->getFlashVars()) as $plugin_flash_vars) {
      $p_vars = $plugin->getFlashVars();
      foreach ($p_vars[$plugin_flash_vars] as $plugin_flash_var) {
        $plugin_flash_var_array = array();
        $plugin_flash_var_array["#title"] = t($plugin->getPluginPrefix() . "." . $plugin_flash_var->getName());
        $plugin_flash_var_array["#default_value"] = $plugin_flash_var->getDefaultValue();
        $plugin_flash_var_array["#description"] = t($plugin_flash_var->getDescription());
        if ($plugin_flash_var->getType() == FlashVar::SELECT) {
          $options = array();
          foreach ($plugin_flash_var->getValues() as $plugin_val) {
            $options[$plugin_val] = $plugin_val;
          }
          $plugin_flash_var_array["#type"] = "select";
          $plugin_flash_var_array["#options"] = $options;
        }
        else {
          $plugin_flash_var_array["#type"] = "textfield";
        }
        $plugin_array[$plugin->getRepository()]["jwplayermodule_plugin_". $plugin->getPluginPrefix() . "|" . $plugin_flash_var->getName()] = $plugin_flash_var_array;
      }
    }
  }
  return $plugin_array;
}

function jwplayermodule_license($form_state = NULL) {
  $form = array();

  global $_jwplayermodule_framework;

  if (!file_exists($_jwplayermodule_framework->getPlayerPath())) {
    drupal_set_message("The JW Player is unavailable and must be downloaded.", "error", FALSE);
  }

  $form["Standard"] = array(
    "#type" => "fieldset",
    "#title" => "Install JW Player&trade;",
    "#description" => t(JW_PLAYER_INSTALL),
  );
  $form["Pro"] = array(
    "#type" => "fieldset",
    "#title" => "Player Licensing",
  );
  $form["Pro"]["License"] = array(
    "#type" => "fieldset",
    "#title" => "Licensing Details",
    "#description" => t(JW_PLAYER_LICENSE_DESC),
  );
  $form["Pro"]["Install"] = array(
    "#type" => "fieldset",
    "#title" => "Install JW Player&trade; Pro",
    "#description" => t(JW_PLAYER_LICENSE_INSTALL),
  );
  return $form;
}

/**
 * Implementation of validate function for configuration form submit.
 */
function jwplayermodule_admin_validate($form, $form_state) {
  global $_jwplayermodule_framework;
  if ($form_state["clicked_button"]["#id"] == "edit-submit-1" || $form_state["clicked_button"]["#id"] == "edit-submit-2") {
    if ($form_state["values"]["Name"] == "") {
      form_set_error("", t("You must enter a name for the Player before saving."));
    }
  }
  elseif ($form_state["clicked_button"]["#id"] == "edit-delete") {
    $configs = $_jwplayermodule_framework->getConfigs();
    if ($configs[$form_state["values"]["Configs"]] == "New Player") {
      form_set_error("", t("You must select a configuration (other than \"New Player\") to delete."));
    }
  }
}

/**
 * Submit function which handles the AHAH event.
 */
function jwplayer_config_submit($form, &$form_state) {
  global $_jwplayermodule_framework;
  unset($form_state["submit_handlers"]);
  form_execute_handlers("submit", $form, $form_state);
  $configs = $_jwplayermodule_framework->getConfigs();
  variable_set(LONGTAIL_KEY . CONFIG, $configs[$form_state["values"]["Configs"]]);
  $form_state["rebuild"] = TRUE;
}

/**
 * Implementation of submit function for the configuration form.
 * Note that specific buttons are processed due to the AHAH functionality
 * triggering a form submit that shouldn't be processed.
 */
function jwplayermodule_admin_submit($form, &$form_state) {
  global $_jwplayermodule_framework;
  $configs = $_jwplayermodule_framework->getConfigs();
  $config = "";
  //Saving a Player.
  if ($form_state["clicked_button"]["#id"] == "edit-submit-1" || $form_state["clicked_button"]["#id"] == "edit-submit-2") {
    $plugins = array();
    foreach ($form_state["values"] as $name => $value) {
      //Get the name of the Player being saved.
      if (strstr($name, "Name")) {
        $config = check_plain($value);
      }
      //Process the player flashvars.
      if (strstr($name, "jwplayermodule_player_") && $value != NULL) {
        $val = check_plain($value);
        $new_val = $val;
        $new_name = str_replace("jwplayermodule_player_", "", $name);
        //Skins are a special case.  Must generate the URL for the skin to be used.
        if ($new_name == "skin") {
          if ($new_val != check_plain("<Default>")) {
            $skins = $_jwplayermodule_framework->getSkins();
            $new_val = $_jwplayermodule_framework->getSkinURL() . $val . "." . $skins[$val];
            $data[$new_name] = $new_val;
          }
        }
        else {
          $data[$new_name] = $new_val;
        }
      //Check if a plugin is enabled and add to plugin list.
      }
      elseif (strstr($name, "jwplayermodule_plugin_") && strstr($name, "_enable")) {
        $plugins[str_replace("_enable", "", str_replace("jwplayermodule_plugin_", "", $name))] = check_plain($value);
      //Process the plugin flashvars.
      }
      elseif (strstr($name, "jwplayermodule_plugin_") && $value != NULL) {
        $plugin_key = str_replace("|", ".", str_replace("jwplayermodule_plugin_", "", $name));
        foreach (array_keys($plugins) as $repo) {
          $key = explode(".", $plugin_key);
          if (strstr($repo, $key[0]) && $plugins[$repo]) {
            $data[$plugin_key] = check_plain($value);
          }
        }
      }
    }
    $active_plugins = array();
    //Build final list of plugins to be used for this Player.
    foreach ($plugins as $name => $enabled) {
      if ($enabled) {
        $active_plugins[] = $name;
      }
    }
    $plugin_string = implode(", ", $active_plugins);
    if ($plugin_string != "") {
      $data["plugins"] = $plugin_string;
    }
    $_jwplayermodule_framework->saveConfig($config, format_xml_elements($data));
    variable_set(LONGTAIL_KEY . CONFIG, $config);
    variable_set(LONGTAIL_KEY . SUBMIT, "SAVE");
    //Deleting a Player.
  }
  elseif ($form_state["clicked_button"]["#id"] == "edit-delete") {
    $_jwplayermodule_framework->deleteConfig($configs[$form_state["values"]["Configs"]]);
    variable_set(LONGTAIL_KEY . CONFIG, "");
    variable_set(LONGTAIL_KEY . SUBMIT, "DELETE");
    //Reverting Player changes.
  }
  elseif ($form_state["clicked_button"]["#id"] == "edit-discard") {
    foreach ($form_state["values"] as $name => $value) {
      if (strstr($name, "Name")) {
        $config = $value;
      }
    }
    variable_set(LONGTAIL_KEY . CONFIG, $config);
    variable_set(LONGTAIL_KEY . SUBMIT, "RESET");
  }
}