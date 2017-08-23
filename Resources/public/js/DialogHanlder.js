/**
 * Created by pfroch on 02.02.17.
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

    this.show = function (title, msg) {
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
            }
        });

        uiMessage.dialog('moveToTop');
    }
}