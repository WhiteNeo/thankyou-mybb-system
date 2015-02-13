/**
 * Thank You MyBB System + MyAlerts + rep xD v 2.4
 * Upgrade for MyBB 1.6.x Testes since 1.6.3 - (actually 1.6.13)
 * contact: neogeoman@gmail.com
 * Website: http://www.mybb.com
 * Author:  Dark Neo
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
