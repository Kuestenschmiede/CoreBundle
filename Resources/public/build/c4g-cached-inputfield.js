/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/build/";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./Resources/public/js/c4g-cached-inputfield.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./Resources/public/js/c4g-cached-inputfield.js":
/*!******************************************************!*\
  !*** ./Resources/public/js/c4g-cached-inputfield.js ***!
  \******************************************************/
/*! no static exports found */
/***/ (function(module, exports) {

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

/**
 * This class generates an input field, which caches inputs into the browser's storage and suggests them, when they
 * match future inputs. Requires jQueryUI.
 */
var CachedInputfield =
/*#__PURE__*/
function () {
  /**
   * Constructor.
   * @param inputSelector   The input element that shall cache its inputs.
   * @param defaultSaving   If true, the storing of input values will be bound to the change event of the input field.
   *                        This value can be set to false if you wish to take care of the input storing yourself.
   *                        This is recommended, as you have more control over when things get stored. You can add a
   *                        value to the suggestions and store it into the browser by calling the "storeValue" method.
   * @param cacheKey        This will be used as cache key for the inputs of this field.
   */
  function CachedInputfield(inputSelector, defaultSaving, cacheKey) {
    _classCallCheck(this, CachedInputfield);

    this.cacheKey = cacheKey;
    this.inputField = $(inputSelector); // this.setHoverStyle(this.highlightColor);

    if (!this.inputField) {
      console.warn("The given CSS selector matches no DOM element...");
      return;
    }

    this.suggestions = this.loadValues();
    this.inputField.autocomplete({
      source: this.suggestions,
      delay: 0
    });
    var scope = this;

    if (defaultSaving) {
      $(this.inputField).on('change', function () {
        scope.storeValue($(this).val());
      });
    }

    $(this.inputField).on('input', function () {
      var options = scope.loadValues();
      $(this).autocomplete("option", "source", options);
    });
  }
  /**
   * Returns the cache key for this input field.
   * @returns {*}
   */


  _createClass(CachedInputfield, [{
    key: "getCacheKey",
    value: function getCacheKey() {
      return this.cacheKey;
    }
    /**
     * Adds a value to this.suggestions and updates the value stored in the browser.
     */

  }, {
    key: "storeValue",
    value: function storeValue(value) {
      if (value && !this.suggestions.includes(value)) {
        this.suggestions.push(value);
        this.inputField.autocomplete("option", "source", this.suggestions);
        this.serializeValues();
      }
    }
    /**
     * Writes the current suggestions into the browser storage.
     */

  }, {
    key: "serializeValues",
    value: function serializeValues() {
      window.localStorage.setItem(this.getCacheKey(), JSON.stringify(this.suggestions));
    }
    /**
     * Loads the values from the browser storage.
     */

  }, {
    key: "loadValues",
    value: function loadValues() {
      var localStorage = window.localStorage;
      var tmpResult = localStorage.getItem(this.getCacheKey());

      if (tmpResult) {
        // only assign when a defined value was loaded
        // the loaded value is encoded json
        this.suggestions = JSON.parse(tmpResult);

        if (!this.suggestions) {
          this.suggestions = [];
        }
      } else {
        this.suggestions = [];
      }

      return this.suggestions;
    }
  }]);

  return CachedInputfield;
}();

/***/ })

