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

var Maintenance = function (guestUrls, frontendUsername, memberUrls) {
    var self = this;

    this.initialized      = false;
    this.switchedToUser   = null;
    this.frontendUsername = frontendUsername;
    this.index            = -1;
    this.guestUrls        = guestUrls;
    this.memberUrls       = memberUrls;
    this.url              = null;
    this.indicator        = null;

    this.request = new Request({
        data: {theme_plus_compile_assets: 1},
        method: 'get',
        headers: {
            'X-Requested-With': false
        },
        onRequest: function () {
            self.indicator.addClass('running');
        },
        onSuccess: function () {
            self.indicator.removeClass('running');
            self.indicator.addClass('succeed');

            self.run();
        },
        onFailure: function () {
            self.indicator.removeClass('running');
            self.indicator.addClass('failed');

            self.run();
        }
    });
};

Maintenance.prototype.switchToGuest = function () {
    if (this.switchedToUser === false) {
        // simply continue execution
        this.run();
    }

    var self = this;

    this.url = location.pathname.replace('/main.php', '/switch.php');
    this.indicator = $$('li[data-url="switch_to_guest"]')[0];

    new Request({
        url: this.url,
        data: {
            REQUEST_TOKEN: Contao.request_token,
            FORM_SUBMIT: 'tl_switch',
            unpublished: 'hide',
            user: ''
        },
        method: 'post',
        headers: {
            'X-Requested-With': false
        },
        onRequest: function () {
            self.indicator.addClass('running');
        },
        onSuccess: function () {
            self.indicator.removeClass('running');
            self.indicator.addClass('succeed');

            self.switchedToUser = false;

            self.run();
        },
        onFailure: function () {
            self.indicator.removeClass('running');
            self.indicator.addClass('failed');

            self.switchedToUser = false;

            self.run();
        }
    }).send();
};

Maintenance.prototype.switchToUser = function () {
    if (this.switchedToUser === true) {
        // simply continue execution
        this.run();
    }

    var self = this;

    this.url = location.pathname.replace('/main.php', '/switch.php');
    this.indicator = $$('li[data-url="switch_to_member"]')[0];

    new Request({
        url: this.url,
        data: {
            REQUEST_TOKEN: Contao.request_token,
            FORM_SUBMIT: 'tl_switch',
            unpublished: 'hide',
            user: this.frontendUsername
        },
        method: 'post',
        headers: {
            'X-Requested-With': false
        },
        onRequest: function () {
            self.indicator.addClass('running');
        },
        onSuccess: function () {
            self.indicator.removeClass('running');
            self.indicator.addClass('succeed');

            self.switchedToUser = true;

            self.run();
        },
        onFailure: function () {
            self.indicator.removeClass('running');
            self.indicator.addClass('failed');

            alert('Could not switch member, will continue without frontend member!');

            self.switchedToUser = true;

            self.run();
        }
    }).send();
};

Maintenance.prototype.run = function () {
    if (this.guestUrls.length) {
        if (this.switchedToUser !== false) {
            this.switchToGuest();
        } else {
            this.url = this.guestUrls.shift();
            this.indicator = $$('li[data-url="' + this.url + '"]')[0];

            this.request.send({url: this.url});
        }
    } else if (this.frontendUsername && this.memberUrls.length) {
        if (this.switchedToUser !== true) {
            this.switchToUser();
        } else {
            this.url = this.memberUrls.shift();
            this.indicator = $$('li[data-url="' + this.url + '"]')[0];

            this.request.send({url: this.url});
        }
    }
};
