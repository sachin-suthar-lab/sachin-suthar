<?php
/**
 * Idempotent homepage provisioner for the Sachin Suthar portfolio.
 *
 * Run via:  wp eval-file tools/setup-homepage.php
 *
 * Activates the theme + plugin, then writes a Home page seeded with the full
 * native-block markup for the 9 sections (parents + their child InnerBlocks).
 * Each section is fully editable in Gutenberg after activation — the seed is
 * just the starter content so the front-end renders immediately.
 *
 * @package SachinSuthar
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "This script must be run via WP-CLI: wp eval-file tools/setup-homepage.php\n" );
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

/* ---------- 3. Block markup builders ----------
 * Tiny helpers so the homepage content stays readable. Each returns a string
 * of properly-formatted block comment markup.
 */
$attrs = static fn( array $a ): string => empty( $a ) ? '' : ' ' . wp_json_encode( $a, JSON_UNESCAPED_SLASHES );

$dyn = static fn( string $name, array $a = [] ): string =>
	sprintf( "<!-- wp:%s%s /-->\n", $name, $attrs( $a ) );

/** Wrap a static-save section: opening parent block + section head + inner wrapper + children + closes. */
$section_static = static function ( string $name, array $atts, string $innerTag, string $innerClass, array $childMarkup, string $extraSectionClass = '' ) use ( $attrs ): string {
	// "smart-blocks/services-grid" → "sb-services" used by shared.scss layout rules.
	$short = preg_replace( '/-(grid|showcase|timeline|stack|projects)$/', '', basename( str_replace( '/', '-', $name ) ) );
	$short = preg_replace( '/^smart-blocks-/', 'sb-', $short );
	$cls   = trim( 'sb-section ' . $short . ' ' . $extraSectionClass . ' sb-reveal' );
	$out  = sprintf( "<!-- wp:%s%s -->\n", $name, $attrs( $atts ) );
	$out .= sprintf( '<section class="wp-block-%s %s">', str_replace( '/', '-', $name ), $cls );
	$out .= '<div class="sb-section-head">';
	if ( ! empty( $atts['eyebrow'] ) ) { $out .= '<span class="sb-eyebrow">' . esc_html( $atts['eyebrow'] ) . '</span>'; }
	if ( ! empty( $atts['heading'] ) ) { $out .= '<h2>' . esc_html( $atts['heading'] ) . '</h2>'; }
	if ( ! empty( $atts['dek'] ) )     { $out .= '<p>'  . esc_html( $atts['dek'] )     . '</p>';  }
	$out .= '</div>';
	$out .= sprintf( '<%s class="%s">', $innerTag, $innerClass );
	$out .= implode( '', $childMarkup );
	$out .= sprintf( '</%s>', $innerTag );
	$out .= '</section>';
	$out .= sprintf( "\n<!-- /wp:%s -->\n\n", $name );
	return $out;
};

/* ---------- 4. Build content ---------- */
$out  = '';

// HERO
$out .= $dyn( 'smart-blocks/hero' );

// SERVICES
$service_items = [
	[ 'icon' => 'wp',       'title' => 'Custom WordPress Development', 'desc' => 'Custom themes, plugins, and bespoke admin tools — architected for long-term maintainability and zero technical debt.' ],
	[ 'icon' => 'layers',   'title' => 'Gutenberg Block Development',  'desc' => 'Production-grade native and dynamic blocks with React, block.json, server-side rendering, and editor parity.' ],
	[ 'icon' => 'cart',     'title' => 'WooCommerce Engineering',      'desc' => 'High-conversion stores, custom checkout flows, subscription logic, and ERP / payment gateway integrations.' ],
	[ 'icon' => 'plug',     'title' => 'Headless WordPress',           'desc' => 'WordPress as a content API powering Next.js, React, and mobile front-ends, with auth, ISR, and caching done right.' ],
	[ 'icon' => 'gauge',    'title' => 'Performance Optimisation',     'desc' => 'Core Web Vitals deep-dives, query profiling, asset budgets, and a measurable path to 90+ Lighthouse on real hardware.' ],
	[ 'icon' => 'cube',     'title' => 'Elementor & Bricks',           'desc' => 'Custom widgets, dynamic data, and clean visual builder workflows that ship designer intent without bloat.' ],
	[ 'icon' => 'rest',     'title' => 'API Integrations',             'desc' => 'REST, GraphQL, and webhook bridges to CRMs, payment processors, mailers, and internal back-office systems.' ],
	[ 'icon' => 'box',      'title' => 'Advanced Custom Fields',       'desc' => 'Modular content models, ACF Pro field groups, and editor experiences that let marketing ship without engineering.' ],
	[ 'icon' => 'terminal', 'title' => 'WP-CLI Automation',            'desc' => 'Idempotent CLI scripts for migrations, content imports, environment setup, and CI/CD-friendly deploys.' ],
];
$out .= $section_static(
	'smart-blocks/services-grid',
	[ 'eyebrow' => 'What I do', 'heading' => 'Services tuned for ambitious WordPress products.', 'dek' => 'Specialist services across the modern WordPress stack — from custom block development to headless architectures and performance engineering.' ],
	'div', 'sb-services__grid',
	array_map( static fn( $i ) => $dyn( 'smart-blocks/service', $i ), $service_items )
);

