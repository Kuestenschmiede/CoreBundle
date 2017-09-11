/*
 WYSIWYG-BBCODE editor
 Copyright (c) 2009, Jitbit Sotware, http://www.jitbit.com/
 PROJECT HOME: http://wysiwygbbcode.codeplex.com/
 All rights reserved.

 Redistribution and use in source and binary forms, with or without
 modification, are permitted provided that the following conditions are met:
 * Redistributions of source code must retain the above copyright
 notice, this list of conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright
 notice, this list of conditions and the following disclaimer in the
 documentation and/or other materials provided with the distribution.
 * Neither the name of the <organization> nor the
 names of its contributors may be used to endorse or promote products
 derived from this software without specific prior written permission.

 THIS SOFTWARE IS PROVIDED BY Jitbit Software ''AS IS'' AND ANY
 EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 DISCLAIMED. IN NO EVENT SHALL Jitbit Software BE LIABLE FOR ANY
 DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

 Modified by
 Tobias Dobbrunz - Küstenschmiede GmbH Software & Design (kuestenschmiede.de)
 tobias.dobbrunz@kuestenschmiede.de
 last edit: 2013-10-25
 */

var wswgEditor = new function () {

    this.getEditorDoc = function () {
        return myeditor;
    }
    this.getIframe = function () {
        return ifm;
    }
    this.IsEditorVisible = function () {
        return editorVisible;
    }

    var myeditor, ifm;
    var body_id, textboxelement;
    var content;
    var isIE = /msie|MSIE/.test(navigator.userAgent);
    var isChrome = /Chrome/.test(navigator.userAgent);
    var isSafari = /Safari/.test(navigator.userAgent) && !isChrome;
    var browser = isIE || window.opera;
    var textRange;
    var editorVisible = false;
    var enableWysiwyg = false;
    var editBarButtons = '&nbsp; <button title="Bold" onclick="wswgEditor.doClick(\'bold\');" role="button" style="background-image:url(\'bundles/con4giscore/vendor/wswgEditor/images/bold.png\')"></button>'
        + '<button title="Italic" onclick="wswgEditor.doClick(\'italic\');" role="button" style="background-image:url(\'bundles/con4giscore/vendor/wswgEditor/images/italic.png\')"></button>'
        + '<button title="Underline" onclick="wswgEditor.doClick(\'underline\');" role="button" style="background-image:url(\'bundles/con4giscore/vendor/wswgEditor/images/underlined.png\')"></button>'
            // +' <span>|</span> '
            // +'<input type="number" title="textsize" id="wswgTextsize" class="wswgInput" min="0" max="60" value="11" onchange="wswgEditor.doClick(\'underline\');">'
        + ' <span>|</span> '
        + '<button title="Textalign (left)" onclick="wswgEditor.doClick(\'justifyLeft\');" role="button" style="background-image:url(\'bundles/con4giscore/vendor/wswgEditor/images/textalign-left.png\')"></button>'
        + '<button title="Textalign (center)" onclick="wswgEditor.doClick(\'justifyCenter\');" role="button" style="background-image:url(\'bundles/con4giscore/vendor/wswgEditor/images/textalign-center.png\')"></button>'
        + '<button title="Textalign (right)" onclick="wswgEditor.doClick(\'justifyRight\');" role="button" style="background-image:url(\'bundles/con4giscore/vendor/wswgEditor/images/textalign-right.png\')"></button>'
        + '<button title="Textalign (justify)" onclick="wswgEditor.doClick(\'justifyFull\');" role="button" style="background-image:url(\'bundles/con4giscore/vendor/wswgEditor/images/textalign-justify.png\')"></button>'
        + ' <span>|</span> '
        + '<button title="Hyperlink" onclick="wswgEditor.doLink();" role="button" style="background-image:url(\'bundles/con4giscore/vendor/wswgEditor/images/url.png\')"></button>'
        + '<button title="Image" onclick="wswgEditor.doImage();" role="button" style="background-image:url(\'bundles/con4giscore/vendor/wswgEditor/images/img.png\')"></button>'
        + '<button title="List (unordered)" onclick="wswgEditor.doClick(\'InsertUnorderedList\');" role="button" style="background-image:url(\'bundles/con4giscore/vendor/wswgEditor/images/icon_list.png\');"></button>'
        + '<button title="List (ordered)" onclick="wswgEditor.doClick(\'InsertOrderedList\');" role="button" style="background-image:url(\'bundles/con4giscore/vendor/wswgEditor/images/icon_olist.png\');"></button>'
        + '<button title="Textcolor" onclick="wswgEditor.showColorGrid2(\'none\')" role="button" style="background-image:url(\'bundles/con4giscore/vendor/wswgEditor/images/colors.png\');"></button><div id="wswgColorPicker" class="wswgColorPicker"></div>'
        + '<button title="Quote" onclick="wswgEditor.insertTag(\'[quote=UNKNOWN]\', \'[/quote]\');" role="button" style="background-image:url(\'bundles/con4giscore/vendor/wswgEditor/images/icon_quote.png\');"></button>'
            //+'<button title="youtube" onclick="wswgEditor.InsertYoutube();" role="button" style="background-image:url(\'bundles/con4giscore/vendor/wswgEditor/images/icon_youtube.png\');"></button>'
        + '<button title="Spoiler" onclick="wswgEditor.insertTag(\'[spoiler]\', \'[/spoiler]\');" role="button" style="background-image:url(\'bundles/con4giscore/vendor/wswgEditor/images/spoiler.png\');"></button>'
            // +'<button title="Smilies" onclick="wswgEditor.doSmilie();" role="button" style="background-image:url(\'bundles/con4giscore/vendor/wswgEditor/images/smilies.png\');"></button>'
        + ' <span>|</span> '
        + '<button title="Switch to source-view" role="button" onclick="wswgEditor.SwitchEditor()" style="background-image:url(\'bundles/con4giscore/vendor/wswgEditor/images/icon_source.png\');"></button>';

    function rep(re, str) {
        content = content.replace(re, str);
    }

    this.parseBBCode = function (text) {
        if (text) {
            content = text;
            bbcode2html(false);
            return content;
        } else {
            return text;
        }
    }
    this.parseBBCodeIgnoreHtml = function (text) {
        if (text) {
            text = text.replace(/</gi, '{§Hlt§}').replace(/>/gi, '{§Hgt§}').replace(/&quot;/gi, '"');
            content = text;
            bbcode2html(false);
            content = content.replace(/<br>/gi, '').replace(/\{§Hlt§\}/gi, '<').replace(/\{§Hgt§\}/gi, '>');
            return content;
        } else {
            return text;
        }
    }

    this.initEditor = function (textarea_id, wysiwyg) {
        if (wysiwyg != undefined) {
            enableWysiwyg = wysiwyg;
        } else {
            enableWysiwyg = true;
        }
        body_id = textarea_id;
        textboxelement = document.getElementById(body_id);
        if (enableWysiwyg) {
            if (!document.getElementById("rte")) { //to prevent recreation
                ifm = document.createElement("iframe");
                ifm.setAttribute("id", "rte");
                ifm.setAttribute("frameBorder", "0");
                ifm.setAttribute("class", "bbc-frame");
                editBar = document.createElement("div");
                editBar.setAttribute("class", "editBar");
                textboxelement.parentNode.insertBefore(editBar, textboxelement);
                textboxelement.parentNode.insertBefore(ifm, textboxelement);
                textboxelement.style.display = 'none';
            }
            editBar.innerHTML = editBarButtons;

            if (ifm) {
                InitIframe();
            } else
                setTimeout('InitIframe()', 100);
        }
    }

    function InitIframe() {
        myeditor = ifm.contentWindow.document;
        myeditor.designMode = "on";
        myeditor.open();
        myeditor.write('<html><head><link href="bundles/con4giscore/vendor/wswgEditor/css/editor.css" rel="Stylesheet" type="text/css" /></head>');
        myeditor.write('<body style="margin:0; padding:5px;" class="editorWYSIWYG">');
        myeditor.write('</body></html>');
        myeditor.close();
        myeditor.body.contentEditable = true;
        myeditor.designMode = 'on';
        myeditor.execCommand('enableObjectResizing', false, false);
        myeditor.execCommand('enableInlineTableEditing', false, false);
        myeditor.designMode = 'off';
        ifm.contentEditable = true;
        if (myeditor.attachEvent) {
            myeditor.attachEvent("onkeypress", kp);
        }
        else if (myeditor.addEventListener) {
            myeditor.addEventListener("keypress", kp, true);
        }
        wswgEditor.ShowEditor();
    }

    this.ShowEditor = function () {
        if (!enableWysiwyg) return;
        editorVisible = true;
        content = document.getElementById(body_id).value;
        bbcode2html(true);
        myeditor.body.innerHTML = content;
    }

    this.SwitchEditor = function () {
        if (editorVisible) {
            this.doCheck();
            ifm.style.display = 'none';
            textboxelement.style.display = '';
            editorVisible = false;
            textboxelement.focus();
        }
        else {
            if (enableWysiwyg && ifm) {
                ifm.style.display = '';
                textboxelement.style.display = 'none';
                this.ShowEditor();
                editorVisible = true;
                ifm.contentWindow.focus();
            }
        }
    }

    function html2bbcode() {
        var temp;

        //remove if-blocks
        rep(/<!--\[if.*?<!\[endif\]-->/gi, '');
        // convert linebreaks
        rep(/<br[^>]*>/gi, '\n');
        rep(/<(p|div)>/gi, '\n');
        //rep(/<\/(p|div)>/gi, ''); <- not needed, since invalid tags will be removed anyway
        // convert &nbsp; to whitespaces
        rep(/&nbsp;/gi, ' ');

        // loop to find every occurance of
        //  <strong> <b> <em> <i> <u> <span class="bbcode_spoiler"> and <div style="text-align:(left|center|right|justify);">
        do {
            temp = content;

            rep(/<strong>(.*?)<\/strong>/gi, '[b]$1[/b]');
            rep(/<b>(.*?)<\/b>/gi, '[b]$1[/b]');
            rep(/<em>(.*?)<\/em>/gi, '[i]$1[/i]');
            rep(/<i>(.*?)<\/i>/gi, '[i]$1[/i]');
            rep(/<u>(.*?)<\/u>/gi, '[u]$1[/u]');

            rep(/<span\sclass="bbcode_spoiler"\s?>(.*?)<\/span>/gi, '[spoiler]$1[/spoiler]');

            rep(/<div\sstyle="\s?text-align:\s?(left|center|right);?"\s?>([\s\S]*?)<\/div>/gi, '[$1]$2[/$1]');
            rep(/<div\sstyle="\s?text-align: \s?(left|center|right);?"\s?>([\s\S]*?)<\/div>/gi, '[$1]$2[/$1]');
            rep(/<div\salign="\s?(left|center|right)"\s?>([\s\S]*?)<\/div>/gi, '[$1]$2[/$1]');
            rep(/<p\salign="\s?(left|center|right)"\s?>([\s\S]*?)<\/p>/gi, '[$1]$2[/$1]');
            rep(/<div\sstyle="\s?text-align:\s?justify;?"\s?>([\s\S]*?)<\/div>/gi, '[block]$1[/block]');
            rep(/<div\salign="\s?justify?"\s?>([\s\S]*?)<\/div>/gi, '[block]$1[/block]');
            rep(/<p\salign="\s?justify?"\s?>([\s\S]*?)<\/p>/gi, '[block]$1[/block]');

        } while (temp != content);

        // parse links
        rep(/<a\shref="(\S*?)"[^>]*?>([^<]*?)<\/a>/gi, '[url="$1"]$2[/url]');

        // loop to find every occurance of
        //   <font color="???">
        //   <img src="path">
        //   NOTE: need to be this far down, otherwise it would not be possible to write other tags in it
        do {
            temp = content;

            rep(/<font\scolor="?(#?[a-fA-F0-9]{3}|#?[a-fA-F0-9]{6}|[a-zA-Z]*)"?\s?>([^<]*?)<\/font>/gi, '[color=$1]$2[/color]');
            rep(/<span\style="background-color:?(#?[a-fA-F0-9]{3}|#?[a-fA-F0-9]{6}|[a-zA-Z]*);"?\s?>([^<]*?)<\/font>/gi, '[bgcolor=$1]$2[/bgcolor]');
            rep(/<img\ssrc="([^<]*?)"\s?>/gi, '[img]$1[/img]');
            rep(/<div\sclass="bbcode_quote"><div\sclass="bbcode_quote_head">\[b\]([^\[\]]*?)\:?\[\/b\]<\/div><div\sclass="bbcode_quote_body">([^<>]*?)<\/div><\/div>/gi, '[quote="$1"]$2[/quote]');
            rep(/<div\sclass="bbcode_quote"><div\sclass="bbcode_quote_body">([^<>]*?)<\/div><\/div>/gi, '[quote]$1[/quote]');

        } while (temp != content);

        //parse lists
        rep(/<ul>(.*?)<\/ul>/gi, '[list]$1\n[/list]');
        rep(/<ol>(.*?)<\/ol>/gi, '[list="1"]$1\n[/list]');
        rep(/<ol type="?([^">]*?)"?>(.*?)<\/ol>/gi, '[list="$1"]$2\n[/list]');
        rep(/<li\sstyle="?\s?text-align:\s?([a-zA-Z]*);?"?>(.*?)<\/li>/gi, '\n  [*=$1]$2');
        rep(/<li>(.*?)<\/li>/gi, '\n  [*]$1');


        // clean up invalid Tags
        rep(/<.*?>/gi, '');

        // parse &
        rep(/&amp;/gi, '&');

        // parse < and >
        rep(/&lt;/gi, '<');
        rep(/&gt;/gi, '>');
    }

    function bbcode2html(isEditor) {
        var temp;
        var tempConvert;
        var re;

        // ___[ restructuring ]_______________________________________________________________

        // [URL]link[URL] -> [URL=link]link[URL] (to prevent loosing any applied styles)
        re = new RegExp(/\[url\](.*?)\[\/url\]/gi);
        while (temp = re.exec(content)) {
            tempConvert = temp[1].replace(/\[.*?\]/gi, '').replace(/\s+/gi, '_');
            rep(temp[0], '[url="' + tempConvert + '"]' + temp[1] + '[/url]');
        }

        // ___________________________________________________________________________________


        // parse &
        rep(/\s&\s/gi, ' &amp; ');
        // delete linebreaks created by the forum
        rep(/<br[^>]*?>/gi, '');
        // parse < and >
        rep(/</gi, '&lt;');
        rep(/>/gi, '&gt;');
        // parse linebreaks
        rep(/\n/gi, '<br>');

        // loop to find every occurance of
        //  [b] [i] [u] [spoiler] [left] [center] [right] and [block] aka [justify]
        do {
            temp = content;

            rep(/\[b\](.*?)\[\/b\]/gi, '<strong>$1</strong>');
            rep(/\[code\](.*?)\[\/code\]/gi, '<code>$1</code>');
            rep(/\[i\](.*?)\[\/i\]/gi, '<em>$1</em>');
            rep(/\[u\](.*?)\[\/u\]/gi, '<u>$1</u>');

            if (!isEditor) { // do not parse this when editing
                rep(/\[spoiler\](.*?)\[\/spoiler\]/gi, '<span class="bbcode_spoiler">$1</span>');
            }
            rep(/\[left\](.*?)\[\/left\]/gi, '<div style="text-align:left;">$1</div>');
            rep(/\[right\](.*?)\[\/right\]/gi, '<div style="text-align:right;">$1</div>');
            rep(/\[center\](.*?)\[\/center\]/gi, '<div style="text-align:center;">$1</div>');
            rep(/\[block\](.*?)\[\/block\]/gi, '<div style="text-align:justify;">$1</div>');
            rep(/\[justify\](.*?)\[\/justify\]/gi, '<div style="text-align:justify;">$1</div>');

        } while (temp != content);

        // parse links
        rep(/\[url="?([^"]*?)"?\]([^\[]*?)\[\/url\]/gi, '<a href="$1" target="_blank">$2</a>');
        rep(/\[email\]([^\[]*?)\[\/email\]/gi, '<a href="mailto:$1" target="_blank">$1</a>');

        // loop to find every occurance of
        //   [color=#000] [color=#000000] and [color=black]
        //   [img]path[/img]
        //   [quote] and [quote="author"]
        //   NOTE: need to be this far down, otherwise it would not be possible to write other tags in it
        do {
            temp = content;

            rep(/\[color="?(#?[a-fA-F0-9]{3}|#?[a-fA-F0-9]{6}|[a-zA-Z]*)"?\]([^\[]*?)\[\/color\]/gi, '<font color="$1">$2</font>');
            rep(/\[bgcolor="?(#?[a-fA-F0-9]{3}|#?[a-fA-F0-9]{6}|[a-zA-Z]*)"?\]([^\[]*?)\[\/bgcolor\]/gi, '<span style="background-color:$1">$2</span>');
            rep(/\[img\]([^\[]*?)\[\/img\]/gi, '<img src="$1">');
            rep(/\[s\]([^\[]*?)\[\/s\]/gi, '<span style="text-decoration:line-through">$1</span>');
            rep(/\[youtube\]([^\[]*?)\[\/youtube\]/gi, '<div style="position:relative;padding-bottom:56.25%;padding-top:30px;height:0;overflow:hidden;"><div style="position: absolute;top: 0;left: 0;width: 100%;height: 100%;"><iframe frameborder="0" allowfullscreen src="$1" width="100%" height="100%"></iframe></div></div>');
            if (!isEditor) { // do not parse this when editing
                rep(/\[quote="?([^\[]*?)"?\]([^\[]*?)\[\/quote\]/gi, '<div class="bbcode_quote"><div class="bbcode_quote_head"><strong>$1:</strong></div><div class="bbcode_quote_body">$2</div></div>');
                rep(/\[quote\]([^\[]*?)\[\/quote\]/gi, '<div class="bbcode_quote"><div class="bbcode_quote_body">$1</div></div>');
            }
            ;
        } while (temp != content);

        // parse lists
        // clean up
        rep(/\[list([^\]]*?)\].*?\[\*([=a-zA-Z]*?)\]/gi, '[list$1][*$2]');
        re = new RegExp(/\[list([^\]]*?)\](.*?)\[\/list\]/gi);
        while (temp = re.exec(content)) {
            tempConvert = temp[2].replace(/<br[^>]*>/gi, '').replace(/\[\*\]([^\[]*)/gi, '<li>$1</li>').replace(/\[\*="?([a-zA-Z]*)"?\]([^\[]*)/gi, '<li style="text-align:$1;">$2</li>').replace(/\s+/gi, ' ');
            if (temp[1]) {
                rep(temp[0], '<ol type="' + temp[1].replace(/("|=)/gi, '') + '">' + tempConvert + '</ol>');
            } else {
                rep(temp[0], '<ul>' + tempConvert + '</ul>');
            }
            ;
        }

        if (!isEditor) { // do not parse this when editing
            // clean up invalid Tags
            rep(/\[.*?\]/gi, '');
        }

        // parse multiple whitespaces
        rep(/\s{2,}/gi, ' &nbsp;');

    }

    this.doCheck = function () {
        var body = document.getElementById(body_id);
        if (!enableWysiwyg || !body) return;
        if (!editorVisible) {
            this.ShowEditor();
        }
        content = myeditor.body.innerHTML;
        html2bbcode();
        body.value = content;
    }

    function stopEvent(evt) {
        evt || window.event;
        if (evt.stopPropagation) {
            evt.stopPropagation();
            evt.preventDefault();
        } else if (typeof evt.cancelBubble != "undefined") {
            evt.cancelBubble = true;
            evt.returnValue = false;
        }
        return false;
    }

    this.insertTag = function (openTag, closeTag) {
        if (editorVisible) {
            ifm.contentWindow.focus();
            if (isIE) {
                textRange = ifm.contentWindow.document.selection.createRange();
                var newTxt = openTag + textRange.text + closeTag;
                textRange.text = newTxt;
            }
            else {
                var edittext = ifm.contentWindow.getSelection().getRangeAt(0);
                var original = edittext.toString();
                edittext.deleteContents();
                edittext.insertNode(ifm.contentWindow.document.createTextNode(openTag + original + closeTag));
            }
        }
        else {
            AddTag(openTag, closeTag);
        }
    }

    function kp(e) {
        if (isIE) {
            if (e.keyCode == 13) {
                var r = myeditor.selection.createRange();
                if (r.parentElement().tagName.toLowerCase() != "li") {
                    r.pasteHTML('<br/>');
                    if (r.move('character'))
                        r.move('character', -1);
                    r.select();
                    stopEvent(e);
                    return false;
                }
            }
        }
    }

    this.InsertYoutube = function () {
        this.InsertText(" http://www.youtube.com/watch?v=XXXXXXXXXXX ");
    }
    this.InsertText = function (txt) {
        if (editorVisible)
            insertHtml(txt);
        else
            textboxelement.value += txt;
    }

    this.doClick = function (command) {
        if (editorVisible) {
            ifm.contentWindow.focus();
            myeditor.execCommand(command, false, null);
        }
        else {
            switch (command) {
                case 'bold':
                    AddTag('[b]', '[/b]');
                    break;
                case 'italic':
                    AddTag('[i]', '[/i]');
                    break;
                case 'underline':
                    AddTag('[u]', '[/u]');
                    break;
                case 'justifyLeft':
                    AddTag('[align=left]', '[/align]');
                    break;
                case 'justifyCenter':
                    AddTag('[align=center]', '[/align]');
                    break;
                case 'justifyRight':
                    AddTag('[align=right]', '[/align]');
                    break;
                case 'justifyFull':
                    AddTag('[align=justify]', '[/align]');
                    break;
                case 'InsertUnorderedList':
                    AddTag('[ul][li]', '[/li][/ul]');
                    break;
                case 'InsertOrderedList':
                    AddTag('[ol][li]', '[/li][/ol]');
                    break;
            }
        }
    }

    function doColor(color) {
        ifm.contentWindow.focus();
        if (isIE) {
            textRange = ifm.contentWindow.document.selection.createRange();
            textRange.select();
        }
        myeditor.execCommand('forecolor', false, color);
    }

    this.doLink = function () {
        if (editorVisible) {
            ifm.contentWindow.focus();
            var mylink = prompt("Enter a URL:", "http://");
            if ((mylink != null) && (mylink != "")) {
                if (isIE) { //IE
                    var range = ifm.contentWindow.document.selection.createRange();
                    if (range.text == '') {
                        range.pasteHTML("<a href='" + mylink + "'>" + mylink + "</a>");
                    }
                    else
                        myeditor.execCommand("CreateLink", false, mylink);
                }
                else if (window.getSelection) { //FF
                    var userSelection = ifm.contentWindow.getSelection().getRangeAt(0);
                    if (userSelection.toString().length == 0)
                        myeditor.execCommand('inserthtml', false, "<a href='" + mylink + "'>" + mylink + "</a>");
                    else
                        myeditor.execCommand("CreateLink", false, mylink);
                }
                else
                    myeditor.execCommand("CreateLink", false, mylink);
            }
        }
        else {
            AddTag('[url=', ']click here[/url]');
        }
    }
    this.doImage = function () {
        if (editorVisible) {
            if (document.getElementById('smilieDiv')) {
                wswgEditor.remove('smilieDiv');
            }

            if (!document.getElementById('imgDiv')) {

                var imgPath = jQuery('input[name="uploadPath"]').val();

                ifm.contentWindow.focus();

                var imgDiv = document.createElement("div");
                imgDiv.setAttribute("class", "ui-widget-header chooseImage");
                imgDiv.setAttribute("id", "imgDiv");
                ifm.parentNode.insertBefore(imgDiv, ifm);
                imgDiv.innerHTML = '<input type="file" class="wswgInput" name="fileElem[]" id="fileElem" multiple="false" accept="image/*" onchange="handleFile(this.files)" style="display:none;">' +
                '<div id="progress" class="progress">' +
                '<div class="progress-bar progress-bar-success"></div>' +
                '</div>' +
                '<button type="submit" name="submit" class="wswgButton" onClick="wswgEditor.insertImage( prompt(\'URL:\', \'http://\'), false );">URL</button>' +
                '<button type="submit" name="submit" class="wswgButton" onClick="document.getElementById(\'fileElem\').click();">FILE</button><br>' +
                '<div id="preview"><img src="bundles/con4giscore/vendor/wswgEditor/images/imgPrev.png"></div>' +
                '<center><button id="uploadFile" class="wswgButton ok" onClick="sendFile( \'' + imgPath + '\' );"><b>✓</b> OK</button>' +
                '<button onClick="wswgEditor.remove(\'imgDiv\');" class="wswgButton cancel"><b>X</b> Cancel</button></center>';

            } else {
                wswgEditor.remove('imgDiv');
            }
        }
        else {
            AddTag('[img]', '[/img]');
        }
    }
    this.insertImage = function (img, prefix) {
        if (prefix) {
            img = jQuery('input[name="uploadEnv"]').val() + img;
        }

        if (img != 0) {
            myeditor.execCommand('InsertImage', false, img);
            this.remove('imgDiv');
        } else {
            var previewDiv = document.getElementById('preview');
            previewDiv.innerHTML = "ERROR";
        }
    }

    // this.doSmilie = function () {
    //  var asTag = true;
    //  if (editorVisible) {
    //      if (document.getElementById( 'imgDiv' )) { wswgEditor.remove('imgDiv'); }
    //      asTag = false;
    //  }

    //  if (!document.getElementById( 'smilieDiv' )) {

    //      // var smiliePath = jQuery('input[name="smiliePath"]').val();
    //      var envPath = jQuery('input[name="uploadEnv"]').val();
    //      var smiliePath = envPath + 'bundles/con4giscore/vendor/wswgEditor/images/smilies/';

    //      ifm.contentWindow.focus();

    //      var smilieDiv = document.createElement("div");
    //      smilieDiv.setAttribute("class", "ui-widget-header chooseSmilie");
    //      smilieDiv.setAttribute("id", "smilieDiv");
    //      ifm.parentNode.insertBefore(smilieDiv, ifm);
    //      smilieDiv.innerHTML = '<button type="submit" name="submit" class="wswgButton" onClick="wswgEditor.insertSmilie( \'' + smiliePath + 'c4g_smile.png\', ' + asTag + ' );"><img src="' + smiliePath + 'c4g_smile.png"></button>' +
    //                          '<button type="submit" name="submit" class="wswgButton" onClick="document.getElementById(\'fileElem\').click();">FILE</button><br>' +
    //                          '<div id="preview"><img src="bundles/con4giscore/vendor/wswgEditor/images/imgPrev.png"></div>'+
    //                          '<center><button id="uploadFile" class="wswgButton ok" onClick="sendFile( \'' + smiliePath + '\' );"><b>✓</b> OK</button>'+
    //                          '<button onClick="wswgEditor.remove(\'smilieDiv\');" class="wswgButton cancel"><b>X</b> Cancel</button></center>';

    //  } else {
    //      wswgEditor.remove('smilieDiv');
    //  }

    // }
    // this.insertSmilie = function( smiliepath, asTag )
    // {
    //  if ( smiliepath != 0 ){
    //      myeditor.execCommand('InsertImage', false, smiliepath);
    //      this.remove('smilieDiv');
    //  } else {
    //      var previewDiv = document.getElementById('preview');
    //      previewDiv.innerHTML = "ERROR";
    //  }
    // }

    this.remove = function (eId) {
        var element = document.getElementById(eId);
        element.parentNode.removeChild(element);
        return false;
    }

    function insertHtml(html) {
        ifm.contentWindow.focus();
        if (isIE)
            ifm.contentWindow.document.selection.createRange().pasteHTML(html);
        else
            myeditor.execCommand('inserthtml', false, html);
    }

    //textarea-mode functions
    function MozillaInsertText(element, text, pos) {
        element.value = element.value.slice(0, pos) + text + element.value.slice(pos);
    }

    function AddTag(t1, t2) {
        var element = textboxelement;
        if (isIE) {
            if (document.selection) {
                element.focus();

                var txt = element.value;
                var str = document.selection.createRange();

                if (str.text == "") {
                    str.text = t1 + t2;
                }
                else if (txt.indexOf(str.text) >= 0) {
                    str.text = t1 + str.text + t2;
                }
                else {
                    element.value = txt + t1 + t2;
                }
                str.select();
            }
        }
        else if (typeof (element.selectionStart) != 'undefined') {
            var sel_start = element.selectionStart;
            var sel_end = element.selectionEnd;
            MozillaInsertText(element, t1, sel_start);
            MozillaInsertText(element, t2, sel_end + t1.length);
            element.selectionStart = sel_start;
            element.selectionEnd = sel_end + t1.length + t2.length;
            element.focus();
        }
        else {
            element.value = element.value + t1 + t2;
        }
    }

    //=======color picker
    function getScrollY() {
        var scrOfX = 0,
            scrOfY = 0;

        if (typeof (window.pageYOffset) == 'number') {
            scrOfY = window.pageYOffset;
            scrOfX = window.pageXOffset;
        } else if (document.body && (document.body.scrollLeft || document.body.scrollTop)) {
            scrOfY = document.body.scrollTop;
            scrOfX = document.body.scrollLeft;
        } else if (document.documentElement && (document.documentElement.scrollLeft || document.documentElement.scrollTop)) {
            scrOfY = document.documentElement.scrollTop;
            scrOfX = document.documentElement.scrollLeft;
        }
        return scrOfY;
    }

    function getTop2() {
        csBrHt = 0;
        if (typeof (window.innerWidth) == 'number') {
            csBrHt = window.innerHeight;
        } else if (document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
            csBrHt = document.documentElement.clientHeight;
        } else if (document.body && (document.body.clientWidth || document.body.clientHeight)) {
            csBrHt = document.body.clientHeight;
        }
        ctop = ((csBrHt / 2) - 115) + getScrollY();
        return ctop;
    }

    var nocol1 = "&#78;&#79;&#32;&#67;&#79;&#76;&#79;&#82;",
        clos1 = "X";

    function getLeft2() {
        var csBrWt = 0;
        if (typeof (window.innerWidth) == 'number') {
            csBrWt = window.innerWidth;
        } else if (document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
            csBrWt = document.documentElement.clientWidth;
        } else if (document.body && (document.body.clientWidth || document.body.clientHeight)) {
            csBrWt = document.body.clientWidth;
        }

        cleft = (csBrWt / 2) - 125;
        return cleft;
    }

    //function setCCbldID2(val, textBoxID) { document.getElementById(textBoxID).value = val; }
    function setCCbldID2(val) {
        if (editorVisible)
            doColor(val);
        else
            AddTag('[color=#' + val + ']', '[/color]');
    }

    function setCCbldSty2(objID, prop, val) {
        switch (prop) {
            case "bc":
                if (objID != 'none') {
                    document.getElementById(objID).style.backgroundColor = val;
                }
                ;
                break;
            case "vs":
                document.getElementById(objID).style.visibility = val;
                break;
            case "ds":
                document.getElementById(objID).style.display = val;
                break;
            case "tp":
                document.getElementById(objID).style.top = val;
                break;
            case "lf":
                document.getElementById(objID).style.left = val;
                break;
        }
    }

    this.putOBJxColor2 = function (Samp, pigMent, textBoxId) {
        if (pigMent != 'x') {
            setCCbldID2(pigMent, textBoxId);
            setCCbldSty2(Samp, 'bc', pigMent);
        }
        setCCbldSty2('wswgColorPicker', 'vs', 'hidden');
        setCCbldSty2('wswgColorPicker', 'ds', 'none');
    }

    this.showColorGrid2 = function (Sam, textBoxId) {
        var objX = new Array('00', '33', '66', '99', 'CC', 'FF');
        var c = 0;
        var xl = '"' + Sam + '","x", "' + textBoxId + '"';
        var mid = '';
        mid += '<table bgcolor="#FFFFFF" border="0" cellpadding="0" cellspacing="0"><tr>';
        mid += "<td colspan='9' align='left' style='margin:0;padding:2px;height:12px;' ><input class='wswgCpDisplay' type='text' size='12' id='wswgCpDisplay' value='#FFFFFF'><input class='wswgCpDisplay2' disabled='true' style='width:14px; background-color:white;' id='wswgCpDisplay2'></td><td colspan='9' align='right'><a class='wswgCpDisplay' href='javascript:onclick=wswgEditor.putOBJxColor2(" + xl + ")'><span class='a01p3'>" + clos1 + "</span></a></td></tr><tr>";
        var br = 1;
        for (o = 0; o < 6; o++) {
            mid += '</tr><tr>';
            for (y = 0; y < 6; y++) {
                if (y == 3) {
                    mid += '</tr><tr>';
                }
                for (x = 0; x < 6; x++) {
                    var grid = '';
                    grid = objX[o] + objX[y] + objX[x];
                    var b = "'" + Sam + "','" + grid + "', '" + textBoxId + "'";
                    mid += '<td class="o5582brd" style="background-color:#' + grid + '"><a class="wswgCpColor"  href="javascript:onclick=wswgEditor.putOBJxColor2(' + b + ');" onmouseover=javascript:document.getElementById("wswgCpDisplay").value="#' + grid + '";javascript:document.getElementById("wswgCpDisplay2").style.backgroundColor="#' + grid + '";  title="#' + grid + '"><div style="width:12px;height:14px;"></div></a></td>';
                    c++;
                }
            }
        }
        mid += "</tr></table>";
        document.getElementById('wswgColorPicker').innerHTML = mid;
        setCCbldSty2('wswgColorPicker', 'vs', 'visible');
        setCCbldSty2('wswgColorPicker', 'ds', 'inline');
    }

}
