/*
 * PopSpider scripts
 */

var displayResults = function(id) {
    var div = document.getElementById(id);
    if (div != null) {
        if (div.style.display == 'none') {
            div.style.display = 'block';
        } else {
            div.style.display = 'none';
        }
    }
};