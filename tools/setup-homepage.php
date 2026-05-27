<?php
/**
 * Idempotent homepage provisioner for the Sachin Suthar portfolio.
 *
 * Run via: wp eval-file tools/setup-homepage.php
 *
 * All blocks are static-save. To seed from CLI we emit the same HTML the
 * editor would persist, wrapped in `<!-- wp:smart-blocks/foo {attrs} -->...HTML...<!-- /wp:smart-blocks/foo -->`.
 *
 * @package SachinSuthar
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	fwrite( STDERR, "Run via WP-CLI: wp eval-file tools/setup-homepage.php\n" );
	exit( 1 );
}

use function SmartBlocks\Helpers\icon;

/* ---------- Theme + plugin ---------- */
if ( ! wp_get_theme( 'sachin-suthar' )->exists() ) WP_CLI::error( 'Theme "sachin-suthar" not found.' );
if ( get_stylesheet() !== 'sachin-suthar' ) { switch_theme( 'sachin-suthar' ); WP_CLI::log( '✓ Activated theme sachin-suthar' ); }
else WP_CLI::log( '· Theme already active' );

/* Site identity — the header/footer Site Title block renders these dynamically. */
if ( get_option( 'blogname' ) !== 'Sachin Suthar' ) { update_option( 'blogname', 'Sachin Suthar' ); WP_CLI::log( '✓ Site title → Sachin Suthar' ); }
if ( get_option( 'blogdescription' ) !== 'Senior WordPress Developer' ) { update_option( 'blogdescription', 'Senior WordPress Developer' ); WP_CLI::log( '✓ Tagline set' ); }

if ( ! function_exists( 'activate_plugin' ) ) require_once ABSPATH . 'wp-admin/includes/plugin.php';
if ( ! is_plugin_active( 'smart-blocks/smart-blocks.php' ) ) {
	$r = activate_plugin( 'smart-blocks/smart-blocks.php' );
	if ( is_wp_error( $r ) ) WP_CLI::error( $r->get_error_message() );
	WP_CLI::log( '✓ Activated smart-blocks' );
} else {
	WP_CLI::log( '· Plugin already active' );
}

/* ---------- Helpers ---------- */
function sb_attrs( array $a ): string { return empty( $a ) ? '' : ' ' . wp_json_encode( $a, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); }
function sb_wrap( string $name, array $atts, string $inner ): string { return "<!-- wp:$name" . sb_attrs( $atts ) . " -->\n$inner\n<!-- /wp:$name -->\n\n"; }

/* ---------- Child block emitters ---------- */
function sb_service_block( array $a ): string {
	// Strip legacy proficiency keys so block attribute JSON stays clean.
	unset( $a['proficiency'], $a['showBar'], $a['barLabel'] );
	$inner = '<article class="wp-block-smart-blocks-service sb-service">'
		. '<div class="sb-service__head">'
			. '<div class="sb-service__icon">' . icon( $a['icon'] ?? 'spark', 22 ) . '</div>'
			. ( ! empty( $a['title'] ) ? '<h3 class="sb-service__title">' . wp_kses_post( $a['title'] ) . '</h3>' : '' )
		. '</div>'
		. ( ! empty( $a['desc'] ) ? '<p class="sb-service__desc">' . wp_kses_post( $a['desc'] ) . '</p>' : '' )
	. '</article>';
	return sb_wrap( 'smart-blocks/service', $a, $inner );
}

function sb_skill_block( array $a ): string {
	unset( $a['proficiency'] );
	$iconH = ! empty( $a['icon'] ) ? '<span class="sb-skill__icon">' . icon( $a['icon'], 20 ) . '</span>' : '';
	$metaH = ! empty( $a['meta'] ) ? '<span class="sb-skill__meta">' . wp_kses_post( $a['meta'] ) . '</span>' : '';
	$inner = '<div class="wp-block-smart-blocks-skill sb-skill">'
		. $iconH
		. '<span class="sb-skill__body">'
			. '<span class="sb-skill__name">' . wp_kses_post( $a['name'] ?? '' ) . '</span>'
			. $metaH
		. '</span>'
	. '</div>';
	return sb_wrap( 'smart-blocks/skill', $a, $inner );
}

function sb_timeline_block( array $a ): string {
	$tags = '';
	if ( ! empty( $a['tags'] ) ) {
		$tags = '<div class="sb-timeline-item__tags">';
		foreach ( $a['tags'] as $t ) $tags .= '<span class="sb-tag">' . esc_html( $t ) . '</span>';
		$tags .= '</div>';
	}
	$inner = '<li class="wp-block-smart-blocks-timeline-item sb-timeline-item">'
		. '<div class="sb-timeline-item__dot" aria-hidden="true"></div>'
		. ( ! empty( $a['period'] ) ? '<span class="sb-timeline-item__period">' . wp_kses_post( $a['period'] ) . '</span>' : '' )
		. ( ! empty( $a['role'] )   ? '<h3 class="sb-timeline-item__role">'   . wp_kses_post( $a['role'] )   . '</h3>'  : '' )
		. ( ! empty( $a['org'] )    ? '<p class="sb-timeline-item__org">'     . wp_kses_post( $a['org'] )    . '</p>'   : '' )
		. ( ! empty( $a['desc'] )   ? '<p class="sb-timeline-item__desc">'    . wp_kses_post( $a['desc'] )   . '</p>'   : '' )
		. $tags
	. '</li>';
	return sb_wrap( 'smart-blocks/timeline-item', $a, $inner );
}