// SKILLS
$skill_items = [
	[ 'name' => 'PHP', 'proficiency' => 96 ], [ 'name' => 'WordPress', 'proficiency' => 98 ],
	[ 'name' => 'Gutenberg', 'proficiency' => 94 ], [ 'name' => 'JavaScript', 'proficiency' => 92 ],
	[ 'name' => 'React', 'proficiency' => 88 ], [ 'name' => 'WooCommerce', 'proficiency' => 90 ],
	[ 'name' => 'MySQL', 'proficiency' => 86 ], [ 'name' => 'REST API', 'proficiency' => 92 ],
	[ 'name' => 'Tailwind CSS', 'proficiency' => 85 ], [ 'name' => 'Git', 'proficiency' => 90 ],
	[ 'name' => 'Linux', 'proficiency' => 82 ], [ 'name' => 'WP-CLI', 'proficiency' => 90 ],
];
$skill_markup = array_map( static function ( $s ) use ( $attrs ) {
	$pct = (int) $s['proficiency'];
	return sprintf(
		'<!-- wp:smart-blocks/skill%s --><div class="wp-block-smart-blocks-skill sb-skill"><div class="sb-skill__head"><span class="sb-skill__name">%s</span><span class="sb-skill__pct">%d%%</span></div><div class="sb-skill__bar" role="progressbar" aria-valuenow="%d" aria-valuemin="0" aria-valuemax="100" aria-label="%s"><div class="sb-skill__fill" style="width:%d%%"></div></div></div><!-- /wp:smart-blocks/skill -->',
		$attrs( $s ), esc_html( $s['name'] ), $pct, $pct, esc_attr( $s['name'] ), $pct
	);
}, $skill_items );
$out .= $section_static(
	'smart-blocks/skills-showcase',
	[ 'eyebrow' => 'Toolkit', 'heading' => 'Skills sharpened by 8 years of shipping.', 'dek' => 'A focused stack centred on WordPress, modern JavaScript, and the surrounding infrastructure that makes products durable.' ],
	'div', 'sb-skills__grid', $skill_markup, 'sb-section--alt'
);

