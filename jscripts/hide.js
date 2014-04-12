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
			name: 'hide',
			insert: 'hide',
			title: this.options.lang.thankyou,
			image: 'hide.png'
		});
	},

	insertMyCode: function($super, code, extra) {
		if (code == 'hide' && extra) {
			this.insertHide();
			return;
			}
			$super(code, extra);
	},

	insertHide: function() {
		var text = this.getSelectedText($(this.textarea));
		if (text && text != 'undefined') {
			this.performInsert('[hide]' + text + '[/hide]', '', true, false);
			return;
		}
		$super(code, extra);	
	}
});