function sb_techitem_block( array $a ): string {
	$inner = '<div class="wp-block-smart-blocks-tech-item sb-tech-card">'
		. '<div class="sb-tech-card__icon">' . icon( $a['icon'] ?? 'spark', 22 ) . '</div>'
		. '<div class="sb-tech-card__body">'
			. '<span class="sb-tech-card__name">' . wp_kses_post( $a['name'] ?? '' ) . '</span>'
			. ( ! empty( $a['meta'] ) ? '<span class="sb-tech-card__meta">' . wp_kses_post( $a['meta'] ) . '</span>' : '' )
		. '</div>'
	. '</div>';
	return sb_wrap( 'smart-blocks/tech-item', $a, $inner );
}

function sb_project_block( array $a ): string {
	$gradient = $a['gradient'] ?? 'linear-gradient(135deg, #6d28d9, #a78bfa)';
	$coverUrl = $a['coverUrl'] ?? '';
	$style    = $coverUrl
		? '--cover:url(' . esc_url( $coverUrl ) . ');background-image:url(' . esc_url( $coverUrl ) . ');'
		: '--cover:' . esc_attr( $gradient ) . ';';
	$titleHtml = '';
	if ( ! empty( $a['title'] ) ) {
		$titleInner = wp_kses_post( $a['title'] );
		$titleHtml = '<h3 class="sb-project__title">'
			. ( ! empty( $a['url'] ) ? '<a href="' . esc_url( $a['url'] ) . '" rel="noopener" target="_blank">' . $titleInner . '</a>' : $titleInner )
			. '</h3>';
	}
	$tagsHtml = '';
	if ( ! empty( $a['tags'] ) ) {
		$tagsHtml = '<div class="sb-project__tags">';
		foreach ( $a['tags'] as $t ) $tagsHtml .= '<span class="sb-tag">' . esc_html( $t ) . '</span>';
		$tagsHtml .= '</div>';
	}
	$inner = '<article class="wp-block-smart-blocks-project-card sb-project">'
		. '<div class="sb-project__cover" style="' . $style . '"' . ( $coverUrl ? ' role="img" aria-label="' . esc_attr( $a['coverAlt'] ?? '' ) . '"' : '' ) . '>'
			. ( ! $coverUrl && ! empty( $a['glyph'] ) ? '<span class="sb-project__cover-glyph">' . esc_html( $a['glyph'] ) . '</span>' : '' )
		. '</div>'
		. '<div class="sb-project__body">'
			. ( ! empty( $a['cat'] )  ? '<span class="sb-project__cat">' . wp_kses_post( $a['cat'] ) . '</span>' : '' )
			. $titleHtml
			. ( ! empty( $a['desc'] ) ? '<p class="sb-project__desc">' . wp_kses_post( $a['desc'] ) . '</p>' : '' )
			. $tagsHtml
		. '</div>'
	. '</article>';
	return sb_wrap( 'smart-blocks/project-card', $a, $inner );
}

function sb_testimonial_block( array $a ): string {
	$avatarUrl = $a['avatarUrl'] ?? '';
	$initials  = '';
	$parts     = preg_split( '/\s+/', trim( wp_strip_all_tags( $a['name'] ?? '' ) ) );
	if ( ! empty( $parts[0] ) ) {
		$initials .= mb_substr( $parts[0], 0, 1 );
		if ( ! empty( $parts[1] ) ) $initials .= mb_substr( $parts[1], 0, 1 );
	}
	$initials = mb_strtoupper( $initials );
	$avatarH  = $avatarUrl ? '<img src="' . esc_url( $avatarUrl ) . '" alt=""/>' : esc_html( $initials );
	$inner = '<figure class="wp-block-smart-blocks-testimonial sb-testimonial sb-testimonials__slide">'
		. ( ! empty( $a['quote'] ) ? '<blockquote class="sb-testimonial__quote">' . wp_kses_post( $a['quote'] ) . '</blockquote>' : '' )
		. '<figcaption class="sb-testimonial__person">'
			. '<span class="sb-testimonial__avatar" aria-hidden="true">' . $avatarH . '</span>'
			. '<span><span class="sb-testimonial__name">' . wp_kses_post( $a['name'] ?? '' ) . '</span><br/><span class="sb-testimonial__role">' . wp_kses_post( $a['role'] ?? '' ) . '</span></span>'
		. '</figcaption>'
	. '</figure>';
	return sb_wrap( 'smart-blocks/testimonial', $a, $inner );
}

function sb_cert_block( array $a ): string {
	$image = ! empty( $a['badgeUrl'] )
		? '<img src="' . esc_url( $a['badgeUrl'] ) . '" alt="' . esc_attr( $a['badgeAlt'] ?? '' ) . '"/>'
		: '<span class="sb-cert__placeholder">Image goes here</span>';

	$body = '<div class="sb-cert__image">' . $image . '</div>'
		. '<div class="sb-cert__body">'
			. ( ! empty( $a['title'] )  ? '<h3 class="sb-cert__title">' . wp_kses_post( $a['title'] ) . '</h3>' : '' )
			. ( ! empty( $a['issuer'] ) ? '<span class="sb-cert__issuer">' . wp_kses_post( $a['issuer'] ) . '</span>' : '' )
			. ( ! empty( $a['year'] )   ? '<span class="sb-cert__year">' . esc_html( $a['year'] ) . '</span>' : '' )
		. '</div>';

	$inner = '<div class="wp-block-smart-blocks-certification sb-cert">'
		. ( ! empty( $a['url'] )
			? '<a class="sb-cert__link" href="' . esc_url( $a['url'] ) . '" rel="noopener" target="_blank">' . $body . '</a>'
			: $body )
	. '</div>';
	return sb_wrap( 'smart-blocks/certification', $a, $inner );
}

