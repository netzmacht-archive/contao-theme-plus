function initThemePlusDevTool(files, asyncJS) {
	window.themePlusDevTool = new (function(files, asyncJS) {
		var tool = document.getElementById('theme-plus-dev-tool');
		var toggler = document.getElementById('theme-plus-dev-tool-toggler');

		var stylesheets        = document.getElementById('theme-plus-dev-tool-stylesheets');
		var stylesheetsCounter = document.getElementById('theme-plus-dev-tool-stylesheets-counter');
		var stylesheetsCount   = document.getElementById('theme-plus-dev-tool-stylesheets-count');
		var javascripts        = document.getElementById('theme-plus-dev-tool-javascripts');
		var javascriptsCounter = document.getElementById('theme-plus-dev-tool-javascripts-counter');
		var javascriptsCount = document.getElementById('theme-plus-dev-tool-javascripts-count');
		var exception = document.getElementById('theme-plus-dev-tool-exception');

		stylesheetsCounter.addEventListener('click', function() {
			if (toggleClass(stylesheets, 'theme-plus-dev-tool-expanded')) {
				removeClass(javascripts, 'theme-plus-dev-tool-expanded');
			}
		});
		javascriptsCounter.addEventListener('click', function() {
			if (toggleClass(javascripts, 'theme-plus-dev-tool-expanded')) {
				removeClass(stylesheets, 'theme-plus-dev-tool-expanded');
			}
		});
		toggler.addEventListener('click', function() {
			if (toggleClass(tool, 'theme-plus-dev-tool-collapsed')) {
				document.cookie='THEME_PLUS_DEV_TOOL_COLLAPES=yes';
			}
			else {
				document.cookie='THEME_PLUS_DEV_TOOL_COLLAPES=no';
			}
		});

		var documentLoaded = false;
		document.addEventListener('load', function() {
			documentLoaded = true;
		});

		function getClasses(element) {
			var cssClasses = [];
			var tempClasses = element.getAttribute('class');
			if (tempClasses) {
				tempClasses = tempClasses.split(/\s+/);
				for (var index in tempClasses) {
					if (typeof tempClasses[index] == 'string') { // work around mootools $family bullshit -.-
						var tempClass = tempClasses[index].trim();
						if (tempClass) {
							cssClasses.push(tempClass);
						}
					}
				}
			}
			return cssClasses;
		}

		function addClass(element, cssClass) {
			var cssClasses = getClasses(element);
			if (cssClasses.indexOf(cssClass) == -1) {
				cssClasses.push(cssClass);
				element.setAttribute('class', cssClasses.join(' '));
			}
		}

		function removeClass(element, cssClass) {
			var cssClasses = getClasses(element);
			var index = cssClasses.indexOf(cssClass);
			if (index != -1) {
				cssClasses.splice(index, 1);
				element.setAttribute('class', cssClasses.join(' '));
			}
		}

		function toggleClass(element, cssClass) {
			var cssClasses = getClasses(element);
			var index = cssClasses.indexOf(cssClass);
			if (index == -1) {
				cssClasses.push(cssClass);
				element.setAttribute('class', cssClasses.join(' '));
				return true;
			}
			else {
				cssClasses.splice(index, 1);
				element.setAttribute('class', cssClasses.join(' '));
				return false;
			}
		}

		var self = this;
		this.stylesheets = {
			succeeded: [],
			failed: []
		}
		this.javascripts = {
			succeeded: [],
			failed: []
		}
		this.lastError = false;

		function updateStylesheetsCount() {
			stylesheetsCount.innerText = (self.stylesheets.succeeded.length + self.stylesheets.failed.length);

			if (self.stylesheets.failed.length) {
				addClass(stylesheetsCounter, 'theme-plus-dev-tool-errors');
				addClass(toggler, 'theme-plus-dev-tool-errors');
				addClass(tool, 'theme-plus-dev-tool-errors');
			}
		}

		function updateJavascriptsCount() {
			javascriptsCount.innerText = (self.javascripts.succeeded.length + self.javascripts.failed.length);

			if (self.javascripts.failed.length) {
				addClass(javascriptsCounter, 'theme-plus-dev-tool-errors');
				addClass(toggler, 'theme-plus-dev-tool-errors');
				addClass(tool, 'theme-plus-dev-tool-errors');
			}
		}

		window.addEventListener('error', function(event) {
			self.lastError = event;

			var filename = event.filename;
			var lastIndex = filename.lastIndexOf('/');
			if (lastIndex != -1) {
				filename = filename.substr(lastIndex + 1);
			}
			exception.innerHTML = '<img src="system/modules/theme-plus/assets/images/exception.png"> ' +
				'<strong>' + event.message + '</strong>' +
				', in <em>' + filename + ':' + event.lineno + '</em>';

			var cssClass = toggler.getAttribute('class');
			if (!cssClass || !cssClass.match(/theme-plus-dev-tool-errors/)) {
				toggler.setAttribute('class', ((cssClass ? cssClass : '') + ' theme-plus-dev-tool-errors').trim());
			}
		});

		this.triggerAsyncLoad = function(script, hash) {
			var monitor = document.getElementById('monitor-' + hash);
			if (self.lastError) {
				monitor.setAttribute('class', monitor.getAttribute('class') + ' theme-plus-dev-tool-failed');
				self.javascripts.failed.push(hash);
			}
			else {
				monitor.setAttribute('class', monitor.getAttribute('class') + ' theme-plus-dev-tool-finished');
				self.javascripts.succeeded.push(hash);
			}
			self.lastError = false;
			updateJavascriptsCount();
		};

		for (var i=0; i<files.length; i++) {
			var source = document.getElementById(files[i]);
			var monitor = document.getElementById('monitor-' + files[i]);

			if (!source) {

			}
			else if (source.nodeName == 'LINK') {
				(function(id, source, monitor) {
					var interval = setInterval(function() {
						for (var j=0; j<document.styleSheets.length; j++) {
							if (document.styleSheets[j].href == source.href) {
								if (!document.styleSheets[j].rules ||
									document.styleSheets[j].rules.length
								) {
									monitor.setAttribute('class', monitor.getAttribute('class') + ' theme-plus-dev-tool-finished');
									self.stylesheets.succeeded.push(id);
									updateStylesheetsCount();
									clearInterval(interval);
									return;
								}
								else {
									monitor.setAttribute('class', monitor.getAttribute('class') + ' theme-plus-dev-tool-failed');
									self.stylesheets.failed.push(id);
									updateStylesheetsCount()
									clearInterval(interval);
									return;
								}
							}
						}
						if (documentLoaded) {
							monitor.setAttribute('class', monitor.getAttribute('class') + ' theme-plus-dev-tool-failed');
							self.stylesheets.failed.push(id);
							updateStylesheetsCount()
							clearInterval(interval);
						}
					}, 100);
				})(files[i], source, monitor);
			}
			else if (source.nodeName == 'SCRIPT') {
				if (!asyncJS) {
					console.log('sry, the Theme+ dev tool does not support non-async loaded scripts yet');
				}
			}
		}
	})(files, asyncJS);
}