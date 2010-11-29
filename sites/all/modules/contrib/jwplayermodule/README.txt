// $Id: README.txt,v 1.1 2010/04/08 20:52:19 longtailvideo Exp $

#############################################
# JW PLAYER MODULE                          #
#############################################

This module is provided by LongTail Video Inc.  It enables you to configure and embed the JW Player for Flash for use
on your Drupal website.  Full support for plugins, playlists, and internal and external media is provided.

For more information about the JW Player for Flash please visit http://www.longtailvideo.com.

Note that to use the LongTail Ad Solution you will need to apply on the LongTail site.

#############################################
# REQUIREMENTS                              #
#############################################

All necessary files for this plugin are provided.  

The only requirement is that you have the Drupal core Upload
module enabled if you would like your player to play content stored on a node.

#############################################
# INSTALLATION                              #
#############################################

Place the Module folder in your modules directory.

Download the non-commercial JW Player™ at http://www.longtailvideo.com/players/jw-flv-player/.

Extract the contents of the zip file.

Navigate to the jwplayermodule directory (/sites/all/modules/jwplayermodule).

Copy the player.swf and yt.swf file into the directory.

Navigate to Administer > Site Building > Modules.

Check the enabled checkbox next to the Module's name.

Click on Save configuration.

#############################################
# USAGE                                     #
#############################################

Go to Administer > Site configuration > JW Player setup

Enter a name for your Player.

Configure the Basic flashvars.

(Optional) Configure Advanced flashvars and add plugins.

Save your Player.

Create or edit a node.

Insert the following tag: [jwplayer|config=<Player name>|file=<your video] into the body.

<your video> can either be an URL or the description of a file you have uploaded to the node.

Save your node.

For more advanced and detailed description of the module please refer to the provided manual.