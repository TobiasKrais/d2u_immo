(function() {
	document.addEventListener("click", function(event) {
		var trigger = event.target.closest("[data-d2u-immo-request-tab='bs5']");
		if (!trigger) {
			return;
		}
		event.preventDefault();
		var requestTab = document.getElementById("tab_request_pill");
		if (requestTab && typeof bootstrap !== "undefined" && bootstrap.Tab) {
			bootstrap.Tab.getOrCreateInstance(requestTab).show();
		}
	});
})();