/* Education item */
function sb_education_block( array $a ): string {
	$cap = '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 10 12 4 2 10l10 6 10-6Z"></path><path d="M6 12v5c0 1.66 2.69 3 6 3s6-1.34 6-3v-5"></path></svg>';
	$inner = '<div class="wp-block-smart-blocks-education-item sb-edu">'
		. '<span class="sb-edu__icon">' . $cap . '</span>'
		. '<div class="sb-edu__body">'
			. ( ! empty( $a['degree'] )      ? '<h3 class="sb-edu__degree">'   . wp_kses_post( $a['degree'] )      . '</h3>'  : '' )
			. ( ! empty( $a['institution'] ) ? '<span class="sb-edu__inst">'   . wp_kses_post( $a['institution'] ) . '</span>' : '' )
			. ( ! empty( $a['years'] )       ? '<span class="sb-edu__years">'  . wp_kses_post( $a['years'] )       . '</span>' : '' )
			. ( ! empty( $a['detail'] )      ? '<p class="sb-edu__detail">'    . wp_kses_post( $a['detail'] )      . '</p>'    : '' )
		. '</div>'
	. '</div>';
	return sb_wrap( 'smart-blocks/education-item', $a, $inner );
}

/* ---------- Parent section wrapper ---------- */
function sb_parent( string $name, string $shortCls, array $atts, string $innerTag, string $innerCls, string $childrenHtml, string $extraCls = '', string $anchor = '' ): string {
	$wrap = trim( 'sb-section ' . $shortCls . ' ' . $extraCls . ' sb-reveal' );
	$head = '<div class="sb-section-head">'
		. ( ! empty( $atts['eyebrow'] ) ? '<span class="sb-eyebrow">' . wp_kses_post( $atts['eyebrow'] ) . '</span>' : '' )
		. ( ! empty( $atts['heading'] ) ? '<h2>' . wp_kses_post( $atts['heading'] ) . '</h2>' : '' )
		. ( ! empty( $atts['dek'] )     ? '<p>'  . wp_kses_post( $atts['dek'] )     . '</p>'  : '' )
	. '</div>';
	$full_cls = 'wp-block-' . str_replace( '/', '-', $name ) . ' ' . $wrap;
	$id_attr  = $anchor !== '' ? ' id="' . esc_attr( $anchor ) . '"' : '';
	$inner = '<section class="' . esc_attr( $full_cls ) . '"' . $id_attr . '>'
		. '<div class="sb-container">' . $head . '<' . $innerTag . ' class="' . esc_attr( $innerCls ) . '">' . $childrenHtml . '</' . $innerTag . '></div>'
	. '</section>';
	if ( $anchor !== '' ) {
		$atts = array_merge( $atts, [ 'anchor' => $anchor ] );
	}
	return sb_wrap( $name, $atts, $inner );
}

function sb_testimonials_parent( array $atts, string $childrenHtml ): string {
	$cls = 'wp-block-smart-blocks-testimonials sb-section sb-testimonials sb-section--alt sb-reveal';
	$head = '<div class="sb-section-head">'
		. '<span class="sb-eyebrow">' . wp_kses_post( $atts['eyebrow'] ?? '' ) . '</span>'
		. '<h2>' . wp_kses_post( $atts['heading'] ?? '' ) . '</h2>'
		. '<p>'  . wp_kses_post( $atts['dek'] ?? '' )     . '</p>'
	. '</div>';
	$atts['anchor'] = 'testimonials';
	$inner = '<section id="testimonials" class="' . esc_attr( $cls ) . '">'
		. '<div class="sb-container">' . $head
			. '<div class="sb-testimonials__carousel" data-sb-carousel>'
				. '<div class="sb-testimonials__viewport"><div class="sb-testimonials__track">' . $childrenHtml . '</div></div>'
				. '<div class="sb-carousel-controls">'
					. '<div class="sb-carousel-dots" data-sb-dots aria-label="Slide selector"></div>'
					. '<div class="sb-carousel-nav">'
						. '<button type="button" data-sb-prev aria-label="Previous testimonials"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg></button>'
						. '<button type="button" data-sb-next aria-label="Next testimonials"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg></button>'
					. '</div>'
				. '</div>'
			. '</div>'
		. '</div>'
	. '</section>';
	return sb_wrap( 'smart-blocks/testimonials', $atts, $inner );
}

/* ---------- Single-instance blocks ---------- */
function sb_hero(): string {
	$metrics = [
		[ 'value' => '8',      'label' => 'Years of WordPress engineering' ],
		[ 'value' => '70+',    'label' => 'Projects shipped end-to-end' ],
		[ 'value' => '50+',    'label' => 'Custom themes & plugins built' ],
		[ 'value' => '30–50%', 'label' => 'Median performance gains' ],
	];
	$chips = [ 'WordPress VIP', 'Gutenberg', 'WooCommerce', 'ACF Pro', 'Headless WP', 'Performance' ];
	$m = '';
	foreach ( $metrics as $x ) $m .= '<div class="sb-metric" role="listitem"><strong>' . esc_html( $x['value'] ) . '</strong><span>' . esc_html( $x['label'] ) . '</span></div>';
	$c = '';
	foreach ( $chips as $x )   $c .= '<span class="sb-hero__chips-item">' . esc_html( $x ) . '</span>';

	$inner = '<section id="home" class="wp-block-smart-blocks-hero sb-section sb-hero sb-reveal">'
		. '<div class="sb-container">'
			. '<div class="sb-hero__layout">'
				. '<div class="sb-hero__copy">'
					. '<span class="sb-hero__badge"><span>Senior WordPress Developer · 8 years</span></span>'
					. '<h1><span>WordPress engineering, built for</span> <span class="sb-gradient">long-term performance.</span></h1>'
					. '<p class="sb-hero__lede">I\'m Sachin Suthar — a Senior WordPress Developer from Ahmedabad with 8 years of hands-on experience across 70+ projects. I build custom themes, plugins, ACF and Gutenberg blocks, and performance-tuned WooCommerce stores for agencies, startups, and enterprise clients.</p>'
					. '<div class="sb-hero__currently"><span class="bullet" aria-hidden="true"></span><span>Currently leading WP engineering at <strong>NineGravity</strong></span></div>'
					. '<div class="sb-hero__cta"><a class="sb-btn sb-btn--primary" href="#contact">Start a project</a><a class="sb-btn sb-btn--ghost" href="#work">View selected work</a></div>'
					. '<div class="sb-hero__chips">' . $c . '</div>'
				. '</div>'
				. '<div class="sb-hero__visual">'
					. '<div class="sb-image-slot"><span class="sb-image-slot__label">Image goes here</span></div>'
				. '</div>'
			. '</div>'
			. '<div class="sb-hero__metrics" role="list">' . $m . '</div>'
		. '</div>'
	. '</section>';
	return sb_wrap( 'smart-blocks/hero', [
		'currentlyText'   => 'Currently leading WP engineering at',
		'currentlyTarget' => 'NineGravity',
		'chips'           => $chips,
	], $inner );
}

