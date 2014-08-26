(function () {
	window.loadAsync = function (src, hash) {
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
			s.addEventListener('load', function(event) {
				if (window.themePlusDevTool) {
					window.themePlusDevTool.triggerAsyncLoad(this, hash);
				}
				dequeue();
			}, false);
		}
		s.src = src;

		x = document.getElementsByTagName("script")[0];
		x.parentNode.insertBefore(s, x);
	}
})();