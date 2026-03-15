(function() {
	document.addEventListener("click", function(event) {
		var trigger = event.target.closest("[data-d2u-immo-request-tab='bs4']");
		if (!trigger || typeof jQuery === "undefined") {
			return;
		}
		event.preventDefault();
		jQuery("#tab_request_pill").tab("show");
	});
})();