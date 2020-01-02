mw.tick = {
	tick(id) {
		const api = new mw.Api();
		api.postWithEditToken({
			action: 'tick',
			pageid: mw.config.get('wgArticleId'),
			tickid: id,
		})
			.then(mw.tick.loadTickStatues)
			.catch(console.error);
	},
	tickSelected() {
		const ticks = Array.from(document.querySelectorAll('input[type=checkbox][name="tick"]:checked'))
			.map(it => it.value);
		mw.tick.tick(ticks);
	},
	async loadTickStatues() {
		const statusElements = Array.from(document.querySelectorAll('span.tick-status'));

		if (statusElements.length < 1) {
			return;
		}

		const api = new mw.Api();
		const tickStatuses = await api.get({
			action: 'query',
			prop: 'tick',
			pageids: mw.config.get('wgArticleId'),
		});

		if (!tickStatuses.query.tick || tickStatuses.query.tick < 1) {
			return;
		}

		const convertArrayToObject = (array, key) => {
			const initialValue = {};
			return array.reduce((obj, item) => {
				return {
					...obj,
					[item[key]]: item,
				};
			}, initialValue);
		};

		const tickStatusObject = convertArrayToObject(tickStatuses.query.tick, 'given_id');

		statusElements.forEach(elem => {
			const tickId = elem.dataset.tickId;
			const tick = tickStatusObject[tickId];

			if (tick) {
				elem.innerText = tick.last_ticked_in_human_language;
			}
		});
	}
};

mw.tick.loadTickStatues();