/* Marquee block — auto-scrolling tech keywords */
function sb_marquee( string $items_csv ): string {
	$parts = array_filter( array_map( 'trim', explode( ',', $items_csv ) ) );
	// Duplicate for seamless loop, matching the React save() output.
	$doubled = array_merge( $parts, $parts );
	$track = '<div class="sb-marquee__track">';
	foreach ( $doubled as $label ) {
		$track .= '<span class="sb-marquee__item"><span class="dot" aria-hidden="true"></span>' . esc_html( $label ) . '</span>';
	}
	$track .= '</div>';
	$inner = '<div class="wp-block-smart-blocks-marquee sb-marquee">' . $track . '</div>';
	return sb_wrap( 'smart-blocks/marquee', [ 'items' => $items_csv ], $inner );
}

/* Blog Slider — dynamic, only marker emitted (PHP queries posts at render time) */
function sb_blog_slider(): string {
	return "<!-- wp:smart-blocks/blog-slider /-->\n\n";
}

function sb_about(): string {
	// Spec-sheet: label → value (no availability framing).
	$highlights = [
		[ 'title' => 'Based in',    'meta' => 'Ahmedabad, India · Remote-friendly' ],
		[ 'title' => 'Experience',  'meta' => '8 years · 70+ projects shipped' ],
		[ 'title' => 'Focus',       'meta' => 'Custom themes, plugins, ACF & Gutenberg' ],
		[ 'title' => 'Education',   'meta' => 'MCA — Gujarat Technological University' ],
		[ 'title' => 'Currently',   'meta' => 'Senior WP Developer & Team Lead, NineGravity' ],
	];
	$h = '';
	foreach ( $highlights as $x ) $h .= '<div class="sb-about__highlight"><strong>' . esc_html( $x['title'] ) . '</strong><span>' . esc_html( $x['meta'] ) . '</span></div>';

	$inner = '<section id="about" class="wp-block-smart-blocks-about sb-section sb-about sb-section--cream sb-reveal">'
		. '<div class="sb-container">'
			. '<div class="sb-about__layout">'
				. '<div class="sb-about__visual"><div class="sb-image-slot"><span class="sb-image-slot__label">Image goes here</span></div></div>'
				. '<div class="sb-about__copy">'
					. '<span class="sb-eyebrow">About</span>'
					. '<h2>Engineer-led WordPress, from <em>planning to deployment.</em></h2>'
					. '<p>I\'m Sachin Suthar — based in Ahmedabad, India, with 8 years building custom themes, plugins, and backend systems for eCommerce, LMS, and corporate platforms. I take projects from technical scoping through code review and deployment: clean, maintainable code, fixing performance bottlenecks, and integrating third-party APIs. Familiar with WordPress VIP coding standards through certification, and currently going deeper into React-based Gutenberg development and PHPUnit testing.</p>'
					. '<div class="sb-about__highlights">' . $h . '</div>'
				. '</div>'
			. '</div>'
		. '</div>'
	. '</section>';
	return sb_wrap( 'smart-blocks/about', [], $inner );
}

function sb_cta(): string {
	$inner = '<section id="cta" class="wp-block-smart-blocks-cta-section sb-section sb-cta-wrap sb-section--cream sb-reveal">'
		. '<div class="sb-container">'
			. '<div class="sb-cta">'
				. '<div class="sb-cta__text">'
					. '<h2>Like what you see? Let\'s connect.</h2>'
					. '<p>Always happy to talk WordPress, swap notes on Gutenberg and performance, or just say hello. Find me below.</p>'
				. '</div>'
				. '<div class="sb-cta__buttons">'
					. '<a class="sb-btn sb-btn--primary" href="#contact">Say hello</a>'
					. '<a class="sb-btn sb-btn--ghost" href="https://github.com/sachin5713/" target="_blank" rel="noopener">View GitHub</a>'
				. '</div>'
			. '</div>'
		. '</div>'
	. '</section>';
	return sb_wrap( 'smart-blocks/cta-section', [], $inner );
}

