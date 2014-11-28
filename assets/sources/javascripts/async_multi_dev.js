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

    function enqueue(src, hash) {
        if (loading) {
            queue.push([src, hash]);
        } else {
            load(src, hash);
        }
    }

    function dequeue() {
        //console.log('dequeue');
        loading = false;
        if (queue.length) {
            var item = queue.shift();
            load(item[0], item[1]);
        }
    }

    function load(src, hash) {
        //console.log(src);
        loading = true;

        var s = document.createElement("script");

        s.type = "text/javascript";
        if (window.attachEvent) {
            s.onreadystatechange = function (event) {
                //console.log(this.readyState);
                if (this.readyState == 'loaded' || this.readyState == 'complete') {
                    if (window.themePlusDevTool) {
                        window.themePlusDevTool.triggerAsyncLoad(this, hash);
                    }
                    dequeue();
                }
            };
        } else {
            s.async = true;
            s.addEventListener('load', function (event) {
                if (window.themePlusDevTool) {
                    window.themePlusDevTool.triggerAsyncLoad(this, hash);
                }
                dequeue();
            }, false);
        }
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

    window.loadAsync = function (src, hash) {
        enqueue(src, hash);
    }
})();