mw.tick = {};

mw.tick.tick = function tick(id) {
	if (!id) {
		// no ID; don't do anything
		return;
	}

	var api = new mw.Api();
	api.postWithEditToken({
		action: 'tick',
		pageid: mw.config.get('wgArticleId'),
		tickid: id,
	})
		.then(mw.tick.loadTickStatuses)
		.catch(console.error);
};

mw.tick.tickSelected = function tickSelected() {
	var ticks = Array
		.from(document.querySelectorAll('input[type=checkbox][name="tick"]:checked'))
		.map(function (it) {
			return it.value;
		});
	console.log('ticking', ticks);
	mw.tick.tick(ticks);
};

mw.tick.loadTickStatuses = function loadTickStatuses() {
	var statusElements = Array.from(document.querySelectorAll('span.tick-status'));

	if (statusElements.length < 1) {
		return;
	}

	var api = new mw.Api();
	api.get({
		action: 'query',
		prop: 'tick',
		pageids: mw.config.get('wgArticleId'),
	})
		.then(function (tickStatuses) {
			if (!tickStatuses.query.tick || tickStatuses.query.tick < 1) {
				return;
			}

			console.log('found statuses: ', tickStatuses);

			function convertArrayToObject(array, key) {
				var initialValue = {};
				return array.reduce(function (obj, item) {
					return {
						...obj,
						[item[key]]: item,
					};
				}, initialValue);
			}

			var tickStatusObject = convertArrayToObject(tickStatuses.query.tick, 'given_id');
			statusElements.forEach(function (elem) {
				const tickId = elem.dataset.tickId;
				const tick = tickStatusObject[tickId];

				if (tick) {
					elem.innerText = tick.last_ticked_in_human_language;
				}
			});
		})
		.catch(console.error);
};

mw.tick.loadTickStatuses();
