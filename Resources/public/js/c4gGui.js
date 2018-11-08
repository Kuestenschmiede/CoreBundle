
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
(function($, c4g) {

  // id for generated DIVs
  var
    nextId = 1;


  // Extend jQuery, so c4gGui can be used with $(...).c4gGui(...)
  c4g.projects.c4gGui = function(options) {
    options = $.extend({
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
    // $.fn.c4gGui()
    // -----------------------------------
    $(window).resize(function(){
      $('.c4gGuiCenterDiv').each(function(i,element){
        scope.fnCenterDiv(element);
      });
      // scope.fnDataTableColumnVis(oDataTable);
      if (oDataTable) {
        oDataTable.columns.adjust().draw();
      }
    });

    var oDataTable = null;  // TODO enable more than one
  };


  $.extend(c4g.projects.c4gGui.prototype, {
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
            $(this).html('jQuery UI missing!');
            return;
          }
        }

        // set height and width if provided
        if (options.height !== '') {
          $(this).height(options.height);
        }
        if (options.width !== '') {
          $(this).width(options.width);
        }

        // add c4gGui class
        $(this).attr('class', function(i, val) {
          if (typeof(val) === 'undefined') {
            return 'c4gGui';
          } else {
            return val + ' c4gGui';
          }
        });

        if (typeof(options.title) !== 'undefined') {
          $('<h1 id="c4gGuiTitle">'+options.title+'</h1>').appendTo($(this));
        }
        $('<h3 id="c4gGuiSubtitle"> </h3>').appendTo($(this));



        // add Breadcrumb Area
        $('<div />')
          .attr('id', 'c4gGuiBreadcrumb'+options.id)
          .attr('class', 'c4gGuiBreadcrumb')
          .appendTo($(this));

        // add Headline Area
        $('<div />')
          .attr('id', 'c4gGuiHeadline'+options.id)
          .attr('class', 'c4gGuiHeadline')
          .appendTo($(this));

        // add Buttons Area
        scope.buttonDiv = $('<div />')
          .attr('id', 'c4gGuiButtons'+options.id)
          .attr('class', 'c4gGuiButtons')
          .appendTo($(this));
        $(scope.buttonDiv).hide();

        // add navigation
        if (options.navPanel) {
          $('<div />')
            .attr('id','c4gGuiNavigation'+options.id)
            .attr('class','c4gGuiNavigation')
            .appendTo($(this));
        }

        // create DIV for ajax Message
        $(this).append('<div class="c4gLoaderPh"></div>');
        $(document).ajaxStart(function(){
          $('.c4gGui,.c4gGuiDialog').addClass('c4gGuiAjaxBusy');
          $('.c4gLoaderPh').addClass('c4gLoader');
        });
        $(document).ajaxStop(function(){
          $('.c4gGui,.c4gGuiDialog').removeClass('c4gGuiAjaxBusy');
          $('.c4gLoaderPh').removeClass('c4gLoader');
        });

        // create DIV for content
        $('<div />')
          .attr('id','c4gGuiContent'+options.id)
          .attr('class','c4gGuiContent')
          .appendTo(
            $('<div />')
              .attr('id','c4gGuiContentWrapper'+options.id)
              .attr('class','c4gGuiContentWrapper')
              .appendTo(this));

        // create DIV for dialogs
        $('<div />')
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
          $.ajax({
            internalId: options.id,
            url: options.ajaxUrl,
            data: options.ajaxData+'/initnav',
            dataType: "json",
            type: "GET"
          }).done(function(data) {
            scope.fnHandleAjaxResponse( data, scope.internalId );
          }).fail(function(data) {
            scope.fnInitContentDiv();
            $(this.contentDiv).text('Error1: '+errorThrown);
          });
        }
        if (history != null) {
          var internalId = options.id;
          if (History && History.Adapter) {
            History.Adapter.bind(window, 'statechange', function() {
              if (!scope.pushingState) {
                var State = History.getState();
                $.ajax({
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
                  $(scope.contentDiv).text('Error2: '+errorThrown);
                });
              }
            });
          }
        }

        // set next Id in case there is more than one element to be set
        options.id++;
        nextId = options.id;
      });
    }, // end of setup

    handlePdfResponse(data, id) {
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

      var fnExecAjax = function (ajaxMethod, ajaxUrl, ajaxData) {
        if (ajaxUrl.indexOf("?") <= 0) {
          ajaxUrl += "?id=0";
        } else {
          ajaxUrl += "&id=0";
        }
        $.ajax({
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
          $(scope.contentDiv).text('Error3: ' + data);
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
            $(this).children('.c4gGuiCollapsible_trigger_target').slideToggle(250);
            $(this).nextUntil('.c4gGuiCollapsible_trigger', '.c4gGuiCollapsible_target').slideToggle(250); //.addClass('sub_active');
          });
      };

      var fnEnableWswgEditor = function () {
        if (typeof(wswgEditor) !== 'undefined') {
          if ($('#editor').length > 0) {
            wswgEditor.initEditor('editor');
          }

          if ($('#ckeditor').length > 0) {
            if (typeof ckEditorItems === "undefined" || ckEditorItems === "" || ckEditorItems.length <= 1) {
              ckEditorItems = ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo', 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', 'Blockquote', '-', 'RemoveFormat', 'NumberedList', 'BulletedList', 'Link', 'Unlink', 'Anchor', 'Image', 'FileUpload', 'Smiley', 'TextColor', 'BGColor'];
            }

            if ($.browser.mozilla) {
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
                filebrowserImageUploadUrl: options.contaoPath + "bundles/con4giscore/vendor/imgUpload.php",
                filebrowserUploadUrl: options.contaoPath + uploadApiUrl
              });
            }, 500);

          }
          $('.BBCode-Area').each(function () {
            $(this).html(wswgEditor.parseBBCode($(this).html()));
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
          $(element).hide();
          var state =
            $(element).prev().show()
              .attr('data-state');
          if ((typeof(state) === "undefined") || (state === "")) {
            state = $(scope.contentDiv).attr('data-state');
          }
          if ($(element).prev().hasClass('c4gGuiContent')) {
            $(scope.buttonDiv).show();
          }

          if ((state !== "") && (history != null)) {
            scope.fnHistoryPush(state);
          }
          $(element).remove();
        } else {
          $(element).parent().find(".ui-dialog-titlebar-close").trigger('click');
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
        content = $.parseJSON(content);
      }
      if (content == null) {
        return;
      }

      if (content.initAction) {
        // execute the action initially
        // store the id, since scope.internalId gets undefined in the done callback
        var keepId = options.id;
        $.ajax({
          internalId: options.id,
          url: options.ajaxUrl + "/" + options.id + "/" + content.initAction,
          dataType: "json",
          type: "GET"
        }).done(function(data) {
          scope.fnHandleAjaxResponse( data, keepId );
        }).fail(function(data) {
          scope.fnInitContentDiv();
          $(this.contentDiv).text('Error1: '+data);
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
        $('#c4gGuiTitle').html(content.title);
      }

      if (typeof(content.subtitle) !== "undefined") {
        $('#c4gGuiSubtitle').html(content.subtitle);
      } else {
        if (typeof(content.title) !== "undefined") {
          $('#c4gGuiSubtitle').html("");
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
        $(headlineDiv).html(content.headline);
      }

      // create breadcrumb
      if ($.isArray(content.breadcrumb)) {
        $(breadcrumbDiv).empty();
        $.each(content.breadcrumb, function (index, value) {
          if (index > 0) {
            if (options.breadcrumbDelim !== '') {
              $(breadcrumbDiv).append('<span class="c4gGuiBreadcrumbDelim">' + options.breadcrumbDelim + '</span>');
            }

          }
          var aButton = $("<a />")
            .attr('href', '#')
            .attr('class', 'c4gGuiBreadcrumbItem')
            .html(value['text'])
            .click(function (e) {
              e.preventDefault();
              e.stopPropagation();
              if ($(e.currentTarget).attr("disabled") !== "disabled") {
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

        $(".c4gGuiBreadcrumbItem").last().attr("disabled", "disabled").addClass("ui-state-hover").on("mouseleave", function (e) {
          $(e.currentTarget).addClass("ui-state-hover");
        }).on("mousedown", function (e) {
          $(e.currentTarget).removeClass("ui-state-active");
        });

        if (content !== data) {
          $(".c4gGuiBreadcrumbItem").last().attr("disabled", false);
        }

      } else {
        $(".c4gGuiBreadcrumbItem").last().attr("disabled", false);
      }

      // create buttons
      if ($.isArray(content.buttons)) {
        $(scope.buttonDiv).empty();
        $(scope.buttonDiv).hide();
        $.each(content.buttons, function (index, value) {
          var aButton = $("<a />")
            .attr('href', '#')
            .attr('accesskey', value['accesskey'])
            .html(value['text'])
            .click(function () {
              if (value['tableSelection']) {
                if ((typeof(oDataTable) !== 'undefined') && (oDataTable != null)) {
                  var formdata = {};
                  oDataTable.$('tr.row_selected').each(function (index, value) {
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
          $(scope.buttonDiv).show();
        });
      }


      if (typeof(content.treedata) !== 'undefined') {
        // populate tree with data when treedata is available

        $(navDiv).empty();

        if (typeof ($.fn.dynatree) === 'undefined') {
          $(navDiv).html('<h1>jQuery.dynatree missing</h1>');
        } else {

          var treeDiv = $('<div />')
            .attr('id', 'c4gGuiDynatree' + internalId)
            .attr('class', 'c4gGuiTree')
            .attr('width', $(navDiv).width())
            .attr('height', $(navDiv).height())
            .appendTo($(navDiv));

          // TODO: Error handling
          var treedata = $.extend(
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
          $(treeDiv).dynatree(treedata);
          $(navDiv).resizable("destroy");
          $(navDiv + " .dynatree-container").width($(navDiv).width() - 8);
          $(navDiv).resizable({
            //animate: true,
            alsoResize: navDiv + " .dynatree-container, " + navDiv + " .c4gGuiTree",
            resize: function (event, ui) {
              var newWidth = $(scope.contentWrapperDiv).parent().width() - ui.size.width - 5;
              $(scope.contentWrapperDiv).width(newWidth);
              $(scope.contentWrapperDiv).height(ui.size.height);
            },
            stop: function () {
              if ((typeof(oDataTable) !== 'undefined') && (oDataTable != null)) {
                oDataTable.fnDraw(true);
              }
            }
          });
          fnAddTooltip($(navDiv));
        }
      }

      // TODO: Error handling
      if ((typeof(content.contentdata) !== 'undefined') || ($.isArray(content.contents))) {
        // populate dataTable
        scope.fnInitContentDiv();

        var newWidth = '100%';
        var newHeight = '100%';
        if (options.navPanel) {
          newWidth = $(scope.contentWrapperDiv).parent().width()
            - $(navDiv).width() - 5;
          newHeight = $(navDiv).height();
        }
        $(scope.contentWrapperDiv).width(newWidth);
        $(scope.contentWrapperDiv).height(newHeight);
        if (typeof (content.state) !== 'undefined') {
          $(scope.contentDiv).attr('data-state', content.state);
        }


        var fnAddContent = function (content) {
          var contenttype = content.contenttype;
          var contentoptions = content.contentoptions;
          var contentdata = content.contentdata;
          if ((contenttype === 'datatable')
            && (typeof (contentdata) !== 'undefined')) {

            if (typeof ($.fn.dataTable) === 'undefined') {
              $(scope.contentDiv).html('<h1>jQuery.dataTable missing</h1>');
            } else {
              var tableDiv = $('<table />')
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
              contentdata = $.extend({
                "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                  if (actioncol !== -1) {
                    $(nRow).attr('data-action', aData[actioncol]);
                  }
                  if (selectrow !== -1) {
                    if (iDisplayIndex === selectrow) {
                      $(nRow).addClass('selected');
                      $(".dataTables_scrollBody").scrollTo(nRow);
                    }
                  }
                  if ((tooltipcol !== -1) && (typeof(jQuery.fn.tooltip) === 'function')) {
                    if (aData[tooltipcol]) {
                      $(nRow).attr('data-tooltip', aData[tooltipcol]);
                      $(nRow).tooltip({
                          bodyHandler: function () {
                            return $(nRow).attr('data-tooltip');
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
                  if ($('.c4g_sumfoot').length > 0) {
                    $('.c4g_sumfoot').remove();
                  }

                  if ($('.c4g_sum').length > 0) {
                    $(this).append('<tfoot class="c4g_sumfoot"><tr role="row" class="c4g_sumrow ui-state-highlight"></tr></tfoot>');
                    api.columns('.c4g_brick_col', {page: 'current'}).every(function () {
                      if ($(this.header()).css("display") !== "none") {
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
                          $('.c4g_sumrow').append('<th class="c4g_list_align_right" style="width:100%;">' + sum + '</th>');
                        } else {
                          $('.c4g_sumrow').append('<th class="c4g_list_align_right" style="width:100%;"></th>');
                        }
                      }
                    });
                  }
                },
                "fnDrawCallback": function () {
                  $(tableDiv).find('tr')
                    .unbind('hover')
                    .unbind('click')
                    .hover(function () {
                      if (selectOnHover) {
                        $(this).addClass('row_selected');
                      }
                      if (clickAction || multiSelect) {
                        $(this).addClass('cursor_pointer');
                      }
                    }, function () {
                      if (selectOnHover) {
                        $(this).removeClass('row_selected');
                      }
                      if (clickAction || multiSelect) {
                        $(this).removeClass('cursor_pointer');
                      }
                    })
                    .click(function () {
                      if (multiSelect) {
                        $(this).toggleClass('row_selected');
                      }
                      if ((clickAction) && (typeof($(this).attr('data-action')) !== 'undefined')) {
                        fnExecAjaxGet(options.ajaxData + '/' + $(this).attr('data-action'));
                        return false;
                      }
                    });
                }
              }, contentdata);
              var oDataTable = $(tableDiv).DataTable(contentdata);
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
            var aHtmlDiv = $('<div />')
              .attr('id', 'c4gGuiHtml' + internalId)
              .attr('class', aClass)
              .appendTo(scope.contentDiv)
              .html(contentdata);

            aHtmlDiv
              .find('.c4gGuiAction')
              .hover(function () {
                if ($(this).attr('data-hoverclass') !== 'undefined') {
                  if ($(this).attr('data-hoverclass')) {
                    $(this).addClass($(this).attr('data-hoverclass'));
                  }
                }
              }, function () {
                if ($(this).attr('data-hoverclass') !== 'undefined') {
                  if ($(this).attr('data-hoverclass')) {
                    $(this).removeClass($(this).attr('data-hoverclass'));
                  }
                }

              })
              .click(function () {
                if (typeof($(this).attr('data-href')) !== 'undefined') {
                  if ($(this).attr('data-href_newwindow')) {
                    fnJumpToLink($(this).attr('data-href'), true);
                  }
                  else {
                    fnJumpToLink($(this).attr('data-href'));
                  }
                  return false;
                }

                if ($(this).hasClass('c4gGuiSend')) {
                  var formdata = {};
                  $(scope.contentDiv).find('.formdata').each(function (index, element) {
                    if ($(element).attr('type') === 'checkbox') {
                      // formdata[$(element).attr('name')] = ($(element).attr('checked') == 'checked');
                      formdata[$(element).attr('name')] = $(element).is(':checked');
                    } else {
                      formdata[$(element).attr('name')] = $(element).val();
                    }
                  });

                  if (typeof($(this).attr('data-action')) !== 'undefined') {
                    fnExecAjaxPut(
                      options.ajaxUrl + '/' + options.ajaxData + '/' + $(this).attr('data-action'),
                      formdata);
                  }
                  return false;
                }
                else if (typeof($(this).attr('data-action')) !== 'undefined') {
                  fnExecAjaxGet(options.ajaxData + '/' + $(this).attr('data-action'));
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
        if ($.isArray(content.contents)) {
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

        $('.c4gGuiCenterDiv').each(function (i, element) {
          scope.fnCenterDiv(element);
        });

        if (typeof(content.precontent) !== 'undefined') {
          $(scope.contentDiv).prepend(
            $('<div />').attr('class', 'c4gGuiPreContent').html(content.precontent));
        }

        if (typeof(content.postcontent) !== 'undefined') {
          $('<div />')
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
          $.each(content.dialogclose, function (index, value) {
            fnDialogClose('#c4gGuiDialog' + value);
          });
        }
      }

      if (content.dialogcloseall) {
        $('.c4gGuiDialog').parent().find(".ui-dialog-titlebar-close").trigger('click');
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
          $(scope.contentWrapperDiv).parent().offset().left,
          $(scope.contentWrapperDiv).parent().offset().top
        ];
        if (typeof(dialogoptions.width) === 'undefined') {
          dialogoptions.width = $(scope.contentDiv).parent().width();
        }
        if (typeof(dialogoptions.height) === 'undefined') {
          if ($(scope.contentWrapperDiv).parent().height() < 300) {
            dialogoptions.height = 300;
          }
          else {
            dialogoptions.height = $(scope.contentWrapperDiv).parent().height();
          }
        }

        dialogoptions.close = function () {
          $('#c4gGuiDialog' + dialogid).remove();
          var state = $(scope.contentDiv).attr('data-state');
          if ((state !== "") && (history != null)) {
            scope.fnHistoryPush(state);
          }

          return true;
        };

        // dialog buttons
        if (typeof(content.dialogbuttons) !== 'undefined') {
          dialogoptions.buttons = [];
          $(content.dialogbuttons).each(function (index, value) {
            var aClass = (value['class'] ? value['class'] : '');
            var aAccesskey = (value['accesskey'] ? value['accesskey'] : '');
            dialogoptions.buttons.push({
              cssClass: aClass, accesskey: aAccesskey, text: value.text, click: function () {
                //todo value['onclick'] == ... dann click auf link für pdf dl
                if (value.type === 'send') {
                  if (($('#ckeditor').length > 0) && CKEDITOR && CKEDITOR.instances['ckeditor'] && typeof CKEDITOR.instances['ckeditor'] !== "undefined") {
                    CKEDITOR.instances.ckeditor.updateElement();
                  } else {
                    if (typeof(wswgEditor) !== 'undefined') {
                      wswgEditor.doCheck();
                    }
                  }

                  var formdata = {};
                  $('#c4gGuiDialog' + dialogid).find('.formdata').each(function (index, element) {
                    $(element).trigger('c4g_before_save');
                    if ($(element).attr('type') === 'checkbox') {
                      // formdata[$(element).attr('name')] = ($(element).attr('checked') == 'checked');
                      formdata[$(element).attr('name')] = $(element).is(':checked');
                    } else {
                      formdata[$(element).attr('name')] = $(element).val();
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
                  $('#c4gGuiDialog' + dialogid).find('.formlink').each(function (index, element) {
                    $(element).trigger('c4g_before_save');
                    var newValue = '';
                    if (value.action !== 'clear') {
                      newValue = $(element).val();
                    }
                    var trg = $(element).attr('data-target');
                    if (trg) {
                      var trgAttr = $(element).attr('data-trgattr');
                      if (!trgAttr) {
                        $(trg).html(newValue);
                      }
                      else {
                        $(trg).attr(trgAttr, newValue);
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
        $('#c4gGuiDialog' + dialogid).remove();

        if (content.dialogtype === 'html') {
          //ToDo test
          window.scrollTo(0, 0);
          dialogClass = 'c4gGuiHtml';
          if (typeof(content.usedialog) !== 'undefined') {
            tmpDialogDiv = $('#c4gGuiDialog' + content.usedialog)
              .attr('id', 'c4gGuiDialog' + dialogid);

          } else {
            tmpDialogDiv = $('<div />')
              .attr('id', 'c4gGuiDialog' + dialogid);
          }
        }

        if (content.dialogtype === 'form') {
          //ToDo test
          window.scrollTo(0, 0);
          dialogClass = 'c4gGuiForm';
          tmpDialogDiv = $('<div />')
            .attr('id', 'c4gGuiDialog' + dialogid);
        }

        if (tmpDialogDiv != null) {

          if (options.embedDialogs) {
            if (typeof(content.usedialog) === 'undefined') {
              $(scope.contentWrapperDiv).children().last().hide();
              $(scope.buttonDiv).hide();
            }
            else {
              $(tmpDialogDiv).empty();
            }
            var dialogContentDiv = $('<div />')
              .attr('id', 'c4gGuiDialogContent' + dialogid)
              .html(content.dialogdata)
              .appendTo(tmpDialogDiv);

            //TODO use JScrollPane
            //fnAddScrollpane(tmpDialogDiv);

            if (options.jquiEmbeddedDialogs) {
              $(dialogContentDiv).attr('class', 'c4gGuiDialogContent c4gGuiDialogContentJqui');
            } else {
              $(dialogContentDiv).attr('class', 'c4gGuiDialogContent c4gGuiDialogContentNoJqui');
            }

            $(tmpDialogDiv)
              .attr('class', dialogClass + ' c4gGuiDialog')
              .appendTo(scope.contentWrapperDiv);

            if (typeof(dialogoptions.title) !== 'undefined') {
              var titleDiv;
              if (options.jquiEmbeddedDialogs) {
                titleDiv = $('<div>').attr('class', 'c4gGuiDialogTitle c4gGuiDialogTitleJqui ui-widget ui-widget-header ui-corner-all');
                titleDiv.html(dialogoptions.title);
              } else {
                titleDiv = $('<div>')
                  .attr('class', 'c4gGuiDialogTitle c4gGuiDialogTitleNoJqui')
                  .append($('<h1>').html(dialogoptions.title));
              }
              $(tmpDialogDiv).prepend(titleDiv);
            }

            var buttonDivClass;
            if (options.jquiEmbeddedDialogs) {
              buttonDivClass = 'c4gGuiDialogButtons c4gGuiDialogButtonsJqui';
            } else {
              buttonDivClass = 'c4gGuiDialogButtons c4gGuiDialogButtonsNoJqui';
            }
            var dialogButtonDiv = $('<div>').attr('class', buttonDivClass);
            $.each(dialogoptions.buttons, function (index, value) {
              var aLink = $('<a>')
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
            $(tmpDialogDiv).append(dialogButtonDiv);


          } else {

            $(tmpDialogDiv).html(content.dialogdata);

            dialogClass = dialogClass + ' c4gGuiScrollable c4gGuiDialog';
            if (typeof(content.usedialog) === 'undefined') {
              $(tmpDialogDiv)
                .appendTo(dialogDiv);
            }

            //use JScrollPane
            fnAddScrollpane(tmpDialogDiv);

            $(tmpDialogDiv)
              .attr('class', dialogClass)
              .dialog(dialogoptions)
              .dialog({
                focus: function (event, ui) {
                  var state = $(this).attr('data-state');
                  if ((state !== "") && (history != null)) {
                    scope.fnHistoryPush(state);
                  }
                }
              });

          }

          $(tmpDialogDiv)
            .attr('data-state', currentState);

          // convert links with class c4gGuiButton to Buttons
          fnAddButton(tmpDialogDiv);

          // toggle checkbox dependent fields on click when data-togglevis attribute is available
          $(tmpDialogDiv)
            .find('input[type="checkbox"]')
            .click(function () {
              var toggle = $(this).attr('data-togglevis');
              if (typeof(toggle) !== 'undefined') {
                if ($(this).is(':checked')) {
                  $(toggle).show();
                } else {
                  $(toggle).hide();
                }
              }
            });

          // ENTER key performs clicks on elements with class "c4gGuiDefaultAction"
          $(tmpDialogDiv).keypress(function (event) {
            if (event.which === 13) {
              $('#c4gGuiDialog' + dialogid).parent().find('.c4gGuiDefaultAction').each(function (index, element) {
                element.click();
              });
            }
          });

          $(tmpDialogDiv)
            .find('a.c4gGuiAction')
            .click(function () {
              if ($(this).hasClass('c4gGuiSend')) {
                var formdata = {};
                $('#c4gGuiDialog' + dialogid).find('.formdata').each(function (index, element) {
                  if ($(element).attr('type') === 'checkbox') {
                    // formdata[$(element).attr('name')] = ($(element).attr('checked') == 'checked');
                    formdata[$(element).attr('name')] = $(element).is(':checked');
                  } else {
                    formdata[$(element).attr('name')] = $(element).val();
                  }
                });

                if (typeof($(this).attr('data-action')) !== 'undefined') {
                  fnExecAjaxPut(
                    options.ajaxUrl + '/' + options.ajaxData + '/' + $(this).attr('data-action'),
                    formdata);
                }
                return false;
              } else {
                if ($(this).hasClass('c4gGuiAction')) {
                  if (typeof($(this).attr('data-action')) !== 'undefined') {
                    if (typeof(wswgEditor) !== 'undefined') {
                      wswgEditor.doCheck();
                    }
                    fnExecAjaxGet(options.ajaxData + '/' + $(this).attr('data-action'));
                  }
                  return false;
                } else {
                  // default processing of link
                }
              }
            });

          if (typeof($.fn.button) === 'function') {
            $(tmpDialogDiv)
              .find('a.c4gGuiButtonDisabled')
              .button({disabled: true});
          }

          // get source data for linked fields
          $(tmpDialogDiv)
            .find('.formlink').each(function (index, element) {
            var src = $(element).attr('data-source');
            if (src) {
              var srcAttr = $(element).attr('data-srcattr');
              if (!srcAttr) {
                $(element).attr('value', $(src).html());
              }
              else {
                $(element).attr('value', $(src).attr(srcAttr));
              }
            }
          });
          fnAddTooltip(tmpDialogDiv);
          fnAddAccordion(tmpDialogDiv);
          fnMakeCollapsible(tmpDialogDiv);

          if (content.dialogid !== "previewpost" && content.dialogid !== "previewthread" && content.dialogid.indexOf("postmapentry") !== 0) {
            fnEnableWswgEditor();
          } else {
            $('.BBCode-Area').each(function () {
              $(this).html(wswgEditor.parseBBCode($(this).html()));
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
      if ((typeof(content.mapdata) !== 'undefined') && (typeof(c4g.maps) !== 'undefined')) {
        if (typeof(c4g.maps.MapController) === 'function') {
          // Version 3
          //
          content.mapdata.addIdToDiv = false;
          c4g.maps.MapController(content.mapdata);
        }
      }

      if ((typeof(content.cronexec) !== 'undefined') && (content.cronexec != null)) {
        var cronexec = content.cronexec;
        if (typeof(cronexec) === 'string') {
          cronexec = new Array(cronexec);
        }
        $.each(cronexec, function (index, element) {
          $.ajax({
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
          $(settings.aoColumns).each(function(i,element){
            if (typeof(element.c4gMinTableSize) !== 'undefined') {
              dataTable.fnSetColumnVis(i,dataTable.width()>=element.c4gMinTableSize, false);
            }
            if (typeof(element.c4gMinTableSizeWidths) !== 'undefined') {
              // set table size dependant column widths
              $(element.c4gMinTableSizeWidths).each(function(tabIndex,tabElement) {
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

        // if ($.browser.msie  && parseInt($.browser.version, 10) === 8) {
        if ($('#top').hasClass('ie8')) {
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
      var pWidth = $(element).parent().width();
      var divWidth = 0;
      $(element).children().each(function(i,element) {
        divWidth += $(element).outerWidth(true);
        if ( divWidth > pWidth) {
          divWidth -= $(element).outerWidth(true);
          return false;
        }
      });
      if (divWidth > 0) {
        $(element).css({
          margin: '0 auto',
          width: divWidth+'px'
        });
      }
    }, // end of fnCenterDiv

    fnInitContentDiv: function () {
      $(this.contentDiv).empty();
      $(this.contentWrapperDiv + ' div:not(' + this.contentDiv + ')').remove();
      $(this.contentDiv).show();
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