// EXPERIENCE
$exp_items = [
	[ 'period' => '2022 — Present', 'role' => 'Senior WordPress Engineer · Freelance', 'org' => 'SaaS, agencies & enterprise clients · Remote', 'desc' => 'Lead WordPress engineer on headless and FSE builds for SaaS marketing sites, agency clients, and enterprise CMS migrations. Architecting custom block libraries, performance-tuned WooCommerce stores, and CI-friendly WP-CLI workflows.', 'tags' => [ 'FSE Themes', 'Gutenberg Blocks', 'Headless', 'Performance' ] ],
	[ 'period' => '2020 — 2022', 'role' => 'WordPress Tech Lead', 'org' => 'Digital product studio', 'desc' => 'Led a team of four building custom plugins and themes for venture-backed SaaS startups. Owned Gutenberg block architecture, code review standards, and release engineering. Delivered 40+ shipped products with measurable Core Web Vitals improvements.', 'tags' => [ 'Team Lead', 'Plugin Architecture', 'Code Review', 'CI/CD' ] ],
	[ 'period' => '2018 — 2020', 'role' => 'Senior WordPress Developer', 'org' => 'Boutique agency · Remote / Hybrid', 'desc' => 'WooCommerce-heavy practice: custom checkout flows, subscription billing, ERP integrations, and multilingual storefronts. Migrated legacy classic-editor sites to Gutenberg with custom block parity.', 'tags' => [ 'WooCommerce', 'Custom Plugins', 'Migrations', 'ACF Pro' ] ],
	[ 'period' => '2017 — 2018', 'role' => 'WordPress Developer', 'org' => 'Product-led startup', 'desc' => 'First in-house WordPress hire. Built the marketing platform, custom block library, REST integrations with the product app, and a publish workflow that took editors from idea to live in minutes.', 'tags' => [ 'Custom Themes', 'REST API', 'Editor UX' ] ],
];
$exp_markup = array_map( static function ( $e ) use ( $attrs ) {
	$tags_html = '';
	foreach ( $e['tags'] as $t ) { $tags_html .= '<span class="sb-tag">' . esc_html( $t ) . '</span>'; }
	return sprintf(
		'<!-- wp:smart-blocks/timeline-item%s --><li class="wp-block-smart-blocks-timeline-item sb-timeline-item"><div class="sb-timeline-item__dot" aria-hidden="true"></div><span class="sb-timeline-item__period">%s</span><h3 class="sb-timeline-item__role">%s</h3><p class="sb-timeline-item__org">%s</p><p class="sb-timeline-item__desc">%s</p><div class="sb-timeline-item__tags">%s</div></li><!-- /wp:smart-blocks/timeline-item -->',
		$attrs( $e ),
		esc_html( $e['period'] ), esc_html( $e['role'] ), esc_html( $e['org'] ),
		esc_html( $e['desc'] ), $tags_html
	);
}, $exp_items );
$out .= $section_static(
	'smart-blocks/experience-timeline',
	[ 'eyebrow' => 'Experience', 'heading' => 'A practical journey through the WordPress ecosystem.', 'dek' => 'Eight years of building WordPress products across SaaS, agencies, e-commerce, and enterprise CMS programmes.' ],
	'ol', 'sb-timeline', $exp_markup
);

// TECH STACK
$tech_items = [
	[ 'icon' => 'wp', 'name' => 'WordPress' ], [ 'icon' => 'php', 'name' => 'PHP' ],
	[ 'icon' => 'js', 'name' => 'JavaScript' ], [ 'icon' => 'react', 'name' => 'React' ],
	[ 'icon' => 'layers', 'name' => 'Gutenberg' ], [ 'icon' => 'cart', 'name' => 'WooCommerce' ],
	[ 'icon' => 'db', 'name' => 'MySQL' ], [ 'icon' => 'tailwind', 'name' => 'Tailwind' ],
	[ 'icon' => 'rest', 'name' => 'REST API' ], [ 'icon' => 'git', 'name' => 'Git' ],
	[ 'icon' => 'linux', 'name' => 'Linux' ], [ 'icon' => 'terminal', 'name' => 'WP-CLI' ],
];
$out .= $section_static(
	'smart-blocks/tech-stack',
	[ 'eyebrow' => 'Tech stack', 'heading' => 'A focused stack — not a buzzword soup.', 'dek' => 'The tools I actually use day-to-day, picked for stability, ecosystem health, and team velocity.' ],
	'div', 'sb-tech__grid',
	array_map( static fn( $t ) => $dyn( 'smart-blocks/tech-item', $t ), $tech_items )
);

