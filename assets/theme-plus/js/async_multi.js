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