function sb_contact(): string {
	$channels = [
		[ 'icon' => 'mail',     'label' => 'Email',    'value' => 'sachin.suthar2493@gmail.com',     'href' => 'mailto:sachin.suthar2493@gmail.com' ],
		[ 'icon' => 'linkedin', 'label' => 'LinkedIn', 'value' => 'in/sachin-wordpress-developer',   'href' => 'https://www.linkedin.com/in/sachin-wordpress-developer/' ],
		[ 'icon' => 'github',   'label' => 'GitHub',   'value' => 'github.com/sachin5713',           'href' => 'https://github.com/sachin5713/' ],
		[ 'icon' => 'bolt',     'label' => 'Phone',    'value' => '+91 97263 37383',                 'href' => 'tel:+919726337383' ],
	];
	$ch = '<div class="sb-contact__list">';
	foreach ( $channels as $c ) {
		$ext = strpos( $c['href'], 'http' ) === 0;
		$ch .= '<a class="sb-contact__item" href="' . esc_url( $c['href'] ) . '"' . ( $ext ? ' target="_blank" rel="noopener"' : '' ) . '>'
			. '<span class="sb-contact__item-icon">' . icon( $c['icon'], 18 ) . '</span>'
			. '<span><span class="sb-contact__item-label">' . esc_html( $c['label'] ) . '</span><span class="sb-contact__item-value">' . esc_html( $c['value'] ) . '</span></span>'
		. '</a>';
	}
	$ch .= '</div>';

	$form = '<form class="sb-contact__form" data-sb-contact novalidate>'
		. '<div class="sb-contact__form-status" data-sb-status role="status" aria-live="polite"></div>'
		. '<div class="sb-contact__form-row">'
			. '<div class="sb-field" data-sb-field="sb_name"><label class="sb-field__label" for="sb_name_input">Your name<span class="sb-field__required" aria-hidden="true">*</span></label><input type="text" id="sb_name_input" name="sb_name" required autocomplete="name" placeholder="Jane Doe" aria-describedby="sb_name_err"/><div class="sb-field__error" id="sb_name_err"></div></div>'
			. '<div class="sb-field" data-sb-field="sb_email"><label class="sb-field__label" for="sb_email_input">Email<span class="sb-field__required" aria-hidden="true">*</span></label><input type="email" id="sb_email_input" name="sb_email" required autocomplete="email" placeholder="jane@company.com" aria-describedby="sb_email_err"/><div class="sb-field__error" id="sb_email_err"></div></div>'
		. '</div>'
		. '<div class="sb-field" data-sb-field="sb_company"><label class="sb-field__label" for="sb_company_input">Company / Project</label><input type="text" id="sb_company_input" name="sb_company" autocomplete="organization" placeholder="Acme Inc · WooCommerce rebuild"/></div>'
		. '<div class="sb-field" data-sb-field="sb_message"><label class="sb-field__label" for="sb_message_input">Tell me about the project<span class="sb-field__required" aria-hidden="true">*</span></label><textarea id="sb_message_input" name="sb_message" required placeholder="What are you building, what\'s the timeline, and what does success look like?" aria-describedby="sb_message_err"></textarea><div class="sb-field__error" id="sb_message_err"></div></div>'
		. '<div class="sb-honeypot" aria-hidden="true"><label>Leave this empty <input type="text" name="sb_website" tabindex="-1" autocomplete="off"/></label></div>'
		. '<div class="sb-contact__form-actions"><button type="submit" class="sb-btn sb-btn--primary" data-sb-submit>Send message</button></div>'
	. '</form>';

	$inner = '<section id="contact" class="wp-block-smart-blocks-contact-section sb-section sb-contact sb-reveal">'
		. '<div class="sb-container">'
			. '<div class="sb-section-head"><span class="sb-eyebrow">Contact</span><h2>Get in <em>touch.</em></h2><p>Questions about my work, WordPress, or just want to connect? Drop a message — I read every one and reply within a couple of days.</p></div>'
			. '<div class="sb-contact__wrap">'
				. '<aside class="sb-contact__intro"><h3>Find me</h3><p>Pick whatever\'s easiest — email, LinkedIn, or the form. Happy to chat.</p>' . $ch . '</aside>'
				. $form
			. '</div>'
		. '</div>'
	. '</section>';
	return sb_wrap( 'smart-blocks/contact-section', [], $inner );
}

/* ---------- Seed sample blog posts if none exist ---------- */
$existing_posts = wp_count_posts( 'post' );
if ( ( $existing_posts->publish ?? 0 ) < 4 ) {
	$samples = [
		[
			'title'    => 'Shipping Gutenberg blocks the WordPress VIP way',
			'excerpt'  => 'How we structure custom blocks for editor-parity, accessibility, and zero deprecation pain across major WP releases.',
			'category' => 'Gutenberg',
		],
		[
			'title'    => 'Cutting LCP from 4.8s to 1.2s on a real WooCommerce store',
			'excerpt'  => 'A practical playbook: critical CSS, image strategy, MySQL query refactor, and edge caching that actually moves the needle.',
			'category' => 'Performance',
		],
		[
			'title'    => 'Building an ACF block library shared across 30+ client sites',
			'excerpt'  => 'Design tokens, versioning strategy, and a release process that lets you ship one fix to every site without breaking content.',
			'category' => 'Architecture',
		],
		[
			'title'    => 'WP-CLI scripts every senior developer should write',
			'excerpt'  => 'Idempotent migrations, content imports, environment bootstrapping, and CI-friendly deploy commands.',
			'category' => 'WP-CLI',
		],
		[
			'title'    => 'Headless WordPress with Next.js: where it shines, where it bites',
			'excerpt'  => 'Two years of running headless WP in production — auth, ISR, preview, image handling, and the editor experience trade-offs.',
			'category' => 'Headless',
		],
	];
	$created = 0;
	foreach ( $samples as $i => $s ) {
		// Avoid duplicating if a similarly-titled post already exists.
		if ( get_page_by_title( $s['title'], OBJECT, 'post' ) ) continue;
		$cat_id = wp_create_category( $s['category'] );
		$post_id = wp_insert_post( [
			'post_type'     => 'post',
			'post_status'   => 'publish',
			'post_title'    => $s['title'],
			'post_excerpt'  => $s['excerpt'],
			'post_content'  => '<!-- wp:paragraph --><p>' . esc_html( $s['excerpt'] ) . '</p><!-- /wp:paragraph -->',
			'post_author'   => 1,
			'post_date'     => date( 'Y-m-d H:i:s', strtotime( "-{$i} days" ) ),
			'post_category' => $cat_id ? [ $cat_id ] : [],
		], true );
		if ( ! is_wp_error( $post_id ) ) $created++;
	}
	if ( $created ) WP_CLI::log( "✓ Seeded {$created} sample blog posts" );
}

