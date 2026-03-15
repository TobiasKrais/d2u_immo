(function() {
	if (typeof jQuery === "undefined") {
		return;
	}
	jQuery(function() {
		var hash = window.location.hash;
		if (hash) {
			jQuery(".d2u-immo-tabs-bs4 ul.nav a[href='" + hash + "']").tab("show");
		}
	});
})();