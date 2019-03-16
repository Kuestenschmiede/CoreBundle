/**
 * This class generates an input field, which caches inputs into the browser's storage and suggests them, when they
 * match future inputs. Requires jQueryUI.
 */
export class CachedInputfield {

  /**
   * Constructor.
   * @param inputSelector   The input element that shall cache its inputs.
   * @param defaultSaving   If true, the storing of input values will be bound to the change event of the input field.
   *                        This value can be set to false if you wish to take care of the input storing yourself.
   *                        This is recommended, as you have more control over when things get stored. You can add a
   *                        value to the suggestions and store it into the browser by calling the "storeValue" method.
   * @param cacheKey        This will be used as cache key for the inputs of this field.
   */
  constructor(inputSelector, defaultSaving, cacheKey) {
    this.cacheKey = cacheKey;
    this.inputField = jQuery(inputSelector);
    // this.setHoverStyle(this.highlightColor);
    if (!this.inputField) {
      console.warn("The given CSS selector matches no DOM element...");
      return;
    }
    this.suggestions = this.loadValues();
    this.inputField.autocomplete({
      source: this.suggestions,
      delay: 0
    });
    const scope = this;
    if (defaultSaving) {
      jQuery(this.inputField).on('change', function() {
        scope.storeValue(jQuery(this).val());
      });
    }
    jQuery(this.inputField).on('input', function() {
      let options = scope.loadValues();
      jQuery(this).autocomplete("option", "source", options);
    });
  }

  /**
   * Returns the cache key for this input field.
   * @returns {*}
   */
  getCacheKey() {
    return this.cacheKey;
  }

  /**
   * Adds a value to this.suggestions and updates the value stored in the browser.
   */
  storeValue(value) {
    if (value && !this.suggestions.includes(value)) {
      this.suggestions.push(value);
      this.inputField.autocomplete("option", "source", this.suggestions);
      this.serializeValues();
    }
  }

  /**
   * Writes the current suggestions into the browser storage.
   */
  serializeValues() {
    window.localStorage.setItem(this.getCacheKey(), JSON.stringify(this.suggestions));
  }

  /**
   * Loads the values from the browser storage.
   */
  loadValues() {
    const localStorage = window.localStorage;
    let tmpResult = localStorage.getItem(this.getCacheKey());
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
}