/* ---------- Build the page content ---------- */
$out  = sb_hero();
$out .= sb_about();

// SERVICES
$services = [
	[ 'icon' => 'wp',       'title' => 'Custom WordPress Development',  'desc' => 'End-to-end custom themes, plugins, and admin-side tooling, built to WordPress VIP coding standards with clean architecture.' ],
	[ 'icon' => 'box',      'title' => 'ACF & Gutenberg Block Dev',     'desc' => 'Production-grade ACF blocks and native Gutenberg blocks. Editor UX that lets marketing ship without engineering.' ],
	[ 'icon' => 'woo',      'title' => 'WooCommerce Engineering',       'desc' => 'Custom checkouts, subscriptions, payment gateways, and ERP integrations for content-heavy stores.' ],
	[ 'icon' => 'plug',     'title' => 'API Integrations',              'desc' => 'REST and third-party API bridges to CRMs, payment processors, mailers, and back-office systems.' ],
	[ 'icon' => 'gauge',    'title' => 'Performance Optimisation',      'desc' => 'Core Web Vitals deep-dives, MySQL query optimisation, Redis/Batcache/edge caching. 30–50% measured gains.' ],
	[ 'icon' => 'layers',   'title' => 'Advanced Custom Fields (ACF)',  'desc' => 'Modular content models with ACF Pro, custom post types, taxonomies, and user-role architectures.' ],
	[ 'icon' => 'cube',     'title' => 'Elementor · WPBakery · Divi',   'desc' => 'Custom widgets, dynamic data, and visual-builder workflows that match designer intent without sacrificing performance.' ],
	[ 'icon' => 'terminal', 'title' => 'WP-CLI Automation',             'desc' => 'Idempotent CLI scripts for migrations, content imports, environment setup, and CI/CD-friendly deploys.' ],
	[ 'icon' => 'spark',    'title' => 'AI-assisted Development',       'desc' => 'n8n, Claude Code, OpenAI Codex, ChatGPT, Antigravity, Cursor — modern tooling to ship faster without compromising code quality.' ],
];
$out .= sb_parent( 'smart-blocks/services-grid', 'sb-services', [
	'eyebrow' => '01 · What I do',
	'heading' => 'Services tuned for <em>ambitious</em> WordPress products.',
	'dek'     => 'Specialist services across the modern WordPress stack — from custom block development to performance engineering and CI-friendly deployments.',
], 'div', 'sb-services__grid', implode( '', array_map( 'sb_service_block', $services ) ), '', 'services' );

// EXPERTISE (was Skills/Toolkit) — capability areas with descriptors
$expertise = [
	[ 'icon' => 'wp',       'name' => 'Custom WordPress',      'meta' => 'Themes, plugins & bespoke admin tooling, VIP standards.' ],
	[ 'icon' => 'layers',   'name' => 'Gutenberg & ACF',       'meta' => 'Native + ACF blocks with a clean editor experience.' ],
	[ 'icon' => 'woo',      'name' => 'WooCommerce',           'meta' => 'Checkout, subscriptions, gateways & ERP integrations.' ],
	[ 'icon' => 'gauge',    'name' => 'Performance',           'meta' => 'Core Web Vitals, caching, and MySQL query tuning.' ],
	[ 'icon' => 'plug',     'name' => 'Headless & APIs',       'meta' => 'REST, webhooks, and React / Next.js front-ends.' ],
	[ 'icon' => 'terminal', 'name' => 'Architecture & DevOps', 'meta' => 'Multisite, WP-CLI, CI/CD, and code-review culture.' ],
];
$out .= sb_parent( 'smart-blocks/skills-showcase', 'sb-skills', [
	'eyebrow' => '02 · Expertise',
	'heading' => 'Where I go <em>deep.</em>',
	'dek'     => 'Six areas I have shipped repeatedly across agencies, products, and enterprise teams.',
], 'div', 'sb-skills__grid', implode( '', array_map( 'sb_skill_block', $expertise ) ), 'sb-section--cream', 'skills' );

