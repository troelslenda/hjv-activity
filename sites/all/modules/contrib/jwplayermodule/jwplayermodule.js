
// $Id: jwplayermodule.js,v 1.1 2010/04/08 20:52:19 longtailvideo Exp $

/**
 * This function looks for swfobject class items and loads them
 * as swfobjects. 
 */
Drupal.behaviors.swfobjectInit = function (context) {
  $('.swfobject:not(.swfobjectInit-processed)', context).addClass('swfobjectInit-processed').each(function () {
    var config = Drupal.settings.swfobject['files'][$(this).attr('id')];
    swfobject.embedSWF(config.url, $(this).attr('id'), config.width, config.height, config.version, config.express_redirect, config.flashVars, config.params, config.attributes);
  });
};
