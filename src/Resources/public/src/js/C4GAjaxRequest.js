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
 * Base class for performing AJAX-Requests.
 * Requests can be performed by creating an C4GAjaxRequest and calling the send() method.
 */
class C4GAjaxRequest {

  /**
   * C4GAjaxRequest constructor.
   * @param url
   * @param method
   * @param settings
   */
  constructor(url, method = 'GET', settings = null) {
    this._url = url;
    if (!settings) {
      settings = {};
    }
    // use the request as this in the callbacks instead of the ajax parameters
    //settings['context'] = this;
    // set other params
    settings['method'] = method;
    // set async to true, to avoid deprecated synchronous requests
    settings['async'] = true;
    this._settings = settings;
  }

  /**
   * Executes the request with the given settings and calls defined callbacks, if there are any.
   */
  execute() {
    let scope = this;
    jQuery.ajax(this._url, this._settings).done(function(data, textStatus, jqXHR) {
      if (scope._settings['done'] && typeof scope._settings['done'] === "function") {
          scope._settings['done'](data, textStatus, jqXHR);
      }
    }).fail(function(data, textStatus, errorThrown) {
      if (scope._settings['fail'] && typeof scope._settings['fail'] === "function") {
          scope._settings['fail'](data, textStatus, errorThrown);
      }
    }).always(function(data, textStatus, alt_jqXHR) {
      // the param alt_jqXHR is the jqXHR or the thrown error, depending on the status of the request
      if (scope._settings['always'] && typeof scope._settings['always'] === "function") {
          scope._settings['always'](data, textStatus, alt_jqXHR);
      }
    });
  }

  /**
   * Adds a 'always' callback to the request.
   * The callback is always executed after the request is finished, no matter what the status is.
   * The callback function gets passed three parameters: the response data, the statusText and if the request was
   * successful, the request object, otherwise the thrown error.
   * @param callback
   */
  addAlwaysCallback(callback) {
    if (callback && typeof callback === "function") {
      this._settings['always'] = callback;
    }
  }

  /**
   * Adds a 'fail' callback to the request.
   * The callback is executed after the request is finished with an error.
   * The callback function gets passed three parameters: the response data, the statusText and the error.
   * @param callback
   */
  addFailCallback(callback) {
    if (callback && typeof callback === "function") {
      this._settings['fail'] = callback;
    }
  }

  /**
   * Adds a 'done' callback to the request.
   * The callback is executed after the request is finished successful.
   * The callback function gets passed three parameters: the response data, the statusText and the request object.
   * @param callback
   */
  addDoneCallback(callback) {
    if (callback && typeof callback === "function") {
      this._settings['done'] = callback;
    }
  }

  /**
   * Adds request data to the request.
   * @param data
   */
  addRequestData(data) {
    this._settings['data'] = data;
  }

  /**
   * Helper function for accessing request parameter data.
   * @param key
   * @returns {*}
   */
  getRequestParameterField(key) {
    return this._settings[key];
  }

  get url() {
    return this._url;
  }

  set url(value) {
    this._url = value;
  }

  get settings() {
    return this._settings;
  }

  set settings(value) {
    this._settings = value;
  }
}
