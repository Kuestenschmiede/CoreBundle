
/**
 * con4gis - the gis-kit
 *
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */

this.c4g = this.c4g || {};
this.c4g.projects = this.c4g.projects || {};

// use local namespace with single execution function
(function(jQuery, c4g) {

  // id for generated DIVs
  var
    nextId = 1;


  // Extend jQuery, so c4gGui can be used with jQuery(...).c4gGui(...)
  c4g.projects.c4gGui = function(options) {
    options = jQuery.extend({
      ajaxUrlPrefix: 'ajax.php',
      navPanel: true,
      jquiBreadcrumb: true,
      jquiButtons: true,
      embedDialogs: false,
      jquiEmbeddedDialogs: true,
      wswgEditor: false,
      breadcrumbDelim: '',
      contaoPath: '',
      contaoLanguage: '',
      width: '',
      height: '',
      mainDiv: ''
    }, options);
    this.options = options;
    this.options.moduleId = options.id;
    this.buttonDiv = null;
    this.pushingState = false;
    this.mainDiv = options.mainDiv;
    var scope = this;

    // -----------------------------------
    // jQuery.fn.c4gGui()
    // -----------------------------------
    jQuery(window).resize(function(){
      jQuery('.c4gGuiCenterDiv').each(function(i,element){
        scope.fnCenterDiv(element);
      });
      // scope.fnDataTableColumnVis(oDataTable);
      if (oDataTable) {
        oDataTable.columns.adjust().draw();
      }
    });

    var oDataTable = null;  // TODO enable more than one
  };


  jQuery.extend(c4g.projects.c4gGui.prototype, {
    setup: function () {
      var scope = this;
      return this.mainDiv.each(function() {
        var options = scope.options;
        // if no ID is provided then initialize internal ID for DIVs
        if (typeof(options.id) ==='undefined') {
          options.id = nextId;
        }

        if (options.jquiBreadcrumb || options.jquiButtons || options.jquiEmbeddedDialogs) {
          if (typeof jQuery.ui === 'undefined') {
            jQuery(this).html('jQuery UI missing!');
            return;
          }
        }

        // set height and width if provided
        if (options.height !== '') {
          jQuery(this).height(options.height);
        }
        if (options.width !== '') {
          jQuery(this).width(options.width);
        }

        // add c4gGui class
        jQuery(this).attr('class', function(i, val) {
          if (typeof(val) === 'undefined') {
            return 'c4gGui';
          } else {
            return val + ' c4gGui';
          }
        });

        if (typeof(options.title) !== 'undefined') {
          jQuery('<h1 id="c4gGuiTitle">'+options.title+'</h1>').appendTo(jQuery(this));
        }
        jQuery('<h3 id="c4gGuiSubtitle"> </h3>').appendTo(jQuery(this));



        // add Breadcrumb Area
        jQuery('<div />')
          .attr('id', 'c4gGuiBreadcrumb'+options.id)
          .attr('class', 'c4gGuiBreadcrumb')
          .appendTo(jQuery(this));

        // add Headline Area
        jQuery('<div />')
          .attr('id', 'c4gGuiHeadline'+options.id)
          .attr('class', 'c4gGuiHeadline')
          .appendTo(jQuery(this));

        // add Buttons Area
        scope.buttonDiv = jQuery('<div />')
          .attr('id', 'c4gGuiButtons'+options.id)
          .attr('class', 'c4gGuiButtons')
          .appendTo(jQuery(this));
        jQuery(scope.buttonDiv).hide();

        // add navigation
        if (options.navPanel) {
          jQuery('<div />')
            .attr('id','c4gGuiNavigation'+options.id)
            .attr('class','c4gGuiNavigation')
            .appendTo(jQuery(this));
        }

        // create DIV for ajax Message
        jQuery(this).append('<div class="c4gLoaderPh"></div>');
        jQuery(document).ajaxStart(function(){
          jQuery('.c4gGui,.c4gGuiDialog').addClass('c4gGuiAjaxBusy');
          jQuery('.c4gLoaderPh').addClass('c4gLoader');
        });
        jQuery(document).ajaxStop(function(){
          jQuery('.c4gGui,.c4gGuiDialog').removeClass('c4gGuiAjaxBusy');
          jQuery('.c4gLoaderPh').removeClass('c4gLoader');
        });

        // create DIV for content
        jQuery('<div />')
          .attr('id','c4gGuiContent'+options.id)
          .attr('class','c4gGuiContent')
          .appendTo(
            jQuery('<div />')
              .attr('id','c4gGuiContentWrapper'+options.id)
              .attr('class','c4gGuiContentWrapper')
              .appendTo(this));

        // create DIV for dialogs
        jQuery('<div />')
          .attr('id','c4gGuiDialog'+options.id)
          .attr('class','c4gGuiDialog')
          .appendTo(this);

        if (options.initData) {
          // initialization data exists
          var initData = {};
          initData.content = options.initData;
          scope.fnHandleAjaxResponse( initData, options.id );
        } else {
          // request initialization from server
          jQuery.ajax({
            internalId: options.id,
            url: options.ajaxUrl,
            data: options.ajaxData+'/initnav',
            dataType: "json",
            type: "GET"
          }).done(function(data) {
            scope.fnHandleAjaxResponse( data, scope.internalId );
          }).fail(function(data) {
            scope.fnInitContentDiv();
            jQuery(this.contentDiv).text('Error1: could not update history');
          });
        }
        if (history != null) {
          var internalId = options.id;
          if (History && History.Adapter) {
            History.Adapter.bind(window, 'statechange', function() {
              if (!scope.pushingState) {
                var State = History.getState();
                if (options.ajaxUrl !== 'undefined' && typeof options.ajaxUrl !== 'undefined') {
                  jQuery.ajax({
                    internalId: internalId,
                    url: options.ajaxUrl + '/' + options.ajaxData,
                    data: 'historyreq='+decodeURI(
                        (RegExp('state=(.+?)(&|$)').exec(State.url)||[,null])[1]
                    ),
                    dataType: "json",
                    type: "GET"
                  }).done(function(data) {
                    scope.fnHandleAjaxResponse( data, this.internalId );
                  }).fail(function(data) {
                    jQuery(scope.contentDiv).text('Error2: could not update history');
                  });
                }
              }
            });
          }
        }

        // set next Id in case there is more than one element to be set
        options.id++;
        nextId = options.id;
      });
    }, // end of setup

    handlePdfResponse: function(data, id) {
      // var blob = new Blob([data], {type:"application/pdf"});
      // //Create a link element, hide it, direct
      // //it towards the blob, and then 'click' it programatically
      let a = document.createElement("a");
      // a.style = "display: none";
      document.body.appendChild(a);
      // //Create a DOMString representing the blob
      // //and point the link element towards it
      // let url = window.URL.createObjectURL(blob);
      a.href = data.filePath;
      a.download = data.fileName;
      //programatically click the link to trigger the download
      a.click();
      //release the reference to the file by revoking the Object URL
      // window.URL.revokeObjectURL(url);
    },

    // -----------------------------------
    // handle Ajax response
    // -----------------------------------
    fnHandleAjaxResponse: function (data, internalId) {
      // TODO: Request Token Handling
      var navDiv = '#c4gGuiNavigation' + internalId;
      this.contentWrapperDiv = '#c4gGuiContentWrapper' + internalId;
      this.contentDiv = '#c4gGuiContent' + internalId;
      var dialogDiv = '#c4gGuiDialog' + internalId;
      var breadcrumbDiv = '#c4gGuiBreadcrumb' + internalId;
      var headlineDiv = '#c4gGuiHeadline' + internalId;
      var scope = this;
      var options = this.options;
      var oDataTable;

      var fnExecAjax = function (ajaxMethod, ajaxUrl, ajaxData) {
        if (ajaxUrl.indexOf("?") <= 0) {
          ajaxUrl += "?id=0";
        } else {
          ajaxUrl += "&id=0";
        }
        jQuery.ajax({
          internalId: internalId,
          url: ajaxUrl,
          data: ajaxData,
          dataType: "json",
          type: ajaxMethod
        }).done(function(data) {
          if (data.filePath && data.fileName) {
            // check if it is a pdf response
            scope.handlePdfResponse(data, this.internalId);
          } else {
            scope.fnHandleAjaxResponse(data, this.internalId);
          }
        }).fail(function(data) {
          scope.fnInitContentDiv();
          jQuery(scope.contentDiv).text('Error3: ' + data);
        });
      };

      var fnExecAjaxGet = function (ajaxData) {
        fnExecAjax("GET", options.ajaxUrl + '/' + ajaxData, null);
      };

      var fnExecAjaxPut = function (ajaxUrl, ajaxData) {
        fnExecAjax("PUT", ajaxUrl, ajaxData);
      };

      var fnJumpToLink = function (linkUrl, newwindow) {
        if (navigator.appName === 'Microsoft Internet Explorer') {
          if ((linkUrl.indexOf(':') < 0) && (linkUrl[0] !== '/')) {
            linkUrl = linkUrl.replace('index.php/', "");
          }
        }
        if (newwindow) {
          window.open(linkUrl, 'Window');
        } else {
          window.location = linkUrl;
        }
      };

      var fnAddTooltip = function (element) {
        if (typeof(jQuery.fn.tooltip) === 'function') {
          element
            .find('.c4gGuiTooltip')
            .tooltip({
              track: false,
              delay: 0,
              extraClass: "c4gGuiTooltipComponent ui-corner-all ui-widget-content",
              showURL: true
            });

        }
      };

      var fnAddScrollpane = function (element) {
        //TODO add Switch
        //TODO disable autoReinitialise and do it manually everytime the dialog resizes
        if (typeof(jQuery.fn.jScrollPane) === 'function') {
          element
            .jScrollPane({autoReinitialise: true, autoReinitialiseDelay: 100});
        }
      };

      var fnAddAccordion = function (element) {
        if (typeof(jQuery.fn.accordion) === 'function') {
          element
            .find('.c4gGuiAccordion')
            .accordion({active: false, autoHeight: false, collapsible: true});
        }
      };

      var fnMakeCollapsible = function (element) {
        element
          .find('.c4gGuiCollapsible_hide')
          .slideUp(0);
        element
          .find('.c4gGuiCollapsible_trigger')
          .click(function () {
            jQuery(this).children('.c4gGuiCollapsible_trigger_target').slideToggle(250);
            jQuery(this).nextUntil('.c4gGuiCollapsible_trigger', '.c4gGuiCollapsible_target').slideToggle(250); //.addClass('sub_active');
          });
      };

      var fnEnableWswgEditor = function () {
        if (typeof(wswgEditor) !== 'undefined') {
          if (jQuery('#editor').length > 0) {
            wswgEditor.initEditor('editor');
          }

          if (jQuery('#ckeditor').length > 0) {
            if (typeof ckEditorItems === "undefined" || ckEditorItems === "" || ckEditorItems.length <= 1) {
              ckEditorItems = ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo', 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'Blockquote', '-', 'RemoveFormat', 'NumberedList', 'BulletedList', 'Link', 'Unlink', 'Anchor', 'Image', 'FileUpload', 'Smiley', 'TextColor', 'BGColor'];
            }

            if (jQuery.browser.mozilla) {
              CKEDITOR.on('instanceReady', function (event) {
                event.editor.on('mode', function (ev) {
                  if (ev.editor.mode === 'wysiwyg') {
                    // gets executed everytime the editor switches from source -> WYSIWYG
                    document.designMode = 'on';
                    document.execCommand('enableObjectResizing', false, false);
                    document.execCommand('enableInlineTableEditing', false, false);
                    document.designMode = 'off';
                  }
                });

                // this gets executed on init
                document.designMode = 'on';
                document.execCommand('enableObjectResizing', false, false);
                document.execCommand('enableInlineTableEditing', false, false);
                document.designMode = 'off';
              });
            }

            try {
              if (CKEDITOR.instances['ckeditor']) {
                CKEDITOR.instances['ckeditor'].destroy(true);
              }
            } catch (e) {
            }
            setTimeout(function () {
              CKEDITOR.replace('ckeditor', {
                toolbar: [{
                  name: 'all', items: ckEditorItems
                }],
                removePlugins: ckRemovePlugins || '',
                language: options.contaoLanguage,
                defaultLanguage: "en",
                disableObjectResizing: true,
                filebrowserImageUploadUrl: options.contaoPath + "con4gis/upload/image",
                filebrowserUploadUrl: options.contaoPath + uploadApiUrl
              });
            }, 500);

          }
          jQuery('.BBCode-Area').each(function () {
            jQuery(this).html(wswgEditor.parseBBCode(jQuery(this).html()));
          });

        }
      };

      var fnAddButton = function (element) {
        if (typeof(jQuery.fn.button) === 'function') {
          element
            .find('.c4gGuiButton')
            .button();
        }
      };

      var fnDialogClose = function (element) {
        if (options.embedDialogs) {
          jQuery(element).hide();
          var state =
            jQuery(element).prev().show()
              .attr('data-state');
          if ((typeof(state) === "undefined") || (state === "")) {
            state = jQuery(scope.contentDiv).attr('data-state');
          }
          if (jQuery(element).prev().hasClass('c4gGuiContent')) {
            jQuery(scope.buttonDiv).show();
          }

          if ((state !== "") && (history != null)) {
            scope.fnHistoryPush(state);
          }
          jQuery(element).remove();
        } else {
          jQuery(element).parent().find(".ui-dialog-titlebar-close").trigger('click');
        }

        //ToDo test
        window.scrollTo(0, 0);
      };

      var content;
      if (data && data.content) {
        content = data.content;
      } else {
        content = data;
      }
      if (content && typeof(content) !== 'object') {
        content = jQuery.parseJSON(content);
      }
      if (content == null) {
        return;
      }

      if (content.initAction) {
        // execute the action initially
        // store the id, since scope.internalId gets undefined in the done callback
        var keepId = options.id;
        jQuery.ajax({
          internalId: options.id,
          url: options.ajaxUrl + "/" + options.id + "/" + content.initAction,
          dataType: "json",
          type: "GET"
        }).done(function(data) {
          scope.fnHandleAjaxResponse( data, keepId );
        }).fail(function(data) {
          scope.fnInitContentDiv();
          jQuery(this.contentDiv).text('Error1: '+data);
        });
      }

      // check for pdf
      if (content.pdfPath) {
        var pdfPath = content.pdfPath;
        var file = new File([], pdfPath);

        var link = document.createElement('a');
        link.href = pdfPath;
        link.setAttribute('download', "");
        link.dispatchEvent(new MouseEvent('click'));
      }

      if (typeof(content.title) !== "undefined") {
        jQuery('#c4gGuiTitle').html(content.title);
      }

      if (typeof(content.subtitle) !== "undefined") {
        jQuery('#c4gGuiSubtitle').html(content.subtitle);
      } else {
        if (typeof(content.title) !== "undefined") {
          jQuery('#c4gGuiSubtitle').html("");
        }
      }

      var currentState = "";

      if (history != null) {
        if (typeof(content.state) !== 'undefined') {
          currentState = content.state;
        }
        if (typeof(content.dialogstate) !== 'undefined') {
          currentState = content.dialogstate;
        }
        if (currentState !== "") {
          this.fnHistoryPush(currentState);
        }
      }

      // Set headline
      if (typeof(content.headline) !== 'undefined') {
        jQuery(headlineDiv).html(content.headline);
      }

      // create breadcrumb
      if (jQuery.isArray(content.breadcrumb)) {
        jQuery(breadcrumbDiv).empty();
        jQuery.each(content.breadcrumb, function (index, value) {
          if (index > 0) {
            if (options.breadcrumbDelim !== '') {
              jQuery(breadcrumbDiv).append('<span class="c4gGuiBreadcrumbDelim">' + options.breadcrumbDelim + '</span>');
            }

          }
          var aButton = jQuery("<a />")
            .attr('href', '#')
            .attr('class', 'c4gGuiBreadcrumbItem')
            .html(value['text'])
            .click(function (e) {
              e.preventDefault();
              e.stopPropagation();
              if (jQuery(e.currentTarget).attr("disabled") !== "disabled") {
                if (value['url']) {
                  fnJumpToLink(value['url']);
                } else {
                  if (value['id']) {
                    fnExecAjaxGet(options.ajaxData + '/' + value['id']);
                  }
                }
              }
              return false;
            })
            .appendTo(breadcrumbDiv);
          if (options.jquiBreadcrumb) {
            aButton.button();
          }
        });

        jQuery(".c4gGuiBreadcrumbItem").last().attr("disabled", "disabled").addClass("ui-state-hover").on("mouseleave", function (e) {
          jQuery(e.currentTarget).addClass("ui-state-hover");
        }).on("mousedown", function (e) {
          jQuery(e.currentTarget).removeClass("ui-state-active");
        });

        if (content !== data) {
          jQuery(".c4gGuiBreadcrumbItem").last().attr("disabled", false);
        }

      } else {
        jQuery(".c4gGuiBreadcrumbItem").last().attr("disabled", false);
      }

      // create buttons
      if (jQuery.isArray(content.buttons)) {
        jQuery(scope.buttonDiv).empty();
        jQuery(scope.buttonDiv).hide();
        jQuery.each(content.buttons, function (index, value) {
          var aButton = jQuery("<a />")
            .attr('href', '#')
            .attr('accesskey', value['accesskey'])
            .html(value['text'])
            .click(function () {
              if (value['tableSelection']) {
                if ((typeof(oDataTable) !== 'undefined') && (oDataTable != null)) {
                  var formdata = {};
                  oDataTable.jQuery('tr.row_selected').each(function (index, value) {
                    formdata['action' + index] = value.attributes['data-action'].value;
                  });
                  fnExecAjaxPut(
                    options.ajaxUrl + '/' + options.ajaxData + '/' + value['id'],
                    formdata);
                }
                return false;

              }
              fnExecAjaxGet(options.ajaxData + '/' + value['id']);
              return false;
            })
            .appendTo(scope.buttonDiv);
          if (options.jquiButtons) {
            aButton.button();
          }
          jQuery(scope.buttonDiv).show();
        });
      }


      if (typeof(content.treedata) !== 'undefined') {
        // populate tree with data when treedata is available

        jQuery(navDiv).empty();

        if (typeof (jQuery.fn.dynatree) === 'undefined') {
          jQuery(navDiv).html('<h1>jQuery.dynatree missing</h1>');
        } else {

          var treeDiv = jQuery('<div />')
            .attr('id', 'c4gGuiDynatree' + internalId)
            .attr('class', 'c4gGuiTree')
            .attr('width', jQuery(navDiv).width())
            .attr('height', jQuery(navDiv).height())
            .appendTo(jQuery(navDiv));

          // TODO: Error handling
          var treedata = jQuery.extend(
            {
              onActivate: function (node) {
                if ((typeof(node.data.href) !== 'undefined') && (node.data.href !== '') && (node.data.href !== null)) {
                  if (node.data.href_newwindow) {
                    fnJumpToLink(node.data.href, true);
                  }
                  else {
                    fnJumpToLink(node.data.href);
                  }

                }
                else {
                  fnExecAjaxGet(options.ajaxData + '/' + node.data.key);
                }
              },
              onFocus: function (node) {
                // Auto-activate focused node after 2 seconds
                node.scheduleAction("activate", 2000);
              }
            }, content.treedata
          );
          jQuery(treeDiv).dynatree(treedata);
          jQuery(navDiv).resizable("destroy");
          jQuery(navDiv + " .dynatree-container").width(jQuery(navDiv).width() - 8);
          jQuery(navDiv).resizable({
            //animate: true,
            alsoResize: navDiv + " .dynatree-container, " + navDiv + " .c4gGuiTree",
            resize: function (event, ui) {
              var newWidth = jQuery(scope.contentWrapperDiv).parent().width() - ui.size.width - 5;
              jQuery(scope.contentWrapperDiv).width(newWidth);
              jQuery(scope.contentWrapperDiv).height(ui.size.height);
            },
            stop: function () {
              if ((typeof(oDataTable) !== 'undefined') && (oDataTable != null)) {
                oDataTable.fnDraw(true);
              }
            }
          });
          fnAddTooltip(jQuery(navDiv));
        }
      }

      // TODO: Error handling
      if ((typeof(content.contentdata) !== 'undefined') || (jQuery.isArray(content.contents))) {
        // populate dataTable
        scope.fnInitContentDiv();

        var newWidth = '100%';
        var newHeight = '100%';
        if (options.navPanel) {
          newWidth = jQuery(scope.contentWrapperDiv).parent().width()
            - jQuery(navDiv).width() - 5;
          newHeight = jQuery(navDiv).height();
        }
        jQuery(scope.contentWrapperDiv).width(newWidth);
        jQuery(scope.contentWrapperDiv).height(newHeight);
        if (typeof (content.state) !== 'undefined') {
          jQuery(scope.contentDiv).attr('data-state', content.state);
        }


        var fnAddContent = function (content) {
          var contenttype = content.contenttype;
          var contentoptions = content.contentoptions;
          var contentdata = content.contentdata;
          if ((contenttype === 'datatable')
            && (typeof (contentdata) !== 'undefined')) {

            if (typeof (jQuery.fn.dataTable) === 'undefined') {
              jQuery(scope.contentDiv).html('<h1>jQuery.dataTable missing</h1>');
            } else {
              var tableDiv = jQuery('<table />')
                .attr('id', 'c4gGuiDataTable:' + content.state)
                // .attr('id','c4gGuiDataTable'+internalId)
                .attr('cellpadding', '0')
                .attr('cellspacing', '0')
                .attr('border', '0')
                .attr('class', 'display c4gGuiDataTable')
                .appendTo(scope.contentDiv);

              var actioncol = -1;
              var selectrow = -1;
              var tooltipcol = -1;
              var selectOnHover = false;
              var clickAction = false;
              var multiSelect = false;


              if (typeof(contentoptions) !== 'undefined') {
                if (typeof(contentoptions.actioncol) !== 'undefined') {
                  actioncol = contentoptions.actioncol;
                }
                if (typeof(contentoptions.selectrow) !== 'undefined') {
                  selectrow = contentoptions.selectrow;
                }
                if (typeof(contentoptions.tooltipcol) !== 'undefined') {
                  tooltipcol = contentoptions.tooltipcol;
                }
                if (typeof(contentoptions.selectOnHover) !== 'undefined') {
                  selectOnHover = contentoptions.selectOnHover;
                }
                if (typeof(contentoptions.clickAction) !== 'undefined') {
                  clickAction = contentoptions.clickAction;
                }
                if (typeof(contentoptions.multiSelect) !== 'undefined') {
                  multiSelect = contentoptions.multiSelect;
                }
              }
              contentdata = jQuery.extend({
                "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                  if (actioncol !== -1) {
                    jQuery(nRow).attr('data-action', aData[actioncol]);
                  }
                  if (selectrow !== -1) {
                    if (iDisplayIndex === selectrow) {
                      jQuery(nRow).addClass('selected');
                      jQuery(".dataTables_scrollBody").scrollTo(nRow);
                    }
                  }
                  if ((tooltipcol !== -1) && (typeof(jQuery.fn.tooltip) === 'function')) {
                    if (aData[tooltipcol]) {
                      jQuery(nRow).attr('data-tooltip', aData[tooltipcol]);
                      jQuery(nRow).tooltip({
                          bodyHandler: function () {
                            return jQuery(nRow).attr('data-tooltip');
                          },
                          extraClass: "c4gGuiTooltipComponent c4gGuiTooltipInTable"
                        }
                      );
                    }
                  }
                  return nRow;
                },
                //"lengthMenu": [ [25, 50, "-1"], [25, 50, "All"] ],
                "footerCallback": function (row, data, start, end, display) {
                  var api = this.api();
                  if (jQuery('.c4g_sumfoot').length > 0) {
                    jQuery('.c4g_sumfoot').remove();
                  }

                  if (jQuery('.c4g_sum').length > 0) {
                    jQuery(this).append('<tfoot class="c4g_sumfoot"><tr role="row" class="c4g_sumrow ui-state-highlight"></tr></tfoot>');
                    api.columns('.c4g_brick_col', {page: 'current'}).every(function () {
                      if (jQuery(this.header()).css("display") !== "none") {
                        if (this.header().className.indexOf('c4g_sum')) {
                          var sum = api
                            .cells(null, this.index())
                            .data()
                            .reduce(function (a, b) {
                              a += "";
                              b += "";
                              var x = a.replace(",", ".");
                              x = parseFloat(x) || 0;
                              var y = b.replace(",", ".");
                              y = parseFloat(y) || 0;
                              return x + y;
                            }, 0);

                          if (sum) {
                            // TODO Internationalize this
                            // TODO make this configurable ?
                            sum = parseFloat(sum).toFixed(2).toLocaleString();
                            sum = sum.replace(".", ",");
                          }
                        }

                        if (sum && this.header().className && (this.header().className.indexOf('c4g_sum') !== -1)) {
                          jQuery('.c4g_sumrow').append('<th class="c4g_list_align_right" style="width:100%;">' + sum + '</th>');
                        } else {
                          jQuery('.c4g_sumrow').append('<th class="c4g_list_align_right" style="width:100%;"></th>');
                        }
                      }
                    });
                  }
                },
                "fnDrawCallback": function () {
                  jQuery(tableDiv).find('tr')
                    .unbind('hover')
                    .unbind('click')
                    .hover(function () {
                      if (selectOnHover) {
                        jQuery(this).addClass('row_selected');
                      }
                      if (clickAction || multiSelect) {
                        jQuery(this).addClass('cursor_pointer');
                      }
                    }, function () {
                      if (selectOnHover) {
                        jQuery(this).removeClass('row_selected');
                      }
                      if (clickAction || multiSelect) {
                        jQuery(this).removeClass('cursor_pointer');
                      }
                    })
                    .click(function () {
                      if (multiSelect) {
                        jQuery(this).toggleClass('row_selected');
                      }
                      if ((clickAction) && (typeof(jQuery(this).attr('data-action')) !== 'undefined')) {
                        fnExecAjaxGet(options.ajaxData + '/' + jQuery(this).attr('data-action'));
                        return false;
                      }
                    });
                }
              }, contentdata);
              oDataTable = jQuery(tableDiv).DataTable(contentdata);
              // scope.dataTableApi = oDataTable.api();
              scope.fnDataTableColumnVis(oDataTable);
            }

          }

          if (contenttype === 'html') {
            var aClass = 'c4gGuiHtml';
            if (typeof(contentoptions) !== 'undefined') {
              if (contentoptions.scrollable) {
                aClass = aClass + ' c4gGuiScrollable';
              }
            }
            var aHtmlDiv = jQuery('<div />')
              .attr('id', 'c4gGuiHtml' + internalId)
              .attr('class', aClass)
              .appendTo(scope.contentDiv)
              .html(contentdata);

            aHtmlDiv
              .find('.c4gGuiAction')
              .hover(function () {
                if (jQuery(this).attr('data-hoverclass') !== 'undefined') {
                  if (jQuery(this).attr('data-hoverclass')) {
                    jQuery(this).addClass(jQuery(this).attr('data-hoverclass'));
                  }
                }
              }, function () {
                if (jQuery(this).attr('data-hoverclass') !== 'undefined') {
                  if (jQuery(this).attr('data-hoverclass')) {
                    jQuery(this).removeClass(jQuery(this).attr('data-hoverclass'));
                  }
                }

              })
              .click(function () {
                if (typeof(jQuery(this).attr('data-href')) !== 'undefined') {
                  if (jQuery(this).attr('data-href_newwindow')) {
                    fnJumpToLink(jQuery(this).attr('data-href'), true);
                  }
                  else {
                    fnJumpToLink(jQuery(this).attr('data-href'));
                  }
                  return false;
                }

                if ((typeof ckeditor5instances !== 'undefined') && (ckeditor5instances)) {
                  let i = Object.keys(ckeditor5instances).length;
                  while (i > 0) {
                    i -= 1;
                    ckeditor5instances[i].updateSourceElement();
                  }
                }

                if (jQuery(this).hasClass('c4gGuiSend')) {
                  var formdata = {};
                  jQuery(scope.contentDiv).find('.formdata').each(function (index, element) {
                    if (jQuery(element).attr('type') === 'checkbox') {
                      // formdata[jQuery(element).attr('name')] = (jQuery(element).attr('checked') == 'checked');
                      formdata[jQuery(element).attr('name')] = jQuery(element).is(':checked');
                    } else {
                      formdata[jQuery(element).attr('name')] = jQuery(element).val();
                    }
                  });

                  if (typeof(jQuery(this).attr('data-action')) !== 'undefined') {
                    fnExecAjaxPut(
                      options.ajaxUrl + '/' + options.ajaxData + '/' + jQuery(this).attr('data-action'),
                      formdata);
                  }
                  return false;
                }
                else if (typeof(jQuery(this).attr('data-action')) !== 'undefined') {
                  fnExecAjaxGet(options.ajaxData + '/' + jQuery(this).attr('data-action'));
                  return false;
                }

              });

            fnAddButton(aHtmlDiv);
            fnAddTooltip(aHtmlDiv);
            fnAddAccordion(aHtmlDiv);
            fnMakeCollapsible(aHtmlDiv);
          }

          //ToDo test
          window.scrollTo(0, 0);

        };  // function fnAddContent

        // call function to add the contents
        if (jQuery.isArray(content.contents)) {
          for (var i = 0; i < content.contents.length; i++) {
            // fnAddContent(
            //  content.contents[i].contenttype,
            //  content.contents[i].contentoptions,
            //  content.contents[i].contentdata);
            fnAddContent(content.contents[i]);
          }
        }
        else {
          //fnAddContent(content.contenttype,content.contentoptions,content.contentdata);
          fnAddContent(content);
        }

        jQuery('.c4gGuiCenterDiv').each(function (i, element) {
          scope.fnCenterDiv(element);
        });

        if (typeof(content.precontent) !== 'undefined') {
          jQuery(scope.contentDiv).prepend(
            jQuery('<div />').attr('class', 'c4gGuiPreContent').html(content.precontent));
        }

        if (typeof(content.postcontent) !== 'undefined') {
          jQuery('<div />')
            .attr('class', 'c4gGuiPostContent')
            .html(content.postcontent)
            .appendTo(scope.contentDiv);
        }
      }

      if (typeof(content.dialogclose) !== 'undefined') {
        if (typeof(content.dialogclose) === 'string') {
          fnDialogClose('#c4gGuiDialog' + content.dialogclose);
        }
        else {
          jQuery.each(content.dialogclose, function (index, value) {
            fnDialogClose('#c4gGuiDialog' + value);
          });
        }
      }

      if (content.dialogcloseall) {
        jQuery('.c4gGuiDialog').parent().find(".ui-dialog-titlebar-close").trigger('click');
      }

      if (typeof(content.dialogdata) !== 'undefined') {
        var dialogoptions = {};
        if (typeof(content.dialogoptions) !== 'undefined') {
          dialogoptions = content.dialogoptions;
        }
        var dialogid = internalId;
        if (typeof(content.dialogid) !== 'undefined') {
          dialogid = content.dialogid;
        }
        dialogoptions.dialogClass = 'c4gGuiDialogWrapper';
        dialogoptions.position = [
          jQuery(scope.contentWrapperDiv).parent().offset().left,
          jQuery(scope.contentWrapperDiv).parent().offset().top
        ];
        if (typeof(dialogoptions.width) === 'undefined') {
          dialogoptions.width = jQuery(scope.contentDiv).parent().width();
        }
        if (typeof(dialogoptions.height) === 'undefined') {
          if (jQuery(scope.contentWrapperDiv).parent().height() < 300) {
            dialogoptions.height = 300;
          }
          else {
            dialogoptions.height = jQuery(scope.contentWrapperDiv).parent().height();
          }
        }

        dialogoptions.close = function () {
          jQuery('#c4gGuiDialog' + dialogid).remove();
          var state = jQuery(scope.contentDiv).attr('data-state');
          if ((state !== "") && (history != null)) {
            scope.fnHistoryPush(state);
          }

          return true;
        };

        // dialog buttons
        if (typeof(content.dialogbuttons) !== 'undefined') {
          dialogoptions.buttons = [];
          jQuery(content.dialogbuttons).each(function (index, value) {
            var aClass = (value['class'] ? value['class'] : '');
            var aAccesskey = (value['accesskey'] ? value['accesskey'] : '');
            dialogoptions.buttons.push({
              cssClass: aClass, accesskey: aAccesskey, text: value.text, click: function () {
                //todo value['onclick'] == ... dann click auf link für pdf dl
                if (value.type === 'send') {
                  if ((jQuery('#ckeditor').length > 0) && CKEDITOR && CKEDITOR.instances['ckeditor'] && typeof CKEDITOR.instances['ckeditor'] !== "undefined") {
                    CKEDITOR.instances.ckeditor.updateElement();
                  } else {
                    if (typeof(wswgEditor) !== 'undefined') {
                      wswgEditor.doCheck();
                    }
                  }

                  if ((typeof ckeditor5instances !== 'undefined') && (ckeditor5instances)) {
                    //console.log(ckeditor5instances);
                    for (var key in ckeditor5instances) {
                      if (ckeditor5instances.hasOwnProperty(key)) {
                        ckeditor5instances[key].updateSourceElement();
                      }
                    }
                  }

                  var formdata = {};
                  jQuery('#c4gGuiDialog' + dialogid).find('.formdata').each(function (index, element) {
                    jQuery(element).trigger('c4g_before_save');
                    //console.log(element);
                    if (jQuery(element).attr('type') === 'checkbox') {
                      // formdata[jQuery(element).attr('name')] = (jQuery(element).attr('checked') == 'checked');
                      formdata[jQuery(element).attr('name')] = jQuery(element).is(':checked');
                    } else {
                      formdata[jQuery(element).attr('name')] = jQuery(element).val();
                    }
                  });

                  if (typeof(value.action) !== 'undefined') {
                    fnExecAjaxPut(
                      options.ajaxUrl + '/' + options.ajaxData + '/' + value.action,
                      formdata);
                  }
                  return false;
                }
                if (value.type === 'submit') {
                  jQuery('#c4gGuiDialog' + dialogid).find('.formlink').each(function (index, element) {
                    jQuery(element).trigger('c4g_before_save');
                    var newValue = '';
                    if (value.action !== 'clear') {
                      newValue = jQuery(element).val();
                    }
                    var trg = jQuery(element).attr('data-target');
                    if (trg) {
                      var trgAttr = jQuery(element).attr('data-trgattr');
                      if (!trgAttr) {
                        jQuery(trg).html(newValue);
                      }
                      else {
                        jQuery(trg).attr(trgAttr, newValue);
                      }
                    }
                  });

                  // close dialog
                  fnDialogClose('#c4gGuiDialog' + dialogid);

                }
                if (value.type === 'get') {
                  if (typeof(value.action) !== 'undefined') {
                    fnExecAjaxGet(options.ajaxData + '/' + value.action);
                  }
                }
                return false;
              }
            });
          });
        }


        var
          tmpDialogDiv = null,
          dialogClass = "";

        // remove already existing dialogs if any
        jQuery('#c4gGuiDialog' + dialogid).remove();

        if (content.dialogtype === 'html') {
          //ToDo test
          window.scrollTo(0, 0);
          dialogClass = 'c4gGuiHtml';
          if (typeof(content.usedialog) !== 'undefined') {
            tmpDialogDiv = jQuery('#c4gGuiDialog' + content.usedialog)
              .attr('id', 'c4gGuiDialog' + dialogid);

          } else {
            tmpDialogDiv = jQuery('<div />')
              .attr('id', 'c4gGuiDialog' + dialogid);
          }
        }

        if (content.dialogtype === 'form') {
          //ToDo test
          window.scrollTo(0, 0);
          dialogClass = 'c4gGuiForm';
          tmpDialogDiv = jQuery('<div />')
            .attr('id', 'c4gGuiDialog' + dialogid);
        }

        if (tmpDialogDiv != null) {

          if (options.embedDialogs) {
            if (typeof(content.usedialog) === 'undefined') {
              jQuery(scope.contentWrapperDiv).children().last().hide();
              jQuery(scope.buttonDiv).hide();
            }
            else {
              jQuery(tmpDialogDiv).empty();
            }
            var dialogContentDiv = jQuery('<div />')
              .attr('id', 'c4gGuiDialogContent' + dialogid)
              .html(content.dialogdata)
              .appendTo(tmpDialogDiv);

            //TODO use JScrollPane
            //fnAddScrollpane(tmpDialogDiv);

            if (options.jquiEmbeddedDialogs) {
              jQuery(dialogContentDiv).attr('class', 'c4gGuiDialogContent c4gGuiDialogContentJqui');
            } else {
              jQuery(dialogContentDiv).attr('class', 'c4gGuiDialogContent c4gGuiDialogContentNoJqui');
            }

            jQuery(tmpDialogDiv)
              .attr('class', dialogClass + ' c4gGuiDialog')
              .appendTo(scope.contentWrapperDiv);

            if (typeof(dialogoptions.title) !== 'undefined') {
              var titleDiv;
              if (options.jquiEmbeddedDialogs) {
                titleDiv = jQuery('<div>').attr('class', 'c4gGuiDialogTitle c4gGuiDialogTitleJqui ui-widget ui-widget-header ui-corner-all');
                titleDiv.html(dialogoptions.title);
              } else {
                titleDiv = jQuery('<div>')
                  .attr('class', 'c4gGuiDialogTitle c4gGuiDialogTitleNoJqui')
                  .append(jQuery('<h1>').html(dialogoptions.title));
              }
              jQuery(tmpDialogDiv).prepend(titleDiv);
            }

            var buttonDivClass;
            if (options.jquiEmbeddedDialogs) {
              buttonDivClass = 'c4gGuiDialogButtons c4gGuiDialogButtonsJqui';
            } else {
              buttonDivClass = 'c4gGuiDialogButtons c4gGuiDialogButtonsNoJqui';
            }
            var dialogButtonDiv = jQuery('<div>').attr('class', buttonDivClass);
            jQuery.each(dialogoptions.buttons, function (index, value) {
              var aLink = jQuery('<a>')
                .attr('href', '#')
                .attr('accesskey', value.accesskey)
                .attr('class', value.cssClass)
                .html(value.text)
                .click(value.click)
                .appendTo(dialogButtonDiv);
              if (options.jquiEmbeddedDialogs) {
                aLink.button();
              }
            });
            jQuery(tmpDialogDiv).append(dialogButtonDiv);


          } else {

            jQuery(tmpDialogDiv).html(content.dialogdata);

            dialogClass = dialogClass + ' c4gGuiScrollable c4gGuiDialog';
            if (typeof(content.usedialog) === 'undefined') {
              jQuery(tmpDialogDiv)
                .appendTo(dialogDiv);
            }

            //use JScrollPane
            fnAddScrollpane(tmpDialogDiv);

            jQuery(tmpDialogDiv)
              .attr('class', dialogClass)
              .dialog(dialogoptions)
              .dialog({
                focus: function (event, ui) {
                  var state = jQuery(this).attr('data-state');
                  if ((state !== "") && (history != null)) {
                    scope.fnHistoryPush(state);
                  }
                }
              });

          }

          jQuery(tmpDialogDiv)
            .attr('data-state', currentState);

          // convert links with class c4gGuiButton to Buttons
          fnAddButton(tmpDialogDiv);

          // toggle checkbox dependent fields on click when data-togglevis attribute is available
          jQuery(tmpDialogDiv)
            .find('input[type="checkbox"]')
            .click(function () {
              var toggle = jQuery(this).attr('data-togglevis');
              if (typeof(toggle) !== 'undefined') {
                if (jQuery(this).is(':checked')) {
                  jQuery(toggle).show();
                } else {
                  jQuery(toggle).hide();
                }
              }
            });

          // ENTER key performs clicks on elements with class "c4gGuiDefaultAction"
          jQuery(tmpDialogDiv).keypress(function (event) {
            if (event.which === 13) {
              jQuery('#c4gGuiDialog' + dialogid).parent().find('.c4gGuiDefaultAction').each(function (index, element) {
                element.click();
              });
            }
          });

          jQuery(tmpDialogDiv)
            .find('.c4gGuiAction')
            .click(function () {
              if (jQuery(this).hasClass('c4gGuiSend')) {
                var formdata = {};
                jQuery('#c4gGuiDialog' + dialogid).find('.formdata').each(function (index, element) {
                  if (jQuery(element).attr('type') === 'checkbox') {
                    // formdata[jQuery(element).attr('name')] = (jQuery(element).attr('checked') == 'checked');
                    formdata[jQuery(element).attr('name')] = jQuery(element).is(':checked');
                  } else {
                    formdata[jQuery(element).attr('name')] = jQuery(element).val();
                  }
                });

                if (typeof(jQuery(this).attr('data-action')) !== 'undefined') {
                  fnExecAjaxPut(
                    options.ajaxUrl + '/' + options.ajaxData + '/' + jQuery(this).attr('data-action'),
                    formdata);
                }
                return false;
              } else {
                if (jQuery(this).hasClass('c4gGuiAction')) {
                  if (typeof(jQuery(this).attr('data-action')) !== 'undefined') {
                    if (typeof(wswgEditor) !== 'undefined') {
                      wswgEditor.doCheck();
                    }
                    fnExecAjaxGet(options.ajaxData + '/' + jQuery(this).attr('data-action'));
                  }
                  return false;
                } else {
                  // default processing of link
                }
              }
            });

          if (typeof(jQuery.fn.button) === 'function') {
            jQuery(tmpDialogDiv)
              .find('a.c4gGuiButtonDisabled')
              .button({disabled: true});
          }

          // get source data for linked fields
          jQuery(tmpDialogDiv)
            .find('.formlink').each(function (index, element) {
            var src = jQuery(element).attr('data-source');
            if (src) {
              var srcAttr = jQuery(element).attr('data-srcattr');
              if (!srcAttr) {
                jQuery(element).attr('value', jQuery(src).html());
              }
              else {
                jQuery(element).attr('value', jQuery(src).attr(srcAttr));
              }
            }
          });
          fnAddTooltip(tmpDialogDiv);
          fnAddAccordion(tmpDialogDiv);
          fnMakeCollapsible(tmpDialogDiv);

          if (content.dialogid !== "previewpost" && content.dialogid !== "previewthread" && content.dialogid.indexOf("postmapentry") !== 0) {
            fnEnableWswgEditor();
          } else {
            jQuery('.BBCode-Area').each(function () {
              jQuery(this).html(wswgEditor.parseBBCode(jQuery(this).html()));
            });
          }
        }

      }

      // User Message
      /**
       * Create a JQuery UI dialog for the usermessage instead of an alert.
       * When opening the dialog raise the dialogs background overlay just beneath the dialogs z-index to ensure the user cannot interact with any other dialogs.
       * When closing the dialog remove itself entirely to keep the dom clean.
       * TODO: Consider providing a specific title based upon the usermessage or hiding the titlebar completely. This can be success via a CSS defition.
       */
      if (typeof(content.usermessage) !== 'undefined') {
        //ToDo auslagern
        var title = 'Usermessage';
        if (navigator.language === 'de' || navigator.language === 'de-DE') {
          title = 'Benutzerhinweis';
        }
        var callback;

        if (content.title) {
          title = content.title;
        }
        if (content.callback) {
          callback = content.callback;
        }

        var dh = new DialogHandler();
        var messageJump = '';
        if (content.jump_after_message) {
          messageJump = content.jump_after_message;
        }
        dh.show(title, content.usermessage, messageJump, callback);
      }

      // additional action to be performed via ajax
      if (typeof(content.performaction) !== 'undefined') {
        fnExecAjaxGet(options.ajaxData + '/' + content.performaction);
      }

      // show map
      if ((typeof(content.mapdata) !== 'undefined')) {
        content.mapdata.addIdToDiv = false;
        window.mapData = window.mapData || {};
        window.mapData[content.mapdata['id']] = content.mapdata;
        window.initMap(window.mapData[content.mapdata['id']]);
      }

      if ((typeof(content.cronexec) !== 'undefined') && (content.cronexec != null)) {
        var cronexec = content.cronexec;
        if (typeof(cronexec) === 'string') {
          cronexec = new Array(cronexec);
        }
        jQuery.each(cronexec, function (index, element) {
          jQuery.ajax({
            url: options.ajaxUrl + '/' + options.ajaxData + '/cron:' + element,
            data: null,
            success: function () {
            },
            global: false
          });
        })
      }

      if (typeof(content.jump_to_url) !== 'undefined') {
        fnJumpToLink(content.jump_to_url);
      }

      // TODO hier hook aufrufen um weitere funktionen nach aufbau der liste aufrufen zu können
      // TODO callHookFunctions aus maps in den core auslagern, dann kann das auch hier verwendet werden

      if (c4g.projects.hook && c4g.projects.hook.responseHandled && c4g.projects.hook.responseHandled.length > 0) {
        for (let j = 0; j < c4g.projects.hook.responseHandled.length; j++) {
          if (typeof c4g.projects.hook.responseHandled[j] === 'function') {
            if (content.searchValue) {
              c4g.projects.hook.responseHandled[j]({searchValue: content.searchValue});
            }
          }
        }
      }
    }, // end of fnHandleAjaxResponse

    fnDataTableColumnVis: function(dataTable) {
      if ((typeof(dataTable) !== 'undefined') && (dataTable!=null)) {
        var settings = dataTable.settings();
        if (settings!=null) {
          jQuery(settings.aoColumns).each(function(i,element){
            if (typeof(element.c4gMinTableSize) !== 'undefined') {
              dataTable.fnSetColumnVis(i,dataTable.width()>=element.c4gMinTableSize, false);
            }
            if (typeof(element.c4gMinTableSizeWidths) !== 'undefined') {
              // set table size dependant column widths
              jQuery(element.c4gMinTableSizeWidths).each(function(tabIndex,tabElement) {
                if (dataTable.width()>=tabElement.tsize) {
                  if (element.sWidthOrig !== tabElement.width) {
                    element.sWidthOrig = tabElement.width;
                  }
                  return false;
                }
              });
            }
          });
        }

        // if (jQuery.browser.msie  && parseInt(jQuery.browser.version, 10) === 8) {
        if (jQuery('#top').hasClass('ie8')) {
          // fnAdjustColumnSizing() produces an infinite loop in MSIE 8 (!), so workaround the problem
          dataTable.fnDraw();
        }
        else {
          // dataTable.fnAdjustColumnSizing();
        }
        // this.dataTable = dataTable;
      }
    }, // end of fnDataTableColumnVis

    fnCenterDiv: function(element) {
      var pWidth = jQuery(element).parent().width();
      var divWidth = 0;
      jQuery(element).children().each(function(i,element) {
        divWidth += jQuery(element).outerWidth(true);
        if ( divWidth > pWidth) {
          divWidth -= jQuery(element).outerWidth(true);
          return false;
        }
      });
      if (divWidth > 0) {
        jQuery(element).css({
          margin: '0 auto',
          width: divWidth+'px'
        });
      }
    }, // end of fnCenterDiv

    fnInitContentDiv: function () {
      jQuery(this.contentDiv).empty();
      jQuery(this.contentWrapperDiv + ' div:not(' + this.contentDiv + ')').remove();
      jQuery(this.contentDiv).show();
    }, // end of fnInitContentDiv

    fnHistoryPush: function (state) {
      if (history != null) {
        this.pushingState = true;
        var newHref = window.location.href;
        var index = newHref.indexOf('?state=');
        if (index !== -1) {
          newHref = newHref.substr(0, index);
        }
        var queryString = '';
        if (newHref.indexOf('?') !== -1) {
          queryString = '&state=';
          index = newHref.indexOf('&state=');
          if (index !== -1) {
            newHref = newHref.substr(0, index);
          }
        } else {
          queryString = '?state='
        }
        if (document.location.hash) {
          history.pushState(null, document.title, newHref + queryString + state + document.location.hash);
        } else {
          history.pushState(null, document.title, newHref + queryString + state);
        }

        // strange workaround for Opera >= 11.60 bug
        // TODO kann raus ?
        if (typeof(document.getElement) !== 'undefined') {
          var head = document.getElement("head");
          if (typeof(head) === 'object') {
            var base = head.getElement('base');
            if (typeof(base) === 'object') {
              base.href = base.href;
            }
          }
        }
        if (c4g.projects.clearUrl && typeof clearBrowserUrl === 'function') {
          clearBrowserUrl();
        }

        this.pushingState = false;
      }
    } // end of fnHistoryPush

  }); // end of extend
})(jQuery, this.c4g);  // single execution function

