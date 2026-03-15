(function() {
	function updatePrintModalTarget(trigger) {
		var modalSelector = trigger.getAttribute("data-bs-target") || trigger.getAttribute("data-target");
		if (!modalSelector) {
			return;
		}
		var modal = document.querySelector(modalSelector);
		if (!modal) {
			return;
		}
		var continueLink = modal.querySelector("[data-d2u-print-continue]");
		if (continueLink) {
			continueLink.setAttribute("href", trigger.getAttribute("data-d2u-print-target"));
		}
	}

	function substractNumber(numberString) {
		numberString = String(numberString || "").trim();
		numberString = numberString.replace(".", "");
		numberString = numberString.replace(".", "");
		numberString = numberString.replace(",", ".");
		return numberString.replace(/[^\d\.,]/g, "");
	}

	function formatZahl(zahl) {
		var k = 2;
		var neu = "";
		var decPoint = ",";
		var thousandsSep = ".";
		var f = Math.pow(10, k);
		zahl = "" + parseInt(zahl * f + (0.5 * (zahl > 0 ? 1 : -1)), 10) / f;
		var idx = zahl.indexOf(".");
		zahl += (idx === -1 ? "." : "") + f.toString().substring(1);
		var sign = zahl < 0;
		if (sign) {
			zahl = zahl.substring(1);
		}
		idx = zahl.indexOf(".");
		if (idx === -1) {
			idx = zahl.length;
		} else {
			neu = decPoint + zahl.substr(idx + 1, k);
		}
		while (idx > 0) {
			if (idx - 3 > 0) {
				neu = thousandsSep + zahl.substring(idx - 3, idx) + neu;
			} else {
				neu = zahl.substring(0, idx) + neu;
			}
			idx -= 3;
		}
		return (sign ? "-" : "") + neu;
	}

	window.recalc = function() {
		var kaufpreisField = document.getElementById("kaufpreis");
		if (!kaufpreisField) {
			return false;
		}
		var provisionField = document.getElementById("maklerprovision");
		var sonstigesField = document.getElementById("sonstiges");
		var eigenkapitalField = document.getElementById("eigenkapital");
		var zinsField = document.getElementById("zinssatz");
		var tilgungField = document.getElementById("tilgung");
		var grundsteuerField = document.querySelector("#grunderwerbsteuer input[type='hidden']");
		var notarkostenField = document.querySelector("#notar input[type='hidden']");
		var kaufpreis = parseFloat(substractNumber(kaufpreisField.value)) || 0;
		var provision = (parseFloat(substractNumber(provisionField.value)) || 0) / 100;
		var sonstiges = parseFloat(substractNumber(sonstigesField.value)) || 0;
		var eigenkapital = parseFloat(substractNumber(eigenkapitalField.value)) || 0;
		var zins = (parseFloat(substractNumber(zinsField.value)) || 0) / 100;
		var tilgung = (parseFloat(substractNumber(tilgungField.value)) || 0) / 100;
		var grundsteuer = (parseFloat(substractNumber(grundsteuerField ? grundsteuerField.value : 0)) || 0) / 100;
		var notarkosten = (parseFloat(substractNumber(notarkostenField ? notarkostenField.value : 0)) || 0) / 100;
		var gesamtkosten = kaufpreis * (provision + notarkosten + grundsteuer + 1) + sonstiges;
		var darlehen = gesamtkosten - eigenkapital;
		if (darlehen < 0) {
			darlehen = 0;
		}
		kaufpreisField.value = formatZahl(kaufpreis);
		document.getElementById("preis_grunderwerbsteuer").firstChild.nodeValue = formatZahl(kaufpreis * grundsteuer);
		document.getElementById("preis_notar").firstChild.nodeValue = formatZahl(kaufpreis * notarkosten);
		provisionField.value = formatZahl(provision * 100);
		document.getElementById("preis_maklerprovision").firstChild.nodeValue = formatZahl(kaufpreis * provision);
		sonstigesField.value = formatZahl(sonstiges);
		document.getElementById("gesamtkosten").firstChild.nodeValue = formatZahl(gesamtkosten);
		eigenkapitalField.value = formatZahl(eigenkapital);
		document.getElementById("darlehen").firstChild.nodeValue = formatZahl(darlehen);
		zinsField.value = formatZahl(zins * 100);
		tilgungField.value = formatZahl(tilgung * 100);
		document.getElementById("rate").firstChild.nodeValue = formatZahl(darlehen * (zins + tilgung) / 12);
		return false;
	};

	function invalidateMap() {
		if (typeof google !== "undefined" && typeof map !== "undefined") {
			google.maps.event.trigger(map, "resize");
			if (typeof myLatlng !== "undefined") {
				map.setCenter(myLatlng);
			}
			return;
		}
		if (typeof L !== "undefined" && typeof map !== "undefined" && map && typeof map.invalidateSize === "function") {
			L.Util.requestAnimFrame(map.invalidateSize, map, false, map._container);
			return;
		}
		var geoContainer = document.querySelector("[id^='d2u']");
		if (geoContainer && geoContainer.__rmMap && geoContainer.__rmMap.map) {
			geoContainer.__rmMap.map.invalidateSize();
		}
	}

	document.addEventListener("click", function(event) {
		var printTrigger = event.target.closest("[data-d2u-print-modal]");
		if (printTrigger) {
			updatePrintModalTarget(printTrigger);
		}
	});

	var hash = window.location.hash;
	if (hash && typeof bootstrap !== "undefined" && bootstrap.Tab) {
		document.querySelectorAll(".d2u-immo-detail-bs5 [data-bs-toggle='tab']").forEach(function(trigger) {
			if (trigger.getAttribute("data-bs-target") === hash) {
				bootstrap.Tab.getOrCreateInstance(trigger).show();
			}
		});
	}

	document.querySelectorAll(".d2u-immo-detail-bs5 [data-bs-toggle='tab']").forEach(function(trigger) {
		trigger.addEventListener("shown.bs.tab", function(event) {
			if (event.target.getAttribute("data-bs-target") === "#tab_map") {
				invalidateMap();
			}
		});
	});

	if (document.querySelector(".d2u-immo-auto-print")) {
		window.print();
	}
})();