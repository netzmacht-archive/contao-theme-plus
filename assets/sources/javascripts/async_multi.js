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

(function () {
    var queue = [];
    var x = null;
    var loading = false;

    function enqueue(src) {
        if (loading) {
            queue.push(src);
        } else {
            load(src);
        }
    }

    function dequeue() {
        //console.log('dequeue');
        loading = false;
        if (queue.length) {
            var url = queue.shift();
            load(url);
        }
    }

    function load(src) {
        //console.log(src);
        loading = true;

        var s = document.createElement("script");

        s.type = "text/javascript";
        s.async = true;
        s.src = src;

        if (x) {
            if (x.nextSibling) {
                x.parentNode.insertBefore(s, x.nextSibling);
            } else {
                x.parentNode.appendChild(s);
            }
        } else {
            x = document.getElementsByTagName("script")[0];
            x.parentNode.insertBefore(s, x);
        }
        x = s;
    }

    window.loadAsync = function (src) {
        enqueue(src);
    }
})();