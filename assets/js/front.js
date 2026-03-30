(function () {
	function initDropdown(root) {
		var toggle = root.querySelector('.bps-switcher__toggle');
		var list = root.querySelector('.bps-switcher__list');
		if (!toggle || !list) return;

		function open() {
			list.hidden = false;
			toggle.setAttribute('aria-expanded', 'true');
			root.classList.add('is-open');
		}

		function close() {
			list.hidden = true;
			toggle.setAttribute('aria-expanded', 'false');
			root.classList.remove('is-open');
		}

		toggle.addEventListener('click', function () {
			if (list.hidden) {
				open();
			} else {
				close();
			}
		});

		list.addEventListener('click', function (event) {
			var btn = event.target.closest('.bps-switcher__option');
			if (!btn) return;
			var url = btn.getAttribute('data-bps-url');
			if (url) {
				window.location.href = url;
			}
		});

		document.addEventListener('click', function (event) {
			if (!root.contains(event.target)) {
				close();
			}
		});
	}

	document.querySelectorAll('[data-bps-switcher-root]').forEach(function (root) {
		initDropdown(root);
	});
})();

