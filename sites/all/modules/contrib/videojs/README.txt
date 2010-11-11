// $Id: README.txt,v 1.1.2.1 2010/10/31 08:50:40 heshanmw Exp $

Dependencies
------------

* CCK (Content module)
* FileField

Install
-------

1) Copy the videojs folder to the modules folder in your installation. Usually
   this is sites/all/modules.

2) Download videojs from http://videojs.com.

3) In your Drupal site, enable the module under Administer -> Site building ->
   Modules (/admin/build/modules).

4) Add or configure a FileField under Administer -> Content management ->
   Content types -> [type] -> Manage Fields
   (admin/content/node-type/[type]/fields). Restrict the upload extension to
   only allow the video HTML5 extension. It's also a good idea to enable the Description
   option, as this can be used to label your files when they are displayed.

5) Configure the display of your FileField to use "videojs player".

6) Create a piece of content with the configured field. Give the file a
   description that will be used as the file label in the playlist.

Support
-------

Heshan <heshan at heidisoft dot com>
