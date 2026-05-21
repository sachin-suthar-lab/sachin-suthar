=== Smart Blocks ===
Contributors: sachinsuthar
Tags: blocks, gutenberg, portfolio, fse, full-site-editing, react
Requires at least: 6.4
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A premium set of nine portfolio Gutenberg blocks (Hero, Services, Skills, Experience, Tech Stack, Projects, Testimonials, CTA, Contact). Built to pair with the Sachin Suthar FSE theme.

== Description ==
* Nine production-ready, server-rendered Gutenberg blocks.
* Editor parity via ServerSideRender — what you see in the editor is exactly what ships.
* No build step required: works the moment the plugin is activated.
* Tokenised CSS that respects the theme's `theme.json` design system.
* Reveal-on-scroll IntersectionObserver, with `prefers-reduced-motion` opt-out.
* Accessible: ARIA roles, focus-visible outlines, honeypot + nonce on contact form.

== Blocks ==
1. Hero
2. Services Grid
3. Skills Showcase
4. Experience Timeline
5. Tech Stack
6. Portfolio Projects
7. Testimonials
8. CTA Section
9. Contact Section

== Filters ==
Every block's content array is wrapped in an `apply_filters( 'smart_blocks/<block>/items', $items )` call so editors and themes can override defaults from the theme's `functions.php`.

== Changelog ==
= 1.0.0 =
* Initial release.
