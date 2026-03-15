(function() {
	var hash = window.location.hash;
	if (!hash || typeof bootstrap === "undefined" || !bootstrap.Tab) {
		return;
	}
	document.querySelectorAll(".d2u-immo-tabs-bs5 [data-bs-toggle='tab']").forEach(function(trigger) {
		if (trigger.getAttribute("data-bs-target") === hash) {
			bootstrap.Tab.getOrCreateInstance(trigger).show();
		}
	});
})();