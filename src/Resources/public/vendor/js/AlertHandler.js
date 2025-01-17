/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 10
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2025, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

import Swal from 'sweetalert2';

/**
 * The "cssClass" parameter in the functions can either be a string or an object. If it's a string, it will be applied
 * as class for the popup. If an object is passed, the classes can be set more precise. The following properties are
 * possible in the object:
 * cssClass: {
 *   container: 'container-class',
 *   popup: 'popup-class',
 *   header: 'header-class',
 *   title: 'title-class',
 *   closeButton: 'close-button-class',
 *   icon: 'icon-class',
 *   image: 'image-class',
 *   content: 'content-class',
 *   input: 'input-class',
 *   actions: 'actions-class',
 *   confirmButton: 'confirm-button-class',
 *   cancelButton: 'cancel-button-class',
 *   footer: 'footer-class'
 * }
 */
export class AlertHandler {

  showErrorDialog(title, content, cssClass) {
    Swal.fire({
      title: title,
      text: content,
      icon: "error",
      customClass: cssClass ? cssClass : ''
    });
  }

  showInfoDialog(title, content, cssClass) {
    Swal.fire({
      title: title,
      text: content,
      icon: "info",
      customClass: cssClass ? cssClass : ''
    });;
  }

  showInfoActionDialog(title, content, confirmCallback, cssClass) {
    Swal.fire({
      title: title,
      text: content,
      icon: "info",
      customClass: cssClass ? cssClass : ''
    }).then (
        function () {
          confirmCallback();
        }
    );
  }

  showInfoActionDialog2(title, content, confirmCallback, cssClass) {
    Swal.fire({
      title: title,
      text: content,
      icon: "info",
      showLoaderOnConfirm: true,
      customClass: cssClass ? cssClass : '',
      preConfirm: (data) => {
        confirmCallback();
      }});
  }


  showConfirmDialog(title, text, confirmCallback, cancelCallback, confirmText, cancelText, cssClass) {
    Swal.fire({
      title: title,
      text: text,
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: confirmText ? confirmText : "Confirm",
      cancelButtonText: cancelText ? cancelText : "Cancel",
      customClass: cssClass ? cssClass : ''
    }).then((willDelete) => {
      if (willDelete.value) {
        confirmCallback();
      } else {
        cancelCallback();
      }
    });
  }
  showConfirmDialogHTML(title, html, confirmCallback, cancelCallback, confirmText, cancelText, cssClass) {
    Swal.fire({
      title: title,
      html: html,
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: confirmText ? confirmText : "Confirm",
      cancelButtonText: cancelText ? cancelText : "Cancel",
      customClass: cssClass ? cssClass : ''
    }).then((willDelete) => {
      if (willDelete.value) {
        confirmCallback();
      } else {
        cancelCallback();
      }
    });
  }

  showPreConfirmDialog(title, text, preConfirmCallback, confirmText, cancelText, cssClass, showLoading) {
    Swal.fire({
      title: title,
      text: text,
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: confirmText ? confirmText : "Confirm",
      cancelButtonText: cancelText ? cancelText : "Cancel",
      showLoaderOnConfirm: showLoading ? showLoading : true,
      preConfirm: function () {
        return new Promise (function (data) {
          preConfirmCallback();
        })
      },
      allowOutsideClick: () => !Swal.isLoading(),
      customClass: cssClass ? cssClass : ''
    });
  }

  showLoadingDialog(title, text, preConfirmCallback, cssClass) {
    Swal.fire({
      title: title,
      text: text,
      showCancelButton: false,
      allowEscapeKey: false,
      allowOutsideClick: false,
      showLoaderOnConfirm: true,
      onBeforeOpen: () => {
        Swal.clickConfirm();
      },
      customClass: cssClass ? cssClass : '',
      preConfirm: () => {
        return new Promise (function (data) {
          preConfirmCallback();
        })
      }
    });
  }

  async showSelectDialog(title, objOptions, confirmText, cancelText, cssClass) {
    const {value: selectedValue} = await Swal.fire({
      title: title,
      input: 'select',
      inputOptions: objOptions,
      inputPlaceholder: '-',
      showCancelButton: true,
      confirmButtonText: confirmText,
      cancelButtonText: cancelText,
      customClass: cssClass ? cssClass : ''
    })
    return selectedValue;
  }
}
