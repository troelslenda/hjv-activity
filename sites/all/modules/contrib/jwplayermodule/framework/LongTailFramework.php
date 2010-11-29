<?php
// $Id: LongTailFramework.php,v 1.1 2010/04/08 20:52:19 longtailvideo Exp $

/**
 * @file
 *
 * The primary backend class which handles the management of config saving,
 * loading and embedding of the player.
 */

include "SWFObjectConfig.php";
include "FlashVar.php";
include "Plugin.php";

class LongTailFramework
{

  private $path;
  private $xml;
  private $div_id = 1;

  /**
   * Constructor.
   * @param $workingPath The working directory for the LongTail framework.
   */
  function __construct($working_path) {
    $this->path = $working_path;
  }

  /**
   * Generates the list of flashvars supported by this version of the player
   * along with their defaults.
   * @param string $config (optional) Pass in a config file if you wish to load
   * values.
   * @return A structured array of the flashvars.
   */
  public function getPlayerFlashVars($config) {
    $f_vars = array();
    //Load the player xml file.
    $this->xml = simplexml_load_file($this->path . "/player.xml");
    $config_found = true;
    //Check if the config file exists.
    if (file_exists($this->getConfigPath($config))) {
      $config_file = simplexml_load_file($this->getConfigPath($config));
    } else {
      $config_found = false;
    }
    //Process the flashvars in the player xml file.
    foreach ($this->xml->flashvars as $flash_vars) {
      $f_var = array();
      $f_var_section = (string) $flash_vars["section"];
      $f_var_advanced = (string) $flash_vars["type"];
      //Ignore the flashvars categorized as "None."
      if ($f_var_advanced != "None") {
        foreach ($flash_vars as $flash_var) {
          $default = (string) $flash_var->{"default"};
          //If the config file was loaded and has an entry for the current flashvar
          //use the value in the config file.
          if ($config_found && $config_file->{$flash_var->name}) {
            $default = (string) $config_file->{$flash_var->name};
            $default = str_replace($this->getSkinURL(), "", $default);
            $default = preg_replace("/(\.swf|\.zip)/", "", $default);
          }
          $values = (array) $flash_var->select;
          $val = $values["option"];
          $type = (string) $flash_var["type"];
          //Load the possible values for the skin flashvar.
          if ($flash_var->name == "skin") {
            $type = "select";
            $val = array_keys($this->getSkins());
          }
          $temp_var = new FlashVar(
            (string) $flash_var->name, $default, (string) $flash_var->description, $val, $type
          );
          $f_var[(string) $flash_var->name] = $temp_var;
        }
        $f_vars[$f_var_advanced][$f_var_section] = $f_var;
      }
    }
    return $f_vars;
  }

  /**
   * Save the Player configuration to an xml file.
   * @param string $config The name of the config file to be saved.
   * @param string $xmlString The xml formatted content to be saved.
   */
  public function saveConfig($config, $xml_string) {
    $xml_file = $this->getConfigPath($config);
    $xml_handle = fopen($xml_file, "w");
    fwrite($xml_handle, "<config>\n" . $xml_string . "</config>");
    fclose($xml_handle);
  }

  /**
   * Delete a Player configuration.
   * @param string $config The name of the Player configuration to be deleted.
   */
  public function deleteConfig($config) {
    $xml_file = $this->getConfigPath($config);
    unlink($xml_file);
  }

  /**
   * Given a Player config name, return the associated xml file.
   * @param string $config The name of the Player configuration.
   * @return A reference to the xml file.
   */
  public function getConfigFile($config) {
    if ($config == "") {
      return "";
    }
    return simplexml_load_file($this->getConfigPath($config));
  }

  /**
   * Get the complete URL for a given Player configuration.
   * @param string $config The name of the Player configuration.
   * @return The complete URL.
   */
  public function getConfigURL($config) {
    if ($config == "") {
      return "";
    }
    return $GLOBALS["base_url"] . "/" . $this->getConfigPath($config);
  }

  /**
   * Get the relative path for a given Player configuration.
   * @param string $config The name of the Player configuration.
   * @return The relative path.
   */
  public function getConfigPath($config) {
    if ($config == "") {
      return "";
    }
    return $this->path . "/configs/" . $config . ".xml";
  }

  /**
   * Get the list of currently saved Player configurations.
   * @return string The list of configurations.
   */
  public function getConfigs() {
    $results = array();
    $handler = opendir($this->path . "/configs");
    $results[] = "New Player";
    while ($file = readdir($handler)) {
      if ($file != "." && $file != ".." && strstr($file, ".xml")) {
        $results[] = str_replace(".xml", "", $file);
      }
    }
    closedir($handler);
    return $results;
  }

  public function getPlayerPath() {
      return $this->path . "/player.swf";
  }

  /**
   * Get the complete URL for the Player swf.
   * @return string The complete URL.
   */
  public function getPlayerURL() {
    return $GLOBALS["base_url"] . "/" . $this->getPlayerPath();
  }

  /**
   * For the given Player configuration, returns the LTAS details.
   * @param string $config The name of the Player configuration
   * @return array An array containing the enabled state and channel code.
   */
  public function getLTASConfig($config) {
    $ltas = array();
    if (file_exists($this->getConfigPath($config))) {
      $config_file = simplexml_load_file($this->getConfigPath($config));
      $ltas["enabled"] = strstr((string) $config_file->plugins, "ltas");
      $ltas["channel_code"] = (string) $config_file->{"ltas.cc"};
    }
    return $ltas;
  }

