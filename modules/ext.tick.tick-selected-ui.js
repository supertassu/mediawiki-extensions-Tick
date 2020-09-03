mw.loader.using(['oojs-ui-core', 'oojs-ui-widgets'], function () {
	console.log('here');
	document.querySelectorAll('.mwe-tick-btn-all')
		.forEach(function (button) {
			var oouiButton = OO.ui.ButtonWidget.static.infuse( button );
			oouiButton.on( 'click', mw.tick.tickSelected );
		});
});
