function initThemePlusDevTool(files, asyncJS) {
	window.themePlusDevTool = new (function(files, asyncJS) {
		var tool = document.getElementById('theme-plus-dev-tool');
		var counter = document.getElementById('theme-plus-dev-tool-counter');
		var count = document.getElementById('theme-plus-dev-tool-count');

		counter.addEventListener('click', function() {
			var cssClass = tool.getAttribute('class');
			if (cssClass.match(/theme-plus-dev-tool-collapsed/)) {
				document.cookie='THEME_PLUS_DEV_TOOL_COLLAPES=no';
				cssClass = cssClass.replace('theme-plus-dev-tool-collapsed', '');
			}
			else {
				document.cookie='THEME_PLUS_DEV_TOOL_COLLAPES=yes';
				cssClass = (cssClass + ' theme-plus-dev-tool-collapsed').trim();
			}
			tool.setAttribute('class', cssClass);
		});

		var documentLoaded = false;
		document.addEventListener('load', function() {
			documentLoaded = true;
		});

		var self = this;
		this.succeeded = [];
		this.failed = [];
		this.lastError = false;

		function updateCount() {
			count.innerText = (self.succeeded.length + self.failed.length);

			var cssClass = counter.getAttribute('class');
			if (self.failed.length && (!cssClass || !cssClass.match(/theme-plus-dev-tool-errors/))) {
				counter.setAttribute('class', (cssClass + ' theme-plus-dev-tool-errors').trim());
			}
		}

		window.addEventListener('error', function(event) {
			self.lastError = event;
		});
		this.triggerAsyncLoad = function(script, hash) {
			var monitor = document.getElementById('monitor-' + hash);
			if (self.lastError) {
				monitor.setAttribute('class', monitor.getAttribute('class') + ' theme-plus-dev-tool-failed');
				self.failed.push(hash);
			}
			else {
				monitor.setAttribute('class', monitor.getAttribute('class') + ' theme-plus-dev-tool-finished');
				self.succeeded.push(hash);
			}
			self.lastError = false;
			updateCount();
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
									self.succeeded.push(id);
									updateCount();
									clearInterval(interval);
									return;
								}
								else {
									monitor.setAttribute('class', monitor.getAttribute('class') + ' theme-plus-dev-tool-failed');
									self.failed.push(id);
									updateCount()
									clearInterval(interval);
									return;
								}
							}
						}
						if (documentLoaded) {
							monitor.setAttribute('class', monitor.getAttribute('class') + ' theme-plus-dev-tool-failed');
							self.failed.push(id);
							updateCount()
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