  /**
   * Get the relative path for the plugins.
   * @return string The relative path
   */
  public function getPluginPath() {
    return $this->path . "/plugins/";
  }

  /**
   * Generates a list of the available plugins along with their flashvars and
   * default values.
   * @param string $config (optional) Pass in if you wish to load the plugin
   * enabled state and flashvar values.
   * @return array The list of available plugins
   */
  public function getPlugins($config) {
    $handler = opendir($this->getPluginPath());
    $plugins = array();
    while ($file = readdir($handler)) {
      if ($file != "." && $file != ".." && strstr($file, ".xml")) {
        $plugin = $this->processPlugin($file, $config);
        $plugins[$plugin->getRepository()] = $plugin;
      }
    }
    return $plugins;
  }

  /**
   * Get the relative path of the Player skins.
   * @return string The relative path
   */
  public function getSkinPath() {
    return $this->path . "/skins/";
  }

  /**
   * Get the complete URL for a skin.
   * @return string The complete URL
   */
  public function getSkinURL() {
    return $GLOBALS["base_url"] . "/" . $this->getSkinPath();
  }

  /**
   * Get the list of available skins.
   * @return string The list of available skins
   */
  public function getSkins() {
    $handler = opendir($this->getSkinPath());
    $skins = array();
    $skins["<Default>"] = "";
    while ($file = readdir($handler)) {
      if ($file != "." && $file != ".." && (strstr($file, ".zip") || strstr($file, ".swf"))) {
        $info = preg_split("/\./", $file);
        $skins[$info[0]] = $info[1];
      }
    }
    return $skins;
  }

  /**
   * Get the necessary embed parameters for embedding a flash object.  For now
   * we assume the flash object will be as big as the dimensions of the player.
   * @param string @config The Player configuration that is going to be embedded
   * @return array The array with the flash object dimensions
   */
  public function getEmbedParameters($config) {
    //If no config has been passed, use the player defaults.
    if ($config == "") {
      $flash_vars = $this->getPlayerFlashVars("");
      $params = array(
        "width" => $flash_vars["Basic Player Settings"]["General"]["width"]->getDefaultValue(),
        "height" => $flash_vars["Basic Player Settings"]["General"]["height"]->getDefaultValue(),
      );
    } else {
      $temp_file = $this->getConfigFile($config);
      $params = array(
        "width" => (string) $temp_file->width,
        "height" => (string) $temp_file->height,
      );
    }
    return $params;
  }

  /**
   * Generates the SWFObjectConfig object which acts as a wrapper for the
   * SWFObject javascript library.
   * @param string $config The name of the Player configuration
   * @param array $flashVars The array of flashVars to be used in the embedding
   * @return The configured SWFObjectConfig object to be used for embedding
   */
  public function generateSWFObject($config, $flash_vars) {
    return new SWFObjectConfig($this->div_id++, $this->getPlayerURL(), $this->getConfigURL($config), $this->getEmbedParameters($config), $flash_vars);
  }

  /**
   * Creates a Plugin object which represents a given Player plugin.
   * @param file $file The xml file which represents the Plugin
   * @param string $config The name of the Player configuration
   * @return A new Plugin object
   */
  private function processPlugin($file, $config) {
    $plugin_xml = simplexml_load_file($this->getPluginPath() . $file);
    $title = (string)$plugin_xml->{"title"};
    $version = (string) $plugin_xml->{"version"};
    $file_name = (string) $plugin_xml->{"filename"};
    $repository = (string) $plugin_xml->{"repository"};
    $description = (string) $plugin_xml->{"description"};
    $href = (string) $plugin_xml->{"page"};
    $enabled = false;
    $config_found = true;
    $plugin_name = str_replace(".swf", "", $file_name);
    //Check if the config file exists.
    if (file_exists($this->getConfigPath($config))) {
      $config_file = simplexml_load_file($this->getConfigPath($config));
    } else {
      $config_found = false;
    }
    $f_vars = array();
    //Process the flashvars in the plugin xml file.
    foreach($plugin_xml->flashvars as $flash_vars) {
      $f_var = array();
      $f_var_section = (string) $flash_vars["section"];
      $f_var_section = $f_var_section ? $f_var_section : "FlashVars";
      foreach ($flash_vars as $flash_var) {
        $default = (string) $flash_var->{"default"};
        //If the config file was loaded and has an entry for the current flashvar
        //use the value in the config file and set the plugin as enabled.
        if ($config_found && $config_file->{$plugin_name . "." . $flash_var->name}) {
          $default = (string) $config_file->{$plugin_name . "." . $flash_var->name};
          $enabled = strstr((string) $config_file->plugins, $repository) ? true : false;
        }
        $values = (array) $flash_var->select;
        $temp_var = new FlashVar(
          (string) $flash_var->name, $default, (string) $flash_var->description,
          (array) $values["option"], (string) $flash_var["type"]
        );
        $f_var[(string) $flash_var->name] = $temp_var;
      }
      $f_vars[$f_var_section] = $f_var;
    }
    $plugin = new Plugin($title, $version, $repository, $file_name, $enabled, $description, $f_vars, $href);
    return $plugin;
  }
}
