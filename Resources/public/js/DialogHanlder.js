/**
 * con4gis - the gis-kit
 *
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */

/**
 * DialogHandler
 *
 * Useage:
 * var dh = new DialogHandler();
 * dh.show('TestTitle', 'Dies ist eine Testmessage!');
 *
 * @constructor
 */
function DialogHandler() {
    this.buttons    = {'OK': function() {jQuery(this).dialog('close');}};
    this.modal      = true;

    this.show = function (title, msg, linkUrl) {
        var date        = new Date();
        var randomId    = Math.random() * Math.random() + date.getTime();
        var uiMessage   = jQuery('<div id="uiMessage-' + randomId + '">' + msg + '</div>');

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
            }
        });

        uiMessage.dialog('moveToTop');
    }
}