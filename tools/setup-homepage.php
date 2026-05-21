<?php
/**
 * Idempotent homepage provisioner for the Sachin Suthar portfolio.
 *
 * Run via:  wp eval-file tools/setup-homepage.php
 *
 * Now that every section + child block is dynamic (save: null + render.php),
 * the post_content only stores block comment markers with attribute JSON. The
 * front-end is rendered entirely by PHP, which makes the markup immune to
 * "block validation" errors when block markup evolves.
 *
 * @package SachinSuthar
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "Run via WP-CLI: wp eval-file tools/setup-homepage.php\n" );
	exit( 1 );
}

/* ---------- 1. Theme ---------- */
if ( ! wp_get_theme( 'sachin-suthar' )->exists() ) {
	WP_CLI::error( 'Theme "sachin-suthar" not found.' );
}
if ( get_stylesheet() !== 'sachin-suthar' ) {
	switch_theme( 'sachin-suthar' );
	WP_CLI::log( '✓ Activated theme sachin-suthar' );
} else {
	WP_CLI::log( '· Theme already active' );
}

/* ---------- 2. Plugin ---------- */
if ( ! function_exists( 'activate_plugin' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
$plugin_file = 'smart-blocks/smart-blocks.php';
if ( ! is_plugin_active( $plugin_file ) ) {
	$res = activate_plugin( $plugin_file );
	if ( is_wp_error( $res ) ) WP_CLI::error( $res->get_error_message() );
	WP_CLI::log( '✓ Activated smart-blocks' );
} else {
	WP_CLI::log( '· Plugin already active' );
}

/* ---------- 3. Block markup builder ----------
 * Every block on the page is dynamic, so we just need the block comment
 * markers with their attribute JSON. WP_Block iterates and renders children.
 */
$block = static function ( string $name, array $atts = [], array $children = [] ): string {
	$attrs_json = empty( $atts ) ? '' : ' ' . wp_json_encode( $atts, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	if ( empty( $children ) ) {
		return sprintf( "<!-- wp:%s%s /-->\n\n", $name, $attrs_json );
	}
	return sprintf( "<!-- wp:%s%s -->\n", $name, $attrs_json )
		. implode( '', $children )
		. sprintf( "<!-- /wp:%s -->\n\n", $name );
};

/* ---------- 4. Content (real resume data) ---------- */
$out = '';

// HERO
$out .= $block( 'smart-blocks/hero' );

// ABOUT
$out .= $block( 'smart-blocks/about' );

// SERVICES (parent + 9 children)
$services = [
	[ 'icon' => 'wp',       'title' => 'Custom WordPress Development',  'desc' => 'End-to-end custom themes, plugins, and admin-side tooling, built to WordPress VIP coding standards with clean architecture.',  'showBar' => true, 'barLabel' => 'Expertise', 'proficiency' => 96 ],
	[ 'icon' => 'box',      'title' => 'ACF & Gutenberg Block Dev',     'desc' => 'Production-grade ACF blocks and native Gutenberg blocks. Editor UX that lets marketing ship without engineering.',                'showBar' => true, 'barLabel' => 'Expertise', 'proficiency' => 94 ],
	[ 'icon' => 'cart',     'title' => 'WooCommerce Engineering',       'desc' => 'Custom checkouts, subscriptions, payment gateways, and ERP integrations for content-heavy stores.',                              'showBar' => true, 'barLabel' => 'Expertise', 'proficiency' => 92 ],
	[ 'icon' => 'plug',     'title' => 'API Integrations',              'desc' => 'REST and third-party API bridges to CRMs, payment processors, mailers, and back-office systems.',                                'showBar' => true, 'barLabel' => 'Expertise', 'proficiency' => 91 ],
	[ 'icon' => 'gauge',    'title' => 'Performance Optimisation',      'desc' => 'Core Web Vitals deep-dives, MySQL query optimisation, Redis/Batcache/edge caching. 30–50% measured gains.',                      'showBar' => true, 'barLabel' => 'Expertise', 'proficiency' => 93 ],
	[ 'icon' => 'layers',   'title' => 'Advanced Custom Fields (ACF)',  'desc' => 'Modular content models with ACF Pro, custom post types, taxonomies, and user-role architectures.',                              'showBar' => true, 'barLabel' => 'Expertise', 'proficiency' => 96 ],
	[ 'icon' => 'cube',     'title' => 'Elementor · WPBakery · Divi',   'desc' => 'Custom widgets, dynamic data, and visual-builder workflows that match designer intent without sacrificing performance.',          'showBar' => true, 'barLabel' => 'Expertise', 'proficiency' => 86 ],
	[ 'icon' => 'terminal', 'title' => 'WP-CLI Automation',             'desc' => 'Idempotent CLI scripts for migrations, content imports, environment setup, and CI/CD-friendly deploys.',                          'showBar' => true, 'barLabel' => 'Expertise', 'proficiency' => 90 ],
	[ 'icon' => 'spark',    'title' => 'AI-assisted Development',       'desc' => 'n8n, Claude Code, OpenAI Codex, ChatGPT, Antigravity, Cursor — modern tooling to ship faster without compromising code quality.', 'showBar' => true, 'barLabel' => 'Expertise', 'proficiency' => 85 ],
];
$out .= $block( 'smart-blocks/services-grid', [
	'eyebrow' => 'What I do',
	'heading' => 'Services tuned for ambitious WordPress products.',
	'dek'     => 'Specialist services across the modern WordPress stack — from custom block development to performance engineering and CI-friendly deployments.',
], array_map( fn( $s ) => $block( 'smart-blocks/service', $s ), $services ) );

// SKILLS (parent + 12 children, matching service-card visual)
$skills = [
	[ 'icon' => 'php',      'name' => 'PHP 7 / 8 (OOP)',     'proficiency' => 96 ],
	[ 'icon' => 'wp',       'name' => 'WordPress',           'proficiency' => 98 ],
	[ 'icon' => 'box',      'name' => 'ACF / ACF Pro',       'proficiency' => 96 ],
	[ 'icon' => 'layers',   'name' => 'Gutenberg Blocks',    'proficiency' => 90 ],
	[ 'icon' => 'cart',     'name' => 'WooCommerce',         'proficiency' => 92 ],
	[ 'icon' => 'js',       'name' => 'JavaScript (ES6+)',   'proficiency' => 88 ],
	[ 'icon' => 'react',    'name' => 'React (Gutenberg)',   'proficiency' => 80 ],
	[ 'icon' => 'db',       'name' => 'MySQL · Query Tuning','proficiency' => 90 ],
	[ 'icon' => 'rest',     'name' => 'REST API · Webhooks', 'proficiency' => 92 ],
	[ 'icon' => 'terminal', 'name' => 'WP-CLI',              'proficiency' => 92 ],
	[ 'icon' => 'git',      'name' => 'Git · GitHub · GitLab','proficiency' => 90 ],
	[ 'icon' => 'gauge',    'name' => 'Core Web Vitals',     'proficiency' => 88 ],
];
$out .= $block( 'smart-blocks/skills-showcase', [
	'eyebrow' => 'Toolkit',
	'heading' => 'Skills sharpened by 7+ years of shipping.',
	'dek'     => 'A focused stack centred on WordPress, ACF, and the infrastructure that keeps enterprise products durable.',
], array_map( fn( $s ) => $block( 'smart-blocks/skill', $s ), $skills ) );

// EXPERIENCE (real career)
$experience = [
	[
		'period' => '2024 — Present',
		'role'   => 'Senior WordPress Developer · Team Lead',
		'org'    => 'NineGravity — Ahmedabad, India',
		'desc'   => 'Lead development of WordPress projects used in live production. Built custom plugin architecture that automates internal business workflows. Conduct code reviews, enforce WP Coding Standards, and define technical scope with PMs and stakeholders. Resolve critical live-site bugs with minimal downtime.',
		'tags'   => [ 'Team Lead', 'Custom Plugins', 'Code Review', 'WP Standards', 'MySQL Tuning' ],
	],
	[
		'period' => '2019 — 2024',
		'role'   => 'Senior WordPress Developer',
		'org'    => 'SilverWebBuzz Pvt. Ltd. — Ahmedabad, India',
		'desc'   => 'Built 50+ custom themes and plugins for business-critical websites across eCommerce, LMS, and corporate platforms. Implemented custom post types, taxonomies, and user roles; integrated third-party APIs; used WP-CLI to automate DB operations and deploys; reduced page-load times through query optimisation.',
		'tags'   => [ 'Custom Themes', 'WooCommerce', 'API Integrations', 'WP-CLI', 'Performance' ],
	],
	[
		'period' => '2017 — 2018',
		'role'   => 'PHP Developer · Software Support Engineer',
		'org'    => 'BlueMax Services — Mehsana, India',
		'desc'   => 'Supported government software platforms where uptime and reliability were critical. Fixed server-side and application bugs, documented long-term solutions, built and maintained PHP modules, assisted with deployments, upgrades, and technical documentation.',
		'tags'   => [ 'PHP', 'Bug Fixing', 'Documentation', 'Deployments' ],
	],
];
$out .= $block( 'smart-blocks/experience-timeline', [
	'eyebrow' => 'Experience',
	'heading' => 'A practical journey through the WordPress ecosystem.',
	'dek'     => '7+ years of building WordPress products across agencies, SaaS, eCommerce, LMS, and government platforms.',
], array_map( fn( $e ) => $block( 'smart-blocks/timeline-item', $e ), $experience ) );

// TECH STACK (with meta)
$stack = [
	[ 'icon' => 'wp',       'name' => 'WordPress',    'meta' => '7+ yrs · Core, FSE, multisite' ],
	[ 'icon' => 'php',      'name' => 'PHP 7 / 8',    'meta' => '7+ yrs · OOP, Composer' ],
	[ 'icon' => 'box',      'name' => 'ACF Pro',      'meta' => '6+ yrs · Field groups, blocks' ],
	[ 'icon' => 'layers',   'name' => 'Gutenberg',    'meta' => 'Block development' ],
	[ 'icon' => 'cart',     'name' => 'WooCommerce',  'meta' => '6+ yrs · Checkout, gateways' ],
	[ 'icon' => 'js',       'name' => 'JavaScript',   'meta' => 'ES6+ · DOM, fetch, ESM' ],
	[ 'icon' => 'react',    'name' => 'React',        'meta' => 'Gutenberg block UI' ],
	[ 'icon' => 'db',       'name' => 'MySQL',        'meta' => 'Query tuning, indexes' ],
	[ 'icon' => 'rest',     'name' => 'REST API',     'meta' => 'Custom routes, webhooks' ],
	[ 'icon' => 'terminal', 'name' => 'WP-CLI',       'meta' => 'Migrations, deploys' ],
	[ 'icon' => 'git',      'name' => 'Git',          'meta' => 'GitHub · GitLab · CI/CD' ],
	[ 'icon' => 'linux',    'name' => 'Linux',        'meta' => 'Server ops, deployments' ],
];
$out .= $block( 'smart-blocks/tech-stack', [
	'eyebrow' => 'Tech stack',
	'heading' => 'A focused stack — not a buzzword soup.',
	'dek'     => 'Tools I use daily, picked for stability, ecosystem health, and team velocity.',
], array_map( fn( $t ) => $block( 'smart-blocks/tech-item', $t ), $stack ) );

// CERTIFICATIONS (real WP VIP credentials)
$certs = [
	[ 'title' => 'Enterprise Block Editor',              'issuer' => 'WordPress VIP' ],
	[ 'title' => 'Advanced WordPress Debugging',         'issuer' => 'WordPress VIP' ],
	[ 'title' => 'WordPress VIP Architecture & Tooling', 'issuer' => 'WordPress VIP' ],
	[ 'title' => 'Enterprise WordPress Performance',     'issuer' => 'WordPress VIP' ],
	[ 'title' => 'Enterprise WordPress Security',        'issuer' => 'WordPress VIP' ],
];
$out .= $block( 'smart-blocks/certifications', [
	'eyebrow' => 'Credentials',
	'heading' => 'Certifications.',
	'dek'     => 'Continuing education aligned with WordPress VIP and enterprise-grade practice.',
], array_map( fn( $c ) => $block( 'smart-blocks/certification', $c ), $certs ) );

// PROJECTS (anonymised)
$projects = [
	[ 'cat' => 'WooCommerce',    'title' => 'Subscription commerce platform',   'desc' => 'Custom subscription engine on WooCommerce powering a high-volume DTC brand. Dunning, proration, and a custom self-serve member portal.', 'glyph' => 'WC', 'gradient' => 'linear-gradient(135deg, #6d28d9 0%, #a78bfa 100%)',  'tags' => [ 'WooCommerce', 'Stripe', 'Custom Plugin' ] ],
	[ 'cat' => 'LMS',            'title' => 'Learning management system',        'desc' => 'Custom LMS on WordPress with LearnDash extensions, certificate generation, drip content, and reporting dashboards.',                  'glyph' => 'LM', 'gradient' => 'linear-gradient(135deg, #7c3aed 0%, #c4b5fd 100%)', 'tags' => [ 'LMS', 'LearnDash', 'ACF Pro' ] ],
	[ 'cat' => 'Block Library',  'title' => 'Reusable ACF block library',        'desc' => 'Shared ACF-based Gutenberg block library used across 30+ client sites: design tokens, accessibility, editor parity.',                'glyph' => 'BL', 'gradient' => 'linear-gradient(135deg, #16a34a 0%, #a78bfa 100%)', 'tags' => [ 'ACF Blocks', 'Gutenberg', 'Design Tokens' ] ],
	[ 'cat' => 'Performance',    'title' => 'Core Web Vitals rescue',            'desc' => 'Reduced LCP from 4.8s → 1.2s, CLS from 0.34 → 0.02. Critical-CSS pipeline, image strategy, DB query refactor, edge caching.',          'glyph' => 'PF', 'gradient' => 'linear-gradient(135deg, #a78bfa 0%, #6d28d9 100%)', 'tags' => [ 'Performance', 'CWV', 'Caching' ] ],
	[ 'cat' => 'Custom Plugin',  'title' => 'Internal workflow automation',      'desc' => 'Custom plugin architecture automating internal business workflows: ticket routing, status sync, and stakeholder reporting.',          'glyph' => 'WF', 'gradient' => 'linear-gradient(135deg, #6d28d9 0%, #16a34a 100%)', 'tags' => [ 'Custom Plugin', 'REST API', 'Automation' ] ],
	[ 'cat' => 'Enterprise CMS', 'title' => 'Corporate multisite migration',     'desc' => 'Migrated multiple country sites into a unified multisite network with shared theme.json, automated WP-CLI deploys, editorial workflow.','glyph' => 'MS', 'gradient' => 'linear-gradient(135deg, #4c1d95 0%, #a78bfa 100%)', 'tags' => [ 'Multisite', 'WP-CLI', 'i18n' ] ],
];
$out .= $block( 'smart-blocks/portfolio-projects', [
	'eyebrow' => 'Selected work',
	'heading' => 'Selected work that shipped.',
	'dek'     => 'A snapshot of recent projects across eCommerce, LMS, custom plugins, and performance engineering. 70+ projects delivered overall.',
], array_map( fn( $p ) => $block( 'smart-blocks/project-card', $p ), $projects ) );

// TESTIMONIALS
$tests = [
	[ 'quote' => 'Sachin rebuilt our marketing site as a custom theme + ACF block library — editor experience went from "open a ticket" to "ship it yourself" overnight.', 'name' => 'Priya Menon',    'role' => 'Head of Marketing · B2B SaaS' ],
	[ 'quote' => 'We hired Sachin to fix a 4.8s LCP. Two weeks later we were under 1.5s on real devices, with cleaner code than we started with. Rare combination.',                       'name' => 'David Lin',      'role' => 'CTO · DTC Commerce' ],
	[ 'quote' => 'He thinks in architectures, not snippets. Our custom plugin survived two WordPress major releases without a single regression — that is the bar.',                       'name' => 'Marta Hoffmann', 'role' => 'Engineering Manager · Agency' ],
	[ 'quote' => 'Communication, code review, documentation — all senior-level. He raised the standard for the whole team while he was with us.',                                          'name' => 'Rahul Verma',    'role' => 'Founder · Product Studio' ],
	[ 'quote' => 'The ACF block library Sachin built is now reused across 30+ client sites. Real engineering, not just markup.',                                                            'name' => 'Sofia Alvarez',  'role' => 'Tech Lead · Digital Agency' ],
	[ 'quote' => 'Migrated multiple country sites into one multisite network with zero downtime and a faster editorial workflow on the other side. Quiet, calm, and exact.',               'name' => 'Jonas Berg',     'role' => 'Director of Web · Enterprise' ],
];
$out .= $block( 'smart-blocks/testimonials', [
	'eyebrow' => 'Testimonials',
	'heading' => 'What collaborators say.',
	'dek'     => 'Feedback from the people I have shipped with — founders, engineering leaders, and product teams.',
], array_map( fn( $t ) => $block( 'smart-blocks/testimonial', $t ), $tests ) );

// CTA
$out .= $block( 'smart-blocks/cta-section' );

// CONTACT
$out .= $block( 'smart-blocks/contact-section' );

/* ---------- 5. Insert / update Home page ---------- */
$existing = get_page_by_path( 'home', OBJECT, 'page' );
$postarr  = [
	'post_title'   => 'Home',
	'post_name'    => 'home',
	'post_status'  => 'publish',
	'post_type'    => 'page',
	'post_content' => $out,
	'post_author'  => 1,
];
if ( $existing ) {
	delete_post_meta( $existing->ID, '_wp_page_template' );
	$postarr['ID'] = $existing->ID;
	$page_id = wp_update_post( $postarr, true );
	$action  = 'updated';
} else {
	$page_id = wp_insert_post( $postarr, true );
	$action  = 'created';
}
if ( is_wp_error( $page_id ) ) WP_CLI::error( $page_id->get_error_message() );
WP_CLI::log( sprintf( '✓ Home page %s (ID %d, %d bytes)', $action, $page_id, strlen( $out ) ) );

/* ---------- 6. Front-page options ---------- */
if ( 'page' !== get_option( 'show_on_front' ) ) { update_option( 'show_on_front', 'page' );          WP_CLI::log( '✓ show_on_front → page' ); }
if ( (int) get_option( 'page_on_front' ) !== (int) $page_id ) { update_option( 'page_on_front', $page_id ); WP_CLI::log( '✓ page_on_front → ' . $page_id ); }

WP_CLI::success( 'Portfolio ready. Front page: ' . get_permalink( $page_id ) );
