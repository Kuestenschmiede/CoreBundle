/**
 * con4gis - the gis-kit
 *
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */

import swal from 'sweetalert';

export class AlertHandler {
  showErrorDialog(title, content) {
    swal(title, content, "error");
  }

  showInfoDialog(title, content) {
    swal(title, content, "info");
  }
}