(function () {
	if (typeof wp === 'undefined' || !wp.blocks || !wp.element) return;
	var registerBlockType = wp.blocks.registerBlockType;
	var createElement = wp.element.createElement;
	var __ = wp.i18n ? wp.i18n.__ : function (s) { return s; };
	var useBlockProps = wp.blockEditor && wp.blockEditor.useBlockProps;

	registerBlockType('bps/polylang-switcher', {
		title: __('Polylang Language Switcher', 'block-polylang-switcher'),
		category: 'widgets',
		description: __('Displays the Polylang language switcher.', 'block-polylang-switcher'),
		keywords: ['polylang', 'language', 'switcher', 'translation'],
		supports: {
			html: false,
			align: ['left', 'right', 'center'],
			className: true,
		},
		edit: function () {
			var blockProps = useBlockProps ? useBlockProps({ className: 'bps-switcher-placeholder' }) : { className: 'bps-switcher-placeholder' };
			return createElement(
				'div',
				Object.assign({ style: { padding: '12px 16px', background: '#f0f0f0', borderRadius: '4px' } }, blockProps),
				__('Polylang Language Switcher — rendered on frontend.', 'block-polylang-switcher')
			);
		},
		save: function () {
			return null;
		},
	});
})();
