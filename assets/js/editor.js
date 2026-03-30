(function () {
	if (typeof wp === 'undefined' || !wp.blocks || !wp.element) return;
	var registerBlockType = wp.blocks.registerBlockType;
	var createElement = wp.element.createElement;
	var __ = wp.i18n ? wp.i18n.__ : function (s) { return s; };
	var InspectorControls = wp.blockEditor && wp.blockEditor.InspectorControls;
	var PanelBody = wp.components && wp.components.PanelBody;
	var SelectControl = wp.components && wp.components.SelectControl;
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
		attributes: {
			view: {
				type: 'string',
				default: 'dropdown',
			},
		},
		edit: function (props) {
			var view = props.attributes.view || 'dropdown';
			var blockProps = useBlockProps
				? useBlockProps({ className: 'bps-switcher bps-switcher--dropdown is-editor-preview' })
				: { className: 'bps-switcher bps-switcher--dropdown is-editor-preview' };

			var dropdownPreview = createElement(
				'nav',
				Object.assign(
					{
						'aria-label': __('Language switcher', 'block-polylang-switcher'),
						'data-bps-switcher-root': true,
					},
					blockProps
				),
				createElement(
					'button',
					{
						type: 'button',
						className: 'bps-switcher__toggle',
						'aria-haspopup': 'true',
						'aria-expanded': 'false',
					},
					createElement(
						'span',
						{ className: 'bps-switcher__current', 'data-bps-current-lang': 'en' },
						'EN'
					),
					createElement('span', { className: 'bps-switcher__icon', 'aria-hidden': 'true' })
				),
				createElement(
					'ul',
					{ className: 'bps-switcher__list', hidden: true },
					createElement(
						'li',
						{ className: 'bps-switcher__item' },
						createElement(
							'button',
							{
								type: 'button',
								className: 'bps-switcher__option',
								'data-bps-lang': 'de',
							},
							'DE'
						)
					)
				)
			);

			var listPreview = createElement(
				'nav',
				Object.assign(
					{
						'aria-label': __('Language switcher', 'block-polylang-switcher'),
					},
					blockProps
				),
				createElement(
					'ul',
					{ className: 'bps-switcher__list-inline' },
					createElement(
						'li',
						{ className: 'bps-switcher__item bps-switcher__item--current' },
						createElement(
							'span',
							{ className: 'bps-switcher__current', 'data-bps-current-lang': 'en' },
							'EN'
						)
					),
					createElement(
						'li',
						{ className: 'bps-switcher__item' },
						createElement(
							'a',
							{ href: '#', className: 'bps-switcher__link', 'data-bps-lang': 'de' },
							'DE'
						)
					)
				)
			);

			var inspector =
				InspectorControls && PanelBody && SelectControl
					? createElement(
							InspectorControls,
							null,
							createElement(
								PanelBody,
								{ title: __('Display', 'block-polylang-switcher'), initialOpen: true },
								createElement(SelectControl, {
									label: __('View', 'block-polylang-switcher'),
									value: view,
									options: [
										{ label: __('Dropdown', 'block-polylang-switcher'), value: 'dropdown' },
										{ label: __('List', 'block-polylang-switcher'), value: 'list' },
									],
									onChange: function (next) {
										props.setAttributes({ view: next || 'dropdown' });
									},
								})
							)
					  )
					: null;

			return createElement(
				wp.element.Fragment,
				null,
				inspector,
				view === 'list' ? listPreview : dropdownPreview
			);
		},
		save: function () {
			return null;
		},
	});
})();

