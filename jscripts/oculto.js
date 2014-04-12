/**
 * Thank You MyBB System + MyAlerts + rep xD v 2.3.3
 * Upgrade for MyBB 1.6.x (actually 1.6.12)
 * darkneo.skn1.com
 * Author: Dark Neo
 */

messageEditor = Class.create(messageEditor,	{
	initialize: function($super, textarea, options) {
		$super(textarea, options);
	},

	showEditor: function($super) {
		$super();

		this.addToolbarItem('formatting', {
			type: 'button',
			name: 'oculto',
			insert: 'oculto',
			title: this.options.lang.thankyou,
			image: 'oculto.png'
		});
	},

	insertMyCode: function($super, code, extra) {
		if (code == 'oculto' && extra) {	
		this.insertOculto();
		return;
		}
		$super(code, extra);
	},

	insertOculto: function() {
		var text = this.getSelectedText($(this.textarea));
		if (text && text != 'undefined') {
			this.performInsert('[oculto]' + text + '[/oculto]', '',true, false);
			return;
		}
	}
});
