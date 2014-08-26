(function () {
	window.loadAsync = function (src) {
		var s = document.createElement("script");

		s.type = "text/javascript";
		s.async = true;
		s.src = src;

		x = document.getElementsByTagName("script")[0];
		x.parentNode.insertBefore(s, x);
	}
})();