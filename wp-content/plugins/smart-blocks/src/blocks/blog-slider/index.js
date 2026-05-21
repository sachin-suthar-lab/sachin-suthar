import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps, RichText, InspectorControls,
} from '@wordpress/block-editor';
import { PanelBody, RangeControl, ToggleControl, TextControl, Placeholder, Spinner } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import metadata from './block.json';
import './style.scss';
import './editor.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const { eyebrow, heading, dek, postsToShow, categorySlug, showDate, showExcerpt } = attributes;
		const blockProps = useBlockProps( { className: 'sb-section sb-blog' } );

		const { posts, isResolving } = useSelect( ( select ) => {
			const query = { per_page: postsToShow, _embed: true, status: 'publish' };
			if ( categorySlug ) query.categories_exclude = []; // handled server-side; editor preview just shows latest
			return {
				posts:       select( 'core' ).getEntityRecords( 'postType', 'post', query ),
				isResolving: select( 'core/data' ).isResolving( 'core', 'getEntityRecords', [ 'postType', 'post', query ] ),
			};
		}, [ postsToShow, categorySlug ] );

		return (
			<>
				<InspectorControls>
					<PanelBody title={ __( 'Query', 'smart-blocks' ) } initialOpen>
						<RangeControl label={ __( 'Posts to show', 'smart-blocks' ) } value={ postsToShow } min={ 2 } max={ 12 } onChange={ ( v ) => setAttributes( { postsToShow: v } ) } />
						<TextControl label={ __( 'Category slug (optional)', 'smart-blocks' ) } value={ categorySlug } onChange={ ( v ) => setAttributes( { categorySlug: v } ) } help={ __( 'Leave empty to show all categories.', 'smart-blocks' ) } />
					</PanelBody>
					<PanelBody title={ __( 'Display', 'smart-blocks' ) } initialOpen={ false }>
						<ToggleControl label={ __( 'Show date', 'smart-blocks' ) } checked={ showDate } onChange={ ( v ) => setAttributes( { showDate: v } ) } />
						<ToggleControl label={ __( 'Show excerpt', 'smart-blocks' ) } checked={ showExcerpt } onChange={ ( v ) => setAttributes( { showExcerpt: v } ) } />
					</PanelBody>
				</InspectorControls>

				<section { ...blockProps }>
					<div className="sb-container">
						<div className="sb-section-head">
							<RichText tagName="span" className="sb-eyebrow" value={ eyebrow } onChange={ ( v ) => setAttributes( { eyebrow: v } ) } placeholder={ __( 'Eyebrow', 'smart-blocks' ) } allowedFormats={ [] } />
							<RichText tagName="h2" value={ heading } onChange={ ( v ) => setAttributes( { heading: v } ) } placeholder={ __( 'Heading', 'smart-blocks' ) } allowedFormats={ [ 'core/bold' ] } />
							<RichText tagName="p" value={ dek } onChange={ ( v ) => setAttributes( { dek: v } ) } placeholder={ __( 'Description', 'smart-blocks' ) } allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] } />
						</div>

						{ isResolving && (
							<Placeholder><Spinner />{ __( 'Loading posts…', 'smart-blocks' ) }</Placeholder>
						) }

						{ ! isResolving && posts && posts.length === 0 && (
							<Placeholder
								icon="admin-post"
								label={ __( 'No posts yet', 'smart-blocks' ) }
								instructions={ __( 'Publish a few blog posts and they will appear here automatically.', 'smart-blocks' ) }
							/>
						) }

						{ posts && posts.length > 0 && (
							<div className="sb-blog__grid">
								{ posts.map( ( p ) => {
									const featured = p._embedded?.[ 'wp:featuredmedia' ]?.[ 0 ];
									const cover    = featured?.source_url || '';
									const date     = new Date( p.date ).toLocaleDateString( undefined, { month: 'short', day: 'numeric', year: 'numeric' } );
									return (
										<article key={ p.id } className="sb-blog__card">
											<div className="sb-blog__cover" style={ cover ? { backgroundImage: `url(${ cover })` } : undefined }>
												{ ! cover && <span className="sb-blog__cover-glyph">{ p.title.rendered.slice( 0, 1 ).toUpperCase() }</span> }
											</div>
											<div className="sb-blog__body">
												{ showDate && <time className="sb-blog__date">{ date }</time> }
												<h3 className="sb-blog__title" dangerouslySetInnerHTML={ { __html: p.title.rendered } } />
												{ showExcerpt && p.excerpt?.rendered && (
													<div className="sb-blog__excerpt" dangerouslySetInnerHTML={ { __html: p.excerpt.rendered } } />
												) }
												<span className="sb-blog__more">{ __( 'Read article →', 'smart-blocks' ) }</span>
											</div>
										</article>
									);
								} ) }
							</div>
						) }

						<p style={ { textAlign: 'center', color: '#615b76', fontSize: 12, marginTop: '1rem' } }>
							{ __( 'Front-end displays this as a swipeable carousel.', 'smart-blocks' ) }
						</p>
					</div>
				</section>
			</>
		);
	},
	save: () => null, // dynamic: render.php queries posts at request time
} );