// EXPERIENCE
$experience = [
	[ 'period' => '2024 — Present', 'role' => 'Senior WordPress Developer · Team Lead',   'org' => 'NineGravity — Ahmedabad, India',     'desc' => 'Lead development of WordPress projects used in live production. Built custom plugin architecture that automates internal business workflows. Conduct code reviews, enforce WP Coding Standards, define technical scope with PMs and stakeholders. Resolve critical live-site bugs with minimal downtime.', 'tags' => [ 'Team Lead', 'Custom Plugins', 'Code Review', 'WP Standards', 'MySQL Tuning' ] ],
	[ 'period' => '2019 — 2024',    'role' => 'Senior WordPress Developer',                'org' => 'SilverWebBuzz Pvt. Ltd. — Ahmedabad', 'desc' => 'Built 50+ custom themes and plugins for business-critical websites across eCommerce, LMS, and corporate platforms. Implemented custom post types, taxonomies, and user roles; integrated third-party APIs; used WP-CLI to automate DB operations and deploys; reduced page-load times through query optimisation.', 'tags' => [ 'Custom Themes', 'WooCommerce', 'API Integrations', 'WP-CLI', 'Performance' ] ],
	[ 'period' => '2017 — 2018',    'role' => 'PHP Developer · Software Support Engineer', 'org' => 'BlueMax Services — Mehsana, India',   'desc' => 'Supported government software platforms where uptime and reliability were critical. Fixed server-side and application bugs, documented long-term solutions, built and maintained PHP modules, assisted with deployments, upgrades, and technical documentation.', 'tags' => [ 'PHP', 'Bug Fixing', 'Documentation', 'Deployments' ] ],
];
$out .= sb_parent( 'smart-blocks/experience-timeline', 'sb-experience', [
	'eyebrow' => '03 · Experience',
	'heading' => 'A practical journey through the <em>WordPress ecosystem.</em>',
	'dek'     => '8 years of building WordPress products across agencies, SaaS, eCommerce, LMS, and government platforms.',
], 'ol', 'sb-timeline', implode( '', array_map( 'sb_timeline_block', $experience ) ), '', 'experience' );

// EDUCATION (placed right after Experience — academic foundation for the career)
$education = [
	[ 'degree' => 'Master of Computer Applications', 'institution' => 'Gujarat Technological University', 'years' => 'MCA · CGPA 8.08', 'detail' => 'Advanced software engineering, databases, and systems design — the foundation for backend-heavy WordPress work.' ],
	[ 'degree' => 'Bachelor of Computer Applications', 'institution' => 'Hemchandracharya North Gujarat University', 'years' => 'BCA · CGPA 7.25', 'detail' => 'Programming fundamentals, web technologies, and data structures.' ],
];
$out .= sb_parent( 'smart-blocks/education', 'sb-education', [
	'eyebrow' => '04 · Education',
	'heading' => 'Formal grounding in <em>computer science.</em>',
	'dek'     => 'A CS background that underpins how I architect and reason about WordPress systems.',
], 'div', 'sb-education__grid', implode( '', array_map( 'sb_education_block', $education ) ), 'sb-section--cream', 'education' );

// TECH — real day-to-day stack of a senior WordPress engineer (8 years).
$stack = [
	[ 'icon' => 'wp',         'name' => 'WordPress',     'meta' => '8 yrs · Core, FSE, multisite' ],
	[ 'icon' => 'php',        'name' => 'PHP 8',         'meta' => 'OOP, namespaces, Composer' ],
	[ 'icon' => 'js',         'name' => 'JavaScript',    'meta' => 'ES6+ · async, fetch, ESM' ],
	[ 'icon' => 'react',      'name' => 'React',         'meta' => 'Gutenberg block UI' ],
	[ 'icon' => 'layers',     'name' => 'Gutenberg',     'meta' => 'Native blocks · block.json' ],
	[ 'icon' => 'woo',        'name' => 'WooCommerce',   'meta' => 'Checkout, subscriptions, gateways' ],
	[ 'icon' => 'box',        'name' => 'ACF Pro',       'meta' => 'Field groups & custom blocks' ],
	[ 'icon' => 'db',         'name' => 'MySQL',         'meta' => 'Query tuning, indexes' ],
	[ 'icon' => 'phpmyadmin', 'name' => 'phpMyAdmin',    'meta' => 'DB admin & query debugging' ],
	[ 'icon' => 'terminal',   'name' => 'WP-CLI',        'meta' => 'Migrations, deploys, scripting' ],
	[ 'icon' => 'docker',     'name' => 'Docker',        'meta' => 'Local envs · wp-env parity' ],
	[ 'icon' => 'composer',   'name' => 'Composer',      'meta' => 'Dependencies · PSR-4 autoload' ],
	[ 'icon' => 'git',        'name' => 'Git',           'meta' => 'GitHub · GitLab · CI/CD' ],
	[ 'icon' => 'node',       'name' => 'Node & npm',    'meta' => 'wp-scripts · build tooling' ],
	[ 'icon' => 'rest',       'name' => 'REST API',      'meta' => 'Custom routes · headless' ],
	[ 'icon' => 'award',      'name' => 'WordPress VIP', 'meta' => 'Coding standards · go/vip' ],
];
$out .= sb_parent( 'smart-blocks/tech-stack', 'sb-tech', [
	'eyebrow' => '05 · Tech stack',
	'heading' => 'The stack I <em>actually</em> ship with.',
	'dek'     => 'Eight years deep in the WordPress ecosystem — from React-powered Gutenberg blocks to WP-CLI, Docker, and VIP-grade workflows.',
], 'div', 'sb-tech__grid', implode( '', array_map( 'sb_techitem_block', $stack ) ), '', 'stack' );

// CERTIFICATIONS
$certs = [
	[ 'title' => 'Enterprise Block Editor',              'issuer' => 'WordPress VIP' ],
	[ 'title' => 'Advanced WordPress Debugging',         'issuer' => 'WordPress VIP' ],
	[ 'title' => 'WordPress VIP Architecture & Tooling', 'issuer' => 'WordPress VIP' ],
	[ 'title' => 'Enterprise WordPress Performance',     'issuer' => 'WordPress VIP' ],
	[ 'title' => 'Enterprise WordPress Security',        'issuer' => 'WordPress VIP' ],
];
$out .= sb_parent( 'smart-blocks/certifications', 'sb-certifications', [
	'eyebrow' => '06 · Credentials',
	'heading' => 'Certifications.',
	'dek'     => 'Continuing education aligned with WordPress VIP and enterprise-grade practice. Upload certificate images per item from the editor.',
], 'div', 'sb-certifications__grid', implode( '', array_map( 'sb_cert_block', $certs ) ), 'sb-section--cream', 'certifications' );