// PROJECTS
$proj_items = [
	[ 'cat' => 'WooCommerce',    'title' => 'Subscription commerce platform',         'desc' => 'Custom subscription engine on WooCommerce powering a $4M ARR DTC brand. Dunning, proration, and a custom self-serve member portal.', 'glyph' => 'WC', 'gradient' => 'linear-gradient(135deg, #7c5cff 0%, #f0abfc 100%)',  'tags' => [ 'WooCommerce', 'Stripe', 'Custom Plugin' ] ],
	[ 'cat' => 'Headless',       'title' => 'Headless WP + Next.js marketing site',   'desc' => 'Editorial WordPress back-end powering a Next.js front-end with ISR, on-demand revalidation, and a custom block-to-component pipeline.',  'glyph' => 'NX', 'gradient' => 'linear-gradient(135deg, #11121a 0%, #22d3ee 130%)', 'tags' => [ 'Next.js', 'GraphQL', 'ISR' ] ],
	[ 'cat' => 'Block Library',  'title' => 'Agency-wide Gutenberg block library',    'desc' => 'A shared block library used across 30+ client sites: design tokens, accessibility baked in, ServerSideRender for editor parity.',         'glyph' => 'GB', 'gradient' => 'linear-gradient(135deg, #34d399 0%, #22d3ee 100%)', 'tags' => [ 'Gutenberg', 'React', 'Design Tokens' ] ],
	[ 'cat' => 'Performance',    'title' => 'Core Web Vitals rescue for a SaaS site', 'desc' => 'LCP from 4.8s → 1.2s, CLS from 0.34 → 0.02. Custom critical-CSS pipeline, image strategy, and DB query refactor.',                       'glyph' => 'PF', 'gradient' => 'linear-gradient(135deg, #f0abfc 0%, #7c5cff 100%)', 'tags' => [ 'Performance', 'CWV', 'DB Tuning' ] ],
	[ 'cat' => 'Custom Plugin',  'title' => 'Real-estate listings & MLS sync',        'desc' => 'Custom listings plugin with nightly MLS RETS sync, map clustering, and saved-search emails. Handled 80k+ properties.',                    'glyph' => 'RE', 'gradient' => 'linear-gradient(135deg, #22d3ee 0%, #7c5cff 100%)', 'tags' => [ 'Custom Plugin', 'Cron', 'REST' ] ],
	[ 'cat' => 'Enterprise',     'title' => 'Multi-site network migration',           'desc' => 'Migrated 14 country sites into a unified multisite network with shared theme.json, automated WP-CLI deploys, and an editorial workflow.',     'glyph' => 'MS', 'gradient' => 'linear-gradient(135deg, #11121a 0%, #7c5cff 130%)', 'tags' => [ 'Multisite', 'WP-CLI', 'i18n' ] ],
];
$proj_markup = array_map( static function ( $p ) use ( $attrs ) {
	$tags_html = '';
	foreach ( $p['tags'] as $t ) { $tags_html .= '<span class="sb-tag">' . esc_html( $t ) . '</span>'; }
	return '<!-- wp:smart-blocks/project-card' . $attrs( $p ) . ' -->'
		. '<article class="wp-block-smart-blocks-project-card sb-project">'
		. '<div class="sb-project__cover" style="--cover:' . esc_attr( $p['gradient'] ) . ';">'
		. '<span class="sb-project__cover-glyph">' . esc_html( $p['glyph'] ) . '</span>'
		. '</div>'
		. '<div class="sb-project__body">'
		. '<span class="sb-project__cat">' . esc_html( $p['cat'] ) . '</span>'
		. '<h3 class="sb-project__title">' . esc_html( $p['title'] ) . '</h3>'
		. '<p class="sb-project__desc">' . esc_html( $p['desc'] ) . '</p>'
		. '<div class="sb-project__tags">' . $tags_html . '</div>'
		. '</div>'
		. '</article>'
		. '<!-- /wp:smart-blocks/project-card -->';
}, $proj_items );
$out .= $section_static(
	'smart-blocks/portfolio-projects',
	[ 'eyebrow' => 'Selected work', 'heading' => 'Selected work that shipped.', 'dek' => 'A snapshot of recent projects across e-commerce, headless, custom plugins, and performance work.' ],
	'div', 'sb-projects__grid', $proj_markup
);

