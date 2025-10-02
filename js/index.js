(function () {
	'use strict';

	const menuBtn = document.getElementById("toggleSidebar");
	const sidebar = document.getElementById("sidebar");

	menuBtn.addEventListener("click", () => {
		sidebar.classList.toggle("active");
	});

	document.querySelectorAll(".nav-item a").forEach(link => {
		link.addEventListener("click", function () {
			document.querySelectorAll(".nav-item a").forEach(a => a.classList.remove("active"));
			this.classList.add("active");
		});
	});
})();