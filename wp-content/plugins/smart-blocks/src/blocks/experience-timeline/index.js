import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';
import metadata from './block.json';
import SectionHead from '../../shared/section-head';
import './style.scss';
import './editor.scss';

const ALLOWED = [ 'smart-blocks/timeline-item' ];
const TEMPLATE = [
	[ 'smart-blocks/timeline-item', {
		period: '2024 — Present',
		role:   'Senior WordPress Developer · Team Lead',
		org:    'NineGravity — Ahmedabad, India',
		desc:   'Lead development of WordPress projects used in live production environments. Built custom plugin architecture that automates internal business workflows. Conduct code reviews, enforce WordPress Coding Standards, and define technical scope with PMs and stakeholders. Resolve critical live-site bugs with minimal downtime.',
		tags:   [ 'Team Lead', 'Custom Plugins', 'Code Review', 'WP Coding Standards', 'MySQL Tuning' ],
	} ],
	[ 'smart-blocks/timeline-item', {
		period: '2019 — 2024',
		role:   'Senior WordPress Developer',
		org:    'SilverWebBuzz Pvt. Ltd. — Ahmedabad, India',
		desc:   'Built 50+ custom themes and plugins for business-critical client websites across eCommerce, LMS, and corporate platforms. Implemented custom post types, taxonomies, and user roles for content-heavy projects. Integrated third-party APIs and reduced page load times through DB query optimisation. Used WP-CLI to automate database operations and deployments.',
		tags:   [ 'Custom Themes', 'WooCommerce', 'API Integrations', 'WP-CLI', 'Performance' ],
	} ],
	[ 'smart-blocks/timeline-item', {
		period: '2017 — 2018',
		role:   'PHP Developer · Software Support Engineer',
		org:    'BlueMax Services — Mehsana, India',
		desc:   'Supported government software platforms where uptime and reliability were critical. Fixed server-side and application bugs, documented long-term solutions, built and maintained PHP modules, and assisted with deployments, upgrades, and technical documentation.',
		tags:   [ 'PHP', 'Bug Fixing', 'Documentation', 'Deployments' ],
	} ],
];

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps( { className: 'sb-section sb-experience' } );
		const innerProps = useInnerBlocksProps(
			{ className: 'sb-timeline' },
			{ allowedBlocks: ALLOWED, template: TEMPLATE, orientation: 'vertical' }
		);
		return (
			<section { ...blockProps }>
				<div className="sb-container">
					<SectionHead attributes={ attributes } setAttributes={ setAttributes } />
					<ol { ...innerProps } />
				</div>
			</section>
		);
	},
	save: () => null,
} );
