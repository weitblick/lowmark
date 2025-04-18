	let lastModified = null;
	const page = window.location.pathname.replace(/^\//, "");

	async function checkForChanges() {
		try {
			const response = await fetch("/local/livereload.php?q=" + page);
			const newTimestamp = await response.text();

			if (lastModified && lastModified !== newTimestamp) {
				location.reload();
			}
			lastModified = newTimestamp;
		} catch (e) {
			console.error("Live-Reload Error:", e);
		}
	}

	// check every two seconds
	setInterval(checkForChanges, 2000);
