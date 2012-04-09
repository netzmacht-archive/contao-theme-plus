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
			var src = queue.shift();
			load(src);
		}
	}

	function load(src) {
		//console.log(src);
		loading = true;

		var s = document.createElement("script");

		s.type = "text/javascript";
		if (window.attachEvent) {
			s.onreadystatechange = function () {
				//console.log(this.readyState);
				if (this.readyState == 'loaded' || this.readyState == 'complete') dequeue();
			};
		} else {
			s.async = true;
			s.addEventListener('load', dequeue, false);
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

	window.loadAsync = function (src) {
		enqueue(src);
	}
})();