CKEDITOR.dialog.add( 'youtubeDialog', function( editor ) {
    return {
        title: 'Youtube Properties',
        minWidth: 400,
        minHeight: 200,
        contents: [
            {
                id: 'tab-basic',
                label: 'Basic Settings',
                elements: [
                    {
                        type: 'text',
                        id: 'youtube',
                        label: 'Youtube Link',
                        validate: CKEDITOR.dialog.validate.notEmpty( "Youtube Link field cannot be empty." )
                    }
                ]
            }
        ],
        onOk: function() {
            var dialog = this;

            var sValue = dialog.getValueOf( 'tab-basic', 'youtube' );


            var p = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
            var sVideoId = ( sValue.match( p ) ) ? RegExp.$1 : false;

            if(sVideoId != false){
                var sUrl = "//www.youtube.com/embed/"+sVideoId+"?rel=0&autoplay=0";
                var youtube = editor.document.createElement( 'iframe' );
                youtube.setAttribute("src", sUrl );
                youtube.setAttribute("frameborder", 0);
                youtube.setAttribute("allowfullscreen","");

                editor.insertElement(youtube);
            }
        }
    };
});