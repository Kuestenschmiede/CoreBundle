/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 10
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2025, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

/**
 * con4gis - the gis-kit
 *
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */


/**
 * DialogHandler
 *
 * Useage:
 * var dh = new DialogHandler();
 * dh.show('TestTitle', 'Dies ist eine Testmessage!','');
 *
 * @constructor
 */
function DialogHandler() {
  var scope = this;
  this.buttons    = {
    'OK': function() {jQuery(this).dialog('close');},
    'Abbruch': function() {
      scope.callback = null;
      jQuery(this).dialog('close');
    }
  };
  this.modal      = true;
  this.callback = null;

  this.show = function (title, msg, linkUrl, opt_callback) {
    var date        = new Date();
    var randomId    = Math.random() * Math.random() + date.getTime();
    var uiMessage   = jQuery('<div class="uiMessage" id="uiMessage-' + randomId + '">' + msg + '</div>');
    if (opt_callback && opt_callback.function && typeof window[opt_callback.function] === 'function' ) {
      scope.callback = opt_callback;
    } else {
        delete scope.buttons['Abbruch'];
    }

    uiMessage.dialog({
      title:      title,
      modal:      this.modal,
      buttons:    this.buttons,

      open: function() {
        var parent = jQuery(this).parent();
        parent.next().css('z-index', parent.css('z-index'));
      },

      close: function() {
        jQuery(this).dialog('destroy').remove();
        if (linkUrl) {
          if ((linkUrl.indexOf(':')<0) && (linkUrl[0]!='/')) {
            linkUrl = linkUrl.replace('index.php/', "");
            window.location = linkUrl;
          }
        }
        if (scope.callback && scope.callback.function && typeof window[scope.callback.function] === 'function' && scope.callback.params) {
          window[scope.callback.function](scope.callback.params);
        } else if (scope.callback && scope.callback.function && typeof scope.callback.function === 'function') {
          window[scope.callback.function]();
        }
      }
    });

    uiMessage.dialog('moveToTop');
  }
}