/******/ });
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vLy4vUmVzb3VyY2VzL3B1YmxpYy9qcy9jNGctY2FjaGVkLWlucHV0ZmllbGQuanMiXSwibmFtZXMiOlsiQ2FjaGVkSW5wdXRmaWVsZCIsImlucHV0U2VsZWN0b3IiLCJkZWZhdWx0U2F2aW5nIiwiY2FjaGVLZXkiLCJpbnB1dEZpZWxkIiwiJCIsImNvbnNvbGUiLCJ3YXJuIiwic3VnZ2VzdGlvbnMiLCJsb2FkVmFsdWVzIiwiYXV0b2NvbXBsZXRlIiwic291cmNlIiwiZGVsYXkiLCJzY29wZSIsIm9uIiwic3RvcmVWYWx1ZSIsInZhbCIsIm9wdGlvbnMiLCJ2YWx1ZSIsImluY2x1ZGVzIiwicHVzaCIsInNlcmlhbGl6ZVZhbHVlcyIsIndpbmRvdyIsImxvY2FsU3RvcmFnZSIsInNldEl0ZW0iLCJnZXRDYWNoZUtleSIsIkpTT04iLCJzdHJpbmdpZnkiLCJ0bXBSZXN1bHQiLCJnZXRJdGVtIiwicGFyc2UiXSwibWFwcGluZ3MiOiI7QUFBQTtBQUNBOztBQUVBO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7O0FBRUE7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTs7O0FBR0E7QUFDQTs7QUFFQTtBQUNBOztBQUVBO0FBQ0E7QUFDQTtBQUNBLGtEQUEwQyxnQ0FBZ0M7QUFDMUU7QUFDQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxnRUFBd0Qsa0JBQWtCO0FBQzFFO0FBQ0EseURBQWlELGNBQWM7QUFDL0Q7O0FBRUE7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLGlEQUF5QyxpQ0FBaUM7QUFDMUUsd0hBQWdILG1CQUFtQixFQUFFO0FBQ3JJO0FBQ0E7O0FBRUE7QUFDQTtBQUNBO0FBQ0EsbUNBQTJCLDBCQUEwQixFQUFFO0FBQ3ZELHlDQUFpQyxlQUFlO0FBQ2hEO0FBQ0E7QUFDQTs7QUFFQTtBQUNBLDhEQUFzRCwrREFBK0Q7O0FBRXJIO0FBQ0E7OztBQUdBO0FBQ0E7Ozs7Ozs7Ozs7Ozs7Ozs7OztBQ2xGQTs7OztJQUlNQSxnQjs7O0FBRUo7Ozs7Ozs7OztBQVNBLDRCQUFZQyxhQUFaLEVBQTJCQyxhQUEzQixFQUEwQ0MsUUFBMUMsRUFBb0Q7QUFBQTs7QUFDbEQsU0FBS0EsUUFBTCxHQUFnQkEsUUFBaEI7QUFDQSxTQUFLQyxVQUFMLEdBQWtCQyxDQUFDLENBQUNKLGFBQUQsQ0FBbkIsQ0FGa0QsQ0FHbEQ7O0FBQ0EsUUFBSSxDQUFDLEtBQUtHLFVBQVYsRUFBc0I7QUFDcEJFLGFBQU8sQ0FBQ0MsSUFBUixDQUFhLGtEQUFiO0FBQ0E7QUFDRDs7QUFDRCxTQUFLQyxXQUFMLEdBQW1CLEtBQUtDLFVBQUwsRUFBbkI7QUFDQSxTQUFLTCxVQUFMLENBQWdCTSxZQUFoQixDQUE2QjtBQUMzQkMsWUFBTSxFQUFFLEtBQUtILFdBRGM7QUFFM0JJLFdBQUssRUFBRTtBQUZvQixLQUE3QjtBQUlBLFFBQU1DLEtBQUssR0FBRyxJQUFkOztBQUNBLFFBQUlYLGFBQUosRUFBbUI7QUFDakJHLE9BQUMsQ0FBQyxLQUFLRCxVQUFOLENBQUQsQ0FBbUJVLEVBQW5CLENBQXNCLFFBQXRCLEVBQWdDLFlBQVc7QUFDekNELGFBQUssQ0FBQ0UsVUFBTixDQUFpQlYsQ0FBQyxDQUFDLElBQUQsQ0FBRCxDQUFRVyxHQUFSLEVBQWpCO0FBQ0QsT0FGRDtBQUdEOztBQUNEWCxLQUFDLENBQUMsS0FBS0QsVUFBTixDQUFELENBQW1CVSxFQUFuQixDQUFzQixPQUF0QixFQUErQixZQUFXO0FBQ3hDLFVBQUlHLE9BQU8sR0FBR0osS0FBSyxDQUFDSixVQUFOLEVBQWQ7QUFDQUosT0FBQyxDQUFDLElBQUQsQ0FBRCxDQUFRSyxZQUFSLENBQXFCLFFBQXJCLEVBQStCLFFBQS9CLEVBQXlDTyxPQUF6QztBQUNELEtBSEQ7QUFJRDtBQUVEOzs7Ozs7OztrQ0FJYztBQUNaLGFBQU8sS0FBS2QsUUFBWjtBQUNEO0FBRUQ7Ozs7OzsrQkFHV2UsSyxFQUFPO0FBQ2hCLFVBQUlBLEtBQUssSUFBSSxDQUFDLEtBQUtWLFdBQUwsQ0FBaUJXLFFBQWpCLENBQTBCRCxLQUExQixDQUFkLEVBQWdEO0FBQzlDLGFBQUtWLFdBQUwsQ0FBaUJZLElBQWpCLENBQXNCRixLQUF0QjtBQUNBLGFBQUtkLFVBQUwsQ0FBZ0JNLFlBQWhCLENBQTZCLFFBQTdCLEVBQXVDLFFBQXZDLEVBQWlELEtBQUtGLFdBQXREO0FBQ0EsYUFBS2EsZUFBTDtBQUNEO0FBQ0Y7QUFFRDs7Ozs7O3NDQUdrQjtBQUNoQkMsWUFBTSxDQUFDQyxZQUFQLENBQW9CQyxPQUFwQixDQUE0QixLQUFLQyxXQUFMLEVBQTVCLEVBQWdEQyxJQUFJLENBQUNDLFNBQUwsQ0FBZSxLQUFLbkIsV0FBcEIsQ0FBaEQ7QUFDRDtBQUVEOzs7Ozs7aUNBR2E7QUFDWCxVQUFNZSxZQUFZLEdBQUdELE1BQU0sQ0FBQ0MsWUFBNUI7QUFDQSxVQUFJSyxTQUFTLEdBQUdMLFlBQVksQ0FBQ00sT0FBYixDQUFxQixLQUFLSixXQUFMLEVBQXJCLENBQWhCOztBQUNBLFVBQUlHLFNBQUosRUFBZTtBQUNiO0FBQ0E7QUFDQSxhQUFLcEIsV0FBTCxHQUFtQmtCLElBQUksQ0FBQ0ksS0FBTCxDQUFXRixTQUFYLENBQW5COztBQUNBLFlBQUksQ0FBQyxLQUFLcEIsV0FBVixFQUF1QjtBQUNyQixlQUFLQSxXQUFMLEdBQW1CLEVBQW5CO0FBQ0Q7QUFDRixPQVBELE1BT087QUFDTCxhQUFLQSxXQUFMLEdBQW1CLEVBQW5CO0FBQ0Q7O0FBQ0QsYUFBTyxLQUFLQSxXQUFaO0FBQ0QiLCJmaWxlIjoiYzRnLWNhY2hlZC1pbnB1dGZpZWxkLmpzIiwic291cmNlc0NvbnRlbnQiOlsiIFx0Ly8gVGhlIG1vZHVsZSBjYWNoZVxuIFx0dmFyIGluc3RhbGxlZE1vZHVsZXMgPSB7fTtcblxuIFx0Ly8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbiBcdGZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblxuIFx0XHQvLyBDaGVjayBpZiBtb2R1bGUgaXMgaW4gY2FjaGVcbiBcdFx0aWYoaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0pIHtcbiBcdFx0XHRyZXR1cm4gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0uZXhwb3J0cztcbiBcdFx0fVxuIFx0XHQvLyBDcmVhdGUgYSBuZXcgbW9kdWxlIChhbmQgcHV0IGl0IGludG8gdGhlIGNhY2hlKVxuIFx0XHR2YXIgbW9kdWxlID0gaW5zdGFsbGVkTW9kdWxlc1ttb2R1bGVJZF0gPSB7XG4gXHRcdFx0aTogbW9kdWxlSWQsXG4gXHRcdFx0bDogZmFsc2UsXG4gXHRcdFx0ZXhwb3J0czoge31cbiBcdFx0fTtcblxuIFx0XHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cbiBcdFx0bW9kdWxlc1ttb2R1bGVJZF0uY2FsbChtb2R1bGUuZXhwb3J0cywgbW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cbiBcdFx0Ly8gRmxhZyB0aGUgbW9kdWxlIGFzIGxvYWRlZFxuIFx0XHRtb2R1bGUubCA9IHRydWU7XG5cbiBcdFx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcbiBcdFx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xuIFx0fVxuXG5cbiBcdC8vIGV4cG9zZSB0aGUgbW9kdWxlcyBvYmplY3QgKF9fd2VicGFja19tb2R1bGVzX18pXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLm0gPSBtb2R1bGVzO1xuXG4gXHQvLyBleHBvc2UgdGhlIG1vZHVsZSBjYWNoZVxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5jID0gaW5zdGFsbGVkTW9kdWxlcztcblxuIFx0Ly8gZGVmaW5lIGdldHRlciBmdW5jdGlvbiBmb3IgaGFybW9ueSBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSBmdW5jdGlvbihleHBvcnRzLCBuYW1lLCBnZXR0ZXIpIHtcbiBcdFx0aWYoIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBuYW1lKSkge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBuYW1lLCB7IGVudW1lcmFibGU6IHRydWUsIGdldDogZ2V0dGVyIH0pO1xuIFx0XHR9XG4gXHR9O1xuXG4gXHQvLyBkZWZpbmUgX19lc01vZHVsZSBvbiBleHBvcnRzXG4gXHRfX3dlYnBhY2tfcmVxdWlyZV9fLnIgPSBmdW5jdGlvbihleHBvcnRzKSB7XG4gXHRcdGlmKHR5cGVvZiBTeW1ib2wgIT09ICd1bmRlZmluZWQnICYmIFN5bWJvbC50b1N0cmluZ1RhZykge1xuIFx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBTeW1ib2wudG9TdHJpbmdUYWcsIHsgdmFsdWU6ICdNb2R1bGUnIH0pO1xuIFx0XHR9XG4gXHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCAnX19lc01vZHVsZScsIHsgdmFsdWU6IHRydWUgfSk7XG4gXHR9O1xuXG4gXHQvLyBjcmVhdGUgYSBmYWtlIG5hbWVzcGFjZSBvYmplY3RcbiBcdC8vIG1vZGUgJiAxOiB2YWx1ZSBpcyBhIG1vZHVsZSBpZCwgcmVxdWlyZSBpdFxuIFx0Ly8gbW9kZSAmIDI6IG1lcmdlIGFsbCBwcm9wZXJ0aWVzIG9mIHZhbHVlIGludG8gdGhlIG5zXG4gXHQvLyBtb2RlICYgNDogcmV0dXJuIHZhbHVlIHdoZW4gYWxyZWFkeSBucyBvYmplY3RcbiBcdC8vIG1vZGUgJiA4fDE6IGJlaGF2ZSBsaWtlIHJlcXVpcmVcbiBcdF9fd2VicGFja19yZXF1aXJlX18udCA9IGZ1bmN0aW9uKHZhbHVlLCBtb2RlKSB7XG4gXHRcdGlmKG1vZGUgJiAxKSB2YWx1ZSA9IF9fd2VicGFja19yZXF1aXJlX18odmFsdWUpO1xuIFx0XHRpZihtb2RlICYgOCkgcmV0dXJuIHZhbHVlO1xuIFx0XHRpZigobW9kZSAmIDQpICYmIHR5cGVvZiB2YWx1ZSA9PT0gJ29iamVjdCcgJiYgdmFsdWUgJiYgdmFsdWUuX19lc01vZHVsZSkgcmV0dXJuIHZhbHVlO1xuIFx0XHR2YXIgbnMgPSBPYmplY3QuY3JlYXRlKG51bGwpO1xuIFx0XHRfX3dlYnBhY2tfcmVxdWlyZV9fLnIobnMpO1xuIFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkobnMsICdkZWZhdWx0JywgeyBlbnVtZXJhYmxlOiB0cnVlLCB2YWx1ZTogdmFsdWUgfSk7XG4gXHRcdGlmKG1vZGUgJiAyICYmIHR5cGVvZiB2YWx1ZSAhPSAnc3RyaW5nJykgZm9yKHZhciBrZXkgaW4gdmFsdWUpIF9fd2VicGFja19yZXF1aXJlX18uZChucywga2V5LCBmdW5jdGlvbihrZXkpIHsgcmV0dXJuIHZhbHVlW2tleV07IH0uYmluZChudWxsLCBrZXkpKTtcbiBcdFx0cmV0dXJuIG5zO1xuIFx0fTtcblxuIFx0Ly8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbiBcdF9fd2VicGFja19yZXF1aXJlX18ubiA9IGZ1bmN0aW9uKG1vZHVsZSkge1xuIFx0XHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cbiBcdFx0XHRmdW5jdGlvbiBnZXREZWZhdWx0KCkgeyByZXR1cm4gbW9kdWxlWydkZWZhdWx0J107IH0gOlxuIFx0XHRcdGZ1bmN0aW9uIGdldE1vZHVsZUV4cG9ydHMoKSB7IHJldHVybiBtb2R1bGU7IH07XG4gXHRcdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsICdhJywgZ2V0dGVyKTtcbiBcdFx0cmV0dXJuIGdldHRlcjtcbiBcdH07XG5cbiBcdC8vIE9iamVjdC5wcm90b3R5cGUuaGFzT3duUHJvcGVydHkuY2FsbFxuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5vID0gZnVuY3Rpb24ob2JqZWN0LCBwcm9wZXJ0eSkgeyByZXR1cm4gT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iamVjdCwgcHJvcGVydHkpOyB9O1xuXG4gXHQvLyBfX3dlYnBhY2tfcHVibGljX3BhdGhfX1xuIFx0X193ZWJwYWNrX3JlcXVpcmVfXy5wID0gXCIvYnVpbGQvXCI7XG5cblxuIFx0Ly8gTG9hZCBlbnRyeSBtb2R1bGUgYW5kIHJldHVybiBleHBvcnRzXG4gXHRyZXR1cm4gX193ZWJwYWNrX3JlcXVpcmVfXyhfX3dlYnBhY2tfcmVxdWlyZV9fLnMgPSBcIi4vUmVzb3VyY2VzL3B1YmxpYy9qcy9jNGctY2FjaGVkLWlucHV0ZmllbGQuanNcIik7XG4iLCIvKipcbiAqIFRoaXMgY2xhc3MgZ2VuZXJhdGVzIGFuIGlucHV0IGZpZWxkLCB3aGljaCBjYWNoZXMgaW5wdXRzIGludG8gdGhlIGJyb3dzZXIncyBzdG9yYWdlIGFuZCBzdWdnZXN0cyB0aGVtLCB3aGVuIHRoZXlcbiAqIG1hdGNoIGZ1dHVyZSBpbnB1dHMuIFJlcXVpcmVzIGpRdWVyeVVJLlxuICovXG5jbGFzcyBDYWNoZWRJbnB1dGZpZWxkIHtcblxuICAvKipcbiAgICogQ29uc3RydWN0b3IuXG4gICAqIEBwYXJhbSBpbnB1dFNlbGVjdG9yICAgVGhlIGlucHV0IGVsZW1lbnQgdGhhdCBzaGFsbCBjYWNoZSBpdHMgaW5wdXRzLlxuICAgKiBAcGFyYW0gZGVmYXVsdFNhdmluZyAgIElmIHRydWUsIHRoZSBzdG9yaW5nIG9mIGlucHV0IHZhbHVlcyB3aWxsIGJlIGJvdW5kIHRvIHRoZSBjaGFuZ2UgZXZlbnQgb2YgdGhlIGlucHV0IGZpZWxkLlxuICAgKiAgICAgICAgICAgICAgICAgICAgICAgIFRoaXMgdmFsdWUgY2FuIGJlIHNldCB0byBmYWxzZSBpZiB5b3Ugd2lzaCB0byB0YWtlIGNhcmUgb2YgdGhlIGlucHV0IHN0b3JpbmcgeW91cnNlbGYuXG4gICAqICAgICAgICAgICAgICAgICAgICAgICAgVGhpcyBpcyByZWNvbW1lbmRlZCwgYXMgeW91IGhhdmUgbW9yZSBjb250cm9sIG92ZXIgd2hlbiB0aGluZ3MgZ2V0IHN0b3JlZC4gWW91IGNhbiBhZGQgYVxuICAgKiAgICAgICAgICAgICAgICAgICAgICAgIHZhbHVlIHRvIHRoZSBzdWdnZXN0aW9ucyBhbmQgc3RvcmUgaXQgaW50byB0aGUgYnJvd3NlciBieSBjYWxsaW5nIHRoZSBcInN0b3JlVmFsdWVcIiBtZXRob2QuXG4gICAqIEBwYXJhbSBjYWNoZUtleSAgICAgICAgVGhpcyB3aWxsIGJlIHVzZWQgYXMgY2FjaGUga2V5IGZvciB0aGUgaW5wdXRzIG9mIHRoaXMgZmllbGQuXG4gICAqL1xuICBjb25zdHJ1Y3RvcihpbnB1dFNlbGVjdG9yLCBkZWZhdWx0U2F2aW5nLCBjYWNoZUtleSkge1xuICAgIHRoaXMuY2FjaGVLZXkgPSBjYWNoZUtleTtcbiAgICB0aGlzLmlucHV0RmllbGQgPSAkKGlucHV0U2VsZWN0b3IpO1xuICAgIC8vIHRoaXMuc2V0SG92ZXJTdHlsZSh0aGlzLmhpZ2hsaWdodENvbG9yKTtcbiAgICBpZiAoIXRoaXMuaW5wdXRGaWVsZCkge1xuICAgICAgY29uc29sZS53YXJuKFwiVGhlIGdpdmVuIENTUyBzZWxlY3RvciBtYXRjaGVzIG5vIERPTSBlbGVtZW50Li4uXCIpO1xuICAgICAgcmV0dXJuO1xuICAgIH1cbiAgICB0aGlzLnN1Z2dlc3Rpb25zID0gdGhpcy5sb2FkVmFsdWVzKCk7XG4gICAgdGhpcy5pbnB1dEZpZWxkLmF1dG9jb21wbGV0ZSh7XG4gICAgICBzb3VyY2U6IHRoaXMuc3VnZ2VzdGlvbnMsXG4gICAgICBkZWxheTogMFxuICAgIH0pO1xuICAgIGNvbnN0IHNjb3BlID0gdGhpcztcbiAgICBpZiAoZGVmYXVsdFNhdmluZykge1xuICAgICAgJCh0aGlzLmlucHV0RmllbGQpLm9uKCdjaGFuZ2UnLCBmdW5jdGlvbigpIHtcbiAgICAgICAgc2NvcGUuc3RvcmVWYWx1ZSgkKHRoaXMpLnZhbCgpKTtcbiAgICAgIH0pO1xuICAgIH1cbiAgICAkKHRoaXMuaW5wdXRGaWVsZCkub24oJ2lucHV0JywgZnVuY3Rpb24oKSB7XG4gICAgICBsZXQgb3B0aW9ucyA9IHNjb3BlLmxvYWRWYWx1ZXMoKTtcbiAgICAgICQodGhpcykuYXV0b2NvbXBsZXRlKFwib3B0aW9uXCIsIFwic291cmNlXCIsIG9wdGlvbnMpO1xuICAgIH0pO1xuICB9XG5cbiAgLyoqXG4gICAqIFJldHVybnMgdGhlIGNhY2hlIGtleSBmb3IgdGhpcyBpbnB1dCBmaWVsZC5cbiAgICogQHJldHVybnMgeyp9XG4gICAqL1xuICBnZXRDYWNoZUtleSgpIHtcbiAgICByZXR1cm4gdGhpcy5jYWNoZUtleTtcbiAgfVxuXG4gIC8qKlxuICAgKiBBZGRzIGEgdmFsdWUgdG8gdGhpcy5zdWdnZXN0aW9ucyBhbmQgdXBkYXRlcyB0aGUgdmFsdWUgc3RvcmVkIGluIHRoZSBicm93c2VyLlxuICAgKi9cbiAgc3RvcmVWYWx1ZSh2YWx1ZSkge1xuICAgIGlmICh2YWx1ZSAmJiAhdGhpcy5zdWdnZXN0aW9ucy5pbmNsdWRlcyh2YWx1ZSkpIHtcbiAgICAgIHRoaXMuc3VnZ2VzdGlvbnMucHVzaCh2YWx1ZSk7XG4gICAgICB0aGlzLmlucHV0RmllbGQuYXV0b2NvbXBsZXRlKFwib3B0aW9uXCIsIFwic291cmNlXCIsIHRoaXMuc3VnZ2VzdGlvbnMpO1xuICAgICAgdGhpcy5zZXJpYWxpemVWYWx1ZXMoKTtcbiAgICB9XG4gIH1cblxuICAvKipcbiAgICogV3JpdGVzIHRoZSBjdXJyZW50IHN1Z2dlc3Rpb25zIGludG8gdGhlIGJyb3dzZXIgc3RvcmFnZS5cbiAgICovXG4gIHNlcmlhbGl6ZVZhbHVlcygpIHtcbiAgICB3aW5kb3cubG9jYWxTdG9yYWdlLnNldEl0ZW0odGhpcy5nZXRDYWNoZUtleSgpLCBKU09OLnN0cmluZ2lmeSh0aGlzLnN1Z2dlc3Rpb25zKSk7XG4gIH1cblxuICAvKipcbiAgICogTG9hZHMgdGhlIHZhbHVlcyBmcm9tIHRoZSBicm93c2VyIHN0b3JhZ2UuXG4gICAqL1xuICBsb2FkVmFsdWVzKCkge1xuICAgIGNvbnN0IGxvY2FsU3RvcmFnZSA9IHdpbmRvdy5sb2NhbFN0b3JhZ2U7XG4gICAgbGV0IHRtcFJlc3VsdCA9IGxvY2FsU3RvcmFnZS5nZXRJdGVtKHRoaXMuZ2V0Q2FjaGVLZXkoKSk7XG4gICAgaWYgKHRtcFJlc3VsdCkge1xuICAgICAgLy8gb25seSBhc3NpZ24gd2hlbiBhIGRlZmluZWQgdmFsdWUgd2FzIGxvYWRlZFxuICAgICAgLy8gdGhlIGxvYWRlZCB2YWx1ZSBpcyBlbmNvZGVkIGpzb25cbiAgICAgIHRoaXMuc3VnZ2VzdGlvbnMgPSBKU09OLnBhcnNlKHRtcFJlc3VsdCk7XG4gICAgICBpZiAoIXRoaXMuc3VnZ2VzdGlvbnMpIHtcbiAgICAgICAgdGhpcy5zdWdnZXN0aW9ucyA9IFtdO1xuICAgICAgfVxuICAgIH0gZWxzZSB7XG4gICAgICB0aGlzLnN1Z2dlc3Rpb25zID0gW107XG4gICAgfVxuICAgIHJldHVybiB0aGlzLnN1Z2dlc3Rpb25zO1xuICB9XG59Il0sInNvdXJjZVJvb3QiOiIifQ==