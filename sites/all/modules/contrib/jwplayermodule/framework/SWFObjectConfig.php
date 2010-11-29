<?php
// $Id: SWFObjectConfig.php,v 1.1 2010/04/08 20:52:19 longtailvideo Exp $

/**
 * @file
 *
 * A config object which functions as a represenation of a unique SWFObject.
 * It handles configuration and generation of the div used in embedding.
 */
class SWFObjectConfig {

  private $id;
  private $path;
  private $conf;
  private $cls;
  private $no_flash;

  /**
   * Constructor.
   * @param int $divId The unique identifier for the div used in the embed
   * process
   * @param string $playerPath The path to the player swf
   * @param string $config The path to the Player configuration to be used
   * @param array $params The list of SWFObject params
   * @param array $flashVars The list of additional flashvars to be used in the
   * embed
   */
  function __construct($divId, $player_path, $config, $params = array(), $flash_vars) {
    $this->id = "jwplayer-" . $divId;
    $this->path = $player_path;
    $this->conf = $config;
    $this->init($params, $flash_vars);
  }

  /**
   * Perform the necessary initialization operations to prepare the SWFObject
   * javascript object.
   * @param array $params The list of SWFObject params
   * @param array $flashVars The list of additional flashvars to be used in the
   * embed
   */
  private function init($params, $flash_vars) {
    //Initialize defaults.
    $default_params = array(
      "width" => 400,
      "height" => 280,
      "allowfullscreen" => "true",
      "allowscriptaccess" => "always",
      "wmode" => "transparent",
      "version" => "9",
      "type" => "movie",
      "bgcolor" => "#FFFFFF",
      "express_redirect" => "/expressinstall.swf",
      "class" => "",
    );

    $params += $default_params;
    $width = $params["width"];
    $height = $params["height"];
    $express = $params["express_redirect"];
    $version = $params["version"];
    $bg_color = $params["bgcolor"];
    $this->cls = implode(" ", array($params["class"], "swfobject"));
    $this->no_flash = $params["no_flash"];

    //Set the config flashvar to the Player configuration path
    if ($this->conf != "") {
      $flash_vars["config"] = $this->conf;
    }

    //Populate the values used for the embed process.
    $this->config["swfobject"]["files"][$this->id] = array(
      "url" => $this->path,
      "width" => $flash_vars["width"] ? $flash_vars["width"] : $width,
      "height" => $flash_vars["height"] ? $flash_vars["height"] : $height,
      "express_redirect" => $express,
      "version" => $version,
      "bgcolor" => $bg_color,
      //The id and name need to be set for LTAS support
      "attributes" => array("id" => $this->id, "name" => $this->id),
      "params" => $params,
      "flashVars" => $flash_vars,
    );
    //Clear the default array
    unset(
      $params["width"], $params["height"], $params["express_redirect"],
      $params["version"], $params["bgcolor"], $params["class"], $params["no_flash"]
    );
  }

  /**
   * Return the configuration values for generation of a SWFObject.
   * @return array The configuration values for generation of a SWFObject
   */
  public function getConfig() {
    return $this->config;
  }

  /**
   * Generate the div to be inserted into the html page.
   * @return string The div to be inserted into the html page
   */
  public function generateDiv() {
    //The outer div is needed for LTAS support.
    return  "<div id=\"$this->id-div\" name=\"$this->id-div\">\n" .
            "<div id=\"$this->id\" class=\"{$this->cls}\">{$this->no_flash}</div>\n" .
            "</div>\n";
  }

}
