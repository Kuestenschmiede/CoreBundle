// Avoid recursions.
var incommit;

// Synchronous field values to other impacted fields is required, e.g. border
// size change should alter inline-style text as well.
function commitInternally( targetFields ) {
    if ( incommit )
        return;

    incommit = 1;

    var dialog = this.getDialog(),
        element = dialog.imageElement;
    if ( element ) {
        // Commit this field and broadcast to target fields.
        this.commit( IMAGE, element );

        targetFields = [].concat( targetFields );
        var length = targetFields.length,
            field;
        for ( var i = 0; i < length; i++ ) {
            field = dialog.getContentElement.apply( dialog, targetFields[ i ].split( ':' ) );
            // May cause recursion.
            field && field.setup( IMAGE, element );
        }
    }

    incommit = 0;
}

CKEDITOR.dialog.add( 'fileUploadDialog', function( editor ) {
    return {
        title: editor.lang.fileUpload.dialogTitle,
        minWidth: 400,
        minHeight: 200,

        contents: [
            {
                id: 'UploadFile',
                hidden: true,
                filebrowser: 'uploadButton',
                label: editor.lang.image.upload,
                elements: [
                    {
                        type: 'file',
                        id: 'uploadFile',
                        label: editor.lang.fileUpload.uploadFieldLabel,
                        style: 'height:40px',
                        size: 38
                    },
                    {
                        type: 'text',
                        id: 'fileTitle',
                        label: editor.lang.fileUpload.titleFieldLabel,
                        style: 'height:40px',
                        size: 38
                    },
                    {
                        type: 'text',
                        id: 'txtUrl',
                        label: editor.lang.fileUpload.urlFieldLabel,
                        onChange:function(){
                            var dialog = this.getDialog();
                            var sUrl = dialog.getValueOf('UploadFile', 'txtUrl');
                            var sRequestUri = sUrl.substring( sUrl.lastIndexOf('/')+1);
                            var fileName = sRequestUri.substring(0, sRequestUri.indexOf('&'));
                            dialog.setValueOf( 'UploadFile', 'fileTitle', fileName );
                        }
                    },
                    {
                        type: 'fileButton',
                        id: 'uploadButton',
                        filebrowser: 'UploadFile:txtUrl',
                        label: editor.lang.fileUpload.sendButtonLabel,
                        'for': [ 'UploadFile', 'uploadFile' ]
                    }
                ]

            }
        ],
        onOk: function() {

            var dialog = this;
            var a = editor.document.createElement( 'a' );
            a.setAttribute( 'title', dialog.getValueOf( 'UploadFile', 'fileTitle' ) );
            a.setAttribute( 'href', dialog.getValueOf( 'UploadFile', 'txtUrl' ) );
            a.setText( dialog.getValueOf( 'UploadFile', 'fileTitle' ) );
            editor.insertElement( a );
        }
    };
});