// TESTIMONIALS
$test_items = [
	[ 'quote' => 'Sachin rebuilt our marketing site as an FSE theme and a small custom block library — the editor experience went from "open a ticket" to "ship it yourself" overnight.', 'name' => 'Priya Menon',    'role' => 'Head of Marketing · B2B SaaS' ],
	[ 'quote' => 'We hired Sachin to fix a 4.8s LCP. Two weeks later we were under 1.5s on real devices, with cleaner code than we started with. Rare combination.',                     'name' => 'David Lin',      'role' => 'CTO · DTC Commerce' ],
	[ 'quote' => 'He thinks in architectures, not snippets. Our custom plugin survived two WordPress major releases without a single regression — that is the bar.',                     'name' => 'Marta Hoffmann', 'role' => 'Engineering Manager · Agency' ],
	[ 'quote' => 'Communication, code review, documentation — all senior-level. He raised the standard for the whole team while he was with us.',                                       'name' => 'Rahul Verma',    'role' => 'Founder · Product Studio' ],
	[ 'quote' => 'The Gutenberg block library Sachin built is now reused across 30+ client sites. Real engineering, not just markup.',                                                    'name' => 'Sofia Alvarez',  'role' => 'Tech Lead · Digital Agency' ],
	[ 'quote' => 'Migrated 14 country sites into one multisite network with zero downtime and a faster editorial workflow on the other side. Quiet, calm, and exact.',                  'name' => 'Jonas Berg',     'role' => 'Director of Web · Enterprise' ],
];
$initials = static function ( string $name ): string {
	$parts = preg_split( '/\s+/', trim( $name ) );
	return strtoupper( ( $parts[0][0] ?? '' ) . ( $parts[1][0] ?? '' ) );
};
$test_markup = array_map( static function ( $t ) use ( $attrs, $initials ) {
	return '<!-- wp:smart-blocks/testimonial' . $attrs( $t ) . ' -->'
		. '<figure class="wp-block-smart-blocks-testimonial sb-testimonial">'
		. '<blockquote class="sb-testimonial__quote">' . esc_html( $t['quote'] ) . '</blockquote>'
		. '<figcaption class="sb-testimonial__person">'
		. '<span class="sb-testimonial__avatar" aria-hidden="true">' . $initials( $t['name'] ) . '</span>'
		. '<span>'
		. '<span class="sb-testimonial__name">' . esc_html( $t['name'] ) . '</span><br>'
		. '<span class="sb-testimonial__role">' . esc_html( $t['role'] ) . '</span>'
		. '</span>'
		. '</figcaption>'
		. '</figure>'
		. '<!-- /wp:smart-blocks/testimonial -->';
}, $test_items );
$out .= $section_static(
	'smart-blocks/testimonials',
	[ 'eyebrow' => 'Testimonials', 'heading' => 'What collaborators say.', 'dek' => 'Feedback from the people I have shipped with — founders, engineering leaders, and product teams.' ],
	'div', 'sb-testimonials__grid', $test_markup, 'sb-section--alt'
);

// CTA — static save, single block
$out .= '<!-- wp:smart-blocks/cta-section -->'
	. '<section class="wp-block-smart-blocks-cta-section sb-section sb-cta-wrap sb-reveal">'
	. '<div class="sb-cta">'
	. '<h2>Got a WordPress build that needs a senior pair of hands?</h2>'
	. '<p>Let\'s talk about your roadmap, your existing stack, and the constraints that make your project actually hard. I\'ll come back with a clear plan and a realistic estimate.</p>'
	. '<div class="sb-cta__buttons">'
	. '<a class="sb-btn sb-btn--primary" href="#contact">Book a discovery call</a>'
	. '<a class="sb-btn sb-btn--ghost" href="mailto:hello@sachinsuthar.dev">Email me directly</a>'
	. '</div>'
	. '</div>'
	. '</section>'
	. "\n<!-- /wp:smart-blocks/cta-section -->\n\n";

// CONTACT — dynamic single block
$out .= $dyn( 'smart-blocks/contact-section' );

/* ---------- 5. Insert / update page ---------- */
$existing = get_page_by_path( 'home', OBJECT, 'page' );
$postarr  = [
	'post_title'    => 'Home',
	'post_name'     => 'home',
	'post_status'   => 'publish',
	'post_type'     => 'page',
	'post_content'  => $out,
	'post_author'   => 1,
];
if ( $existing ) {
	// Clear any stale template meta from a previous provisioner run so wp_update_post
	// doesn't fail validation against custom templates the FSE theme may no longer expose.
	delete_post_meta( $existing->ID, '_wp_page_template' );
	$postarr['ID'] = $existing->ID;
	$page_id = wp_update_post( $postarr, true );
	$action  = 'updated';
} else {
	$page_id = wp_insert_post( $postarr, true );
	$action  = 'created';
}
if ( is_wp_error( $page_id ) ) {
	WP_CLI::error( $page_id->get_error_message() );
}
WP_CLI::log( sprintf( '✓ Home page %s (ID %d, %d bytes)', $action, $page_id, strlen( $out ) ) );

/* ---------- 6. Front-page options ---------- */
if ( 'page' !== get_option( 'show_on_front' ) ) {
	update_option( 'show_on_front', 'page' );
	WP_CLI::log( '✓ show_on_front → page' );
}
if ( (int) get_option( 'page_on_front' ) !== (int) $page_id ) {
	update_option( 'page_on_front', $page_id );
	WP_CLI::log( '✓ page_on_front → ' . $page_id );
}

WP_CLI::success( 'Portfolio ready. Front page: ' . get_permalink( $page_id ) );
