function checkGermanText(value){
  value = value.toLowerCase();
  value = value.replace('ä', 'ae');
  value = value.replace('ö', 'oe');
  value = value.replace('ü', 'ue');
  value = value.replace('ß', 'ss');
  value = value.replace('&auml;', 'ae');
  value = value.replace('&Auml;', 'ae');
  value = value.replace('&ouml;', 'oe');
  value = value.replace('&Ouml;', 'oe');
  value = value.replace('&uuml;', 'ue');
  value = value.replace('&Uuml;', 'ue');
  value = value.replace('&slig;', 'ss');
  value = value.replace('&szlig;', 'ss');
  return value;
}

jQuery.extend( jQuery.fn.dataTableExt.oSort, {
  "de_text-asc": function ( a, b ) {
    a = checkGermanText(a);
    b = checkGermanText(b);
    return ((a < b) ? -1 : ((a > b) ? 1 : 0));
  },

  "de_text-desc": function ( a, b ) {
    a = checkGermanText(a);
    b = checkGermanText(b);
    return ((a < b) ? 1 : ((a > b) ? -1 : 0));
  }
} );