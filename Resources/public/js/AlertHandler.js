/**
 * con4gis - the gis-kit
 *
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */

import Swal from 'sweetalert2';

export class AlertHandler {

  showErrorDialog(title, content) {
    Swal.fire(title, content, "error");
  }

  showInfoDialog(title, content) {
    Swal.fire(title, content, "info");
  }

  showConfirmDialog(title, text, confirmCallback, cancelCallback, confirmText, cancelText) {
    Swal.fire({
      title: title,
      text: text,
      type: "warning",
      showCancelButton: true,
      confirmButtonText: confirmText ? confirmText : "Confirm",
      cancelButtonText: cancelText ? cancelText : "Cancel",
      dangerMode: true
    }).then((willDelete) => {
      if (willDelete.value) {
        confirmCallback();
      } else {
        cancelCallback();
      }
    });
  }

  async showSelectDialog(title, objOptions, confirmText, cancelText) {
    const {value: selectedValue} = await Swal.fire({
      title: title,
      input: 'select',
      inputOptions: objOptions,
      inputPlaceholder: 'Select a fruit',
      showCancelButton: true,
      confirmButtonText: confirmText,
      cancelButtonText: cancelText
    });
    return selectedValue;
  }
}