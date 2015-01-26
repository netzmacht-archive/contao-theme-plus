/**
 * This file is part of bit3/contao-theme-plus.
 *
 * (c) Tristan Lins <tristan.lins@bit3.de>
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    bit3/contao-theme-plus
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @copyright  bit3 UG <https://bit3.de>
 * @link       https://github.com/bit3/contao-theme-plus
 * @license    http://opensource.org/licenses/LGPL-3.0 LGPL-3.0+
 * @filesource
 */

var Userlist = function (elementId) {
    if (!document.createElement('datalist') || !window.HTMLDataListElement) {
        console && console.log('HTMLDataList support is not available!');
        return;
    }

    var userInput = $('ctrl_user');
    var userList  = $('userlist');

    var request = new Request.JSON({
        url: location.pathname.replace('/main.php', '/switch.php'),
        onSuccess: function(txt, json) {
            if (userList.empty()) {
                JSON.decode(json).each(function(value) {
                    new Element('option', {'value': value}).inject(userList);
                });
            }
        }
    });

    userInput.addEvent('keyup', function() {
        if (userInput.value.length < 2) {
            return;
        }

        request.post({
            value: userInput.value,
            REQUEST_TOKEN: Contao.request_token
        });
    });
};
