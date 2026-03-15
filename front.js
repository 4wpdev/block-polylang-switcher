(function () {
	document.querySelectorAll('.bps-switcher__select[data-bps-switcher]').forEach(function (select) {
		select.addEventListener('change', function () {
			var url = this.value;
			if (url) {
				window.location.href = url;
			}
		});
	});
})();