// PROJECTS
$projects = [
	[ 'cat' => 'WooCommerce',     'title' => 'Subscription commerce platform',         'desc' => 'Custom subscription engine on WooCommerce powering a high-volume DTC brand. Dunning, proration, and a custom self-serve member portal.', 'glyph' => 'WC', 'gradient' => 'linear-gradient(135deg, #6d28d9 0%, #a78bfa 100%)',  'tags' => [ 'WooCommerce', 'Stripe', 'Custom Plugin' ] ],
	[ 'cat' => 'LMS',             'title' => 'Learning management system',              'desc' => 'Custom LMS on WordPress with LearnDash extensions, certificate generation, drip content, and reporting dashboards.',                  'glyph' => 'LM', 'gradient' => 'linear-gradient(135deg, #7c3aed 0%, #c4b5fd 100%)', 'tags' => [ 'LMS', 'LearnDash', 'ACF Pro' ] ],
	[ 'cat' => 'Block Library',   'title' => 'Reusable ACF block library',              'desc' => 'Shared ACF-based Gutenberg block library used across 30+ client sites: design tokens, accessibility, editor parity.',                'glyph' => 'BL', 'gradient' => 'linear-gradient(135deg, #16a34a 0%, #a78bfa 100%)', 'tags' => [ 'ACF Blocks', 'Gutenberg', 'Design Tokens' ] ],
	[ 'cat' => 'Performance',     'title' => 'Core Web Vitals rescue',                  'desc' => 'Reduced LCP from 4.8s → 1.2s, CLS from 0.34 → 0.02. Critical-CSS pipeline, image strategy, DB query refactor, edge caching.',          'glyph' => 'PF', 'gradient' => 'linear-gradient(135deg, #a78bfa 0%, #6d28d9 100%)', 'tags' => [ 'Performance', 'CWV', 'Caching' ] ],
	[ 'cat' => 'Custom Plugin',   'title' => 'Internal workflow automation',            'desc' => 'Custom plugin architecture automating internal business workflows: ticket routing, status sync, and stakeholder reporting.',          'glyph' => 'WF', 'gradient' => 'linear-gradient(135deg, #6d28d9 0%, #16a34a 100%)', 'tags' => [ 'Custom Plugin', 'REST API', 'Automation' ] ],
	[ 'cat' => 'Enterprise CMS',  'title' => 'Corporate multisite migration',           'desc' => 'Migrated multiple country sites into a unified multisite network with shared theme.json, automated WP-CLI deploys, editorial workflow.','glyph' => 'MS', 'gradient' => 'linear-gradient(135deg, #4c1d95 0%, #a78bfa 100%)', 'tags' => [ 'Multisite', 'WP-CLI', 'i18n' ] ],
];
$out .= sb_parent( 'smart-blocks/portfolio-projects', 'sb-projects', [
	'eyebrow' => '07 · Selected work',
	'heading' => 'Selected work that <em>shipped.</em>',
	'dek'     => 'A snapshot of recent projects across eCommerce, LMS, custom plugins, and performance engineering. 70+ projects delivered overall.',
], 'div', 'sb-projects__grid', implode( '', array_map( 'sb_project_block', $projects ) ), '', 'work' );

// TESTIMONIALS (carousel)
$tests = [
	[ 'quote' => 'Sachin rebuilt our marketing site as a custom theme + ACF block library — editor experience went from "open a ticket" to "ship it yourself" overnight.', 'name' => 'Priya Menon',    'role' => 'Head of Marketing · B2B SaaS' ],
	[ 'quote' => 'We hired Sachin to fix a 4.8s LCP. Two weeks later we were under 1.5s on real devices, with cleaner code than we started with. Rare combination.',                       'name' => 'David Lin',      'role' => 'CTO · DTC Commerce' ],
	[ 'quote' => 'He thinks in architectures, not snippets. Our custom plugin survived two WordPress major releases without a single regression — that is the bar.',                       'name' => 'Marta Hoffmann', 'role' => 'Engineering Manager · Agency' ],
	[ 'quote' => 'Communication, code review, documentation — all senior-level. He raised the standard for the whole team while he was with us.',                                          'name' => 'Rahul Verma',    'role' => 'Founder · Product Studio' ],
	[ 'quote' => 'The ACF block library Sachin built is now reused across 30+ client sites. Real engineering, not just markup.',                                                            'name' => 'Sofia Alvarez',  'role' => 'Tech Lead · Digital Agency' ],
	[ 'quote' => 'Migrated multiple country sites into one multisite network with zero downtime and a faster editorial workflow on the other side. Quiet, calm, and exact.',               'name' => 'Jonas Berg',     'role' => 'Director of Web · Enterprise' ],
];
$out .= sb_testimonials_parent( [
	'eyebrow' => '08 · Testimonials',
	'heading' => 'What collaborators <em>say.</em>',
	'dek'     => 'Feedback from the people I have shipped with — founders, engineering leaders, and product teams.',
], implode( '', array_map( 'sb_testimonial_block', $tests ) ) );

$out .= sb_blog_slider();
$out .= sb_cta();
$out .= sb_contact();

/* ---------- Insert / update page ---------- */
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

if ( 'page' !== get_option( 'show_on_front' ) ) { update_option( 'show_on_front', 'page' );          WP_CLI::log( '✓ show_on_front → page' ); }
if ( (int) get_option( 'page_on_front' ) !== (int) $page_id ) { update_option( 'page_on_front', $page_id ); WP_CLI::log( '✓ page_on_front → ' . $page_id ); }

WP_CLI::success( 'Portfolio ready. Front page: ' . get_permalink( $page_id ) );
