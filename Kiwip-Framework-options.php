<?php
/**
 * Kiwip Theme Options
 * Call the Kiwip Framework and process
 *
 * @package WordPress
 * @subpackage Kiwip_Framework | Wordpress Theme
 * @since Kiwip Framework 0.1
 * @author Benjamin Cabanes | http://slapandthink.com | @slapandthink
 * @version 0.1
 * @copyright Benjamin Cabanes
 */

/**
 * ==============================================================
 * ======================== SIMPLE USAGE ========================
 * ==============================================================
 * How to use the Kiwip Framework
 * Simply include the Kiwip-Framework-options.php file at the top of your themes functions.php file, like so:
 * get_template_part('Kiwip-Framework-options');
 * Then change the settings as written in the Kiwip-Framework-options.php file.
 */


// Call the Kiwip Framework
require_once TEMPLATEPATH.'/Kiwip_Framework/Kiwip_Framework.php';
$KiwipFramework = new Kiwip_Framework;

$template_url  = get_bloginfo('template_url');
$template_textdomain = basename(get_template_directory_uri()); // Use for translation ready, get the TextDomain of the theme.

/**
 * ==============================================================
 * ====================== CUSTOM POST TYPE ======================
 * ==============================================================
 */

/**
 * Create the Custom Post Type for Slideshow
 * 
 */

/**
 * To create a Custom Post Type, you need to Kiwip_Framework is instantiated.
 * Then you have to call the "add_custom_post_type" method of the Kiwip Framework.
 *
 * method add_custom_post_type(string $ctp_name, array $config, array $args, array $labels)
 * 
 * $ctp_name (string) (required) The name of the Custom Post Type (display in the admin area)
 *   (max. 20 characters, can not contain capital letters or spaces) 
 * $args (array) (optional) An array of arguments. 
 * $config (array) (optional) A set of configuration for more personalisation.
 *   -> icons : array (add icons in 16x16 and 32x32 pixels)
 *   -> include in search : include Custom Post Type data in search
 * $args (array) (optional) The same arguments for the Register Post Type function 
 *   http://codex.wordpress.org/Function_Reference/register_post_type
 * $labels (array) (optional) The same argument for the Register Post Type function
 *   http://codex.wordpress.org/Function_Reference/register_post_type
 *   NOTE: The plural form is generated automatically
 *
 * Problems to fix in the "add_custom_post_type" method:
 * - Name probleme "can not contain capital letters or spaces" ???!! Fix that!					//--> Checked
 * - The declaration way of icons should be like this: 
 *      'icons' => array('16px' => 'path/to/the/icon', '32px' => 'path/to/the/icon')			//--> Checked
 * - Plural is working ??? I think not really ...												//--> Checked
 *
 */
$CPT_Slideshow = $KiwipFramework->add_custom_post_type( 'Slideshow', 
			array(  // configuration
				'icons' => array(
					'16px' => $template_url.'/img/cpt/slideshow-16.png',
					'32px' => $template_url.'/img/cpt/slideshow-32.png'
				),
				'include_in_search' => false
			),
            array( 'supports' => array( 'title', 'editor', 'thumbnail' ) )//,
            //array( 'name' => 'Slideshows' ) // 'name' - general name for the post type, usually plural. 
        );
/**
 * To add taxonomy to your new Custom Post Type, 
 * you need to call the "kiwip_add_taxonomy" method AFTER calling "add_custom_post_type" method.
 *
 * method kiwip_add_taxonomy ( string $taxnonmy_name, array $args, array $labels)
 *
 * $taxonomy_name (string) The name of the Taxnonmy. Name should be in slug form
 *   (must not contain capital letters or spaces) and not more than 32 characters long
 * $args (array) (optional) The same arguments for the Resister Taxonomy function 
 *   http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments
 * $labels (array) (optional) The same argument for the Register Taxonomy function
 *   http://codex.wordpress.org/Function_Reference/register_taxonomy
 *
 */
$CPT_Slideshow->kiwip_add_taxonomy( //add a page taxonomy to Slideshow Custom Post Type
	'Slide group', 
	array( //args
		
		'public'            => true,
		'show_in_nav_menus' => true,
		'show_ui'           => true,
		'show_tagcloud'     => true,
		'hierarchical'      => true,
		'query_var'         => true,
		//'rewrite'           => array('slug' => _x('slide-group', 'taxonomy rewrite', $template_textdomain)) // For example
		), 
	array( //labels
		'menu_name'         => __('Group', $template_textdomain)
		)
	); 
/**
 * To add some CPT's meta boxes in the admin panel,
 * you neet to call the "kiwip_add_meta_box" method AFTER calling "add_custom_post_type" method.
 *
 * method kiwip_add_meta_box ( string $zone_title, array $metaboxes);
 *
 * $zone_title (string) The title of the admin zone where is displayed all meta boxes
 * $metaboxes (array) All the meta boxes you need
 *
 * The metabox declaration is composed of five elements:
 *  - name        (required) : the input name
 *  - label       (required) : the label text
 *  - description (optional) : the description text displayed under the label
 *  - options     (optional) : the metabox options
 *
 * All metabox type available:
 *   - text       : display an input text
 *   - image      : display the media library button with upload and library access
 *   - checkbox   : display a checkbox
 *   - checkboxes : display multiple checkboxes
 *   - textarea   : display a textarea with no editor
 *   - editor     : display a textarea with editor
 *   - yesno      : display two radio buttons, one with "yes" value and another with "no" value
 *   - select     : display a select form
 *   - date       : display an inpute text with a datepicker
 *
 * Problems to fix:
 * - ReCoding the base function "kiwip_make_field" with objects in folders (more flexible)
 * - How do we use the checkboxes ????
 * - Style review
 * - Add more elements
 * 
 */
$CPT_Slideshow->kiwip_add_meta_box( 
    'Slideshow informations', 
    array(
        array(
            'name'          => 'link',
            'label'         => __('Link', $template_textdomain),
            'description'   => __('URL for the slide', $template_textdomain),
            'type'          => 'text'
        ), 
        array(
            'name'          => 'background',
            'label'         => __('Background', $template_textdomain),
            'description'   => __('Background image for the slide', $template_textdomain),
            'type'          => 'image'
        ),
        array(
            'name'          => 'video',
            'label'         => __('Youtube Code', $template_textdomain),
            'description'   => __('Paste the youtube video code', $template_textdomain),
            'type'          => 'text'
        ),
        array(
            'name'          => 'not_display_title',
            'label'         => __('Do NOT display title?', $template_textdomain),
            'description'   => __('Do you want to NOT display the slide title?', $template_textdomain),
            'type'          => 'checkbox'
        )
    ));

/**
 * ==============================================================
 * ========================= OPTION PAGE ========================
 * ==============================================================
 */

/**
 * This is how we can instantiate options for the template
 */

/**
 * The options page is composed in multiple sections. There sections are like 
 * categories, they can separate your content in direfent tabs in page.
 * One tab is one section, you can create any section you want.
 * In each of there sections, you can put some inputs.
 * Set the options page like this:
 */

/**
 * STEP 1: define the args var as an array
 */
$args = array();
/**
 * STEP 2: set the name of the template
 */
$args['option_name'] = 'Themename';
/**
 * STEP 3: create the sections
 *
 * Each section is built as an array. You can define multiple informations.
 * - icon   : the icon URL for the tab
 * - title  : the tab title
 * - slug   : the tab slug, slugable string, can not contain capital letters or spaces
 * - desc   : the tab description, displayed under the tab, can contain HTML
 * - fields : the fields of this section
 * 
 * Fields type: 
 * - text           : display an input text
 * - multi-text     : display multipe lines of input text
 * - textarea       : display a textarea
 * - select         : display a select input
 * - select2        : display ???
 * - checkbox       : display a checkbox
 * - multi-checkbox : display multiple checkboxes
 * - upload         : display the media library button with upload and library access
 * 
 * TEXT INPUT CLASSES
 * There are some options available for the text input used with the "class" argument: (validate options)
 *  ---> the class argument is optional <---
 * - default      : accept only a few inline HTML elements (a, b, em, i, strong)
 *     this is a "cover-all" fall-back when the class argument is not set
 * - numeric      : accept only numeric values
 * - multinumeric : accept numerci values separate by a comma (",")
 *     matches: -1 | 1 | -12,-23 | 12,23 | -123, -234 | 123, 234  | etc...
 * - nohtml       : accept the input only after stripping out all HTML, extra white space etc...
 * - url          : accept the input only when the url is a real email
 * - email        : accept the input only is the email is a validate email address
 *
 * TEXTAREA CLASSES
 * There are some options available for the textarea used with the "class" argument: (validate options)
 *  ---> the class argument is optional <---
 * - default         : accept only limited HTML elements 
 *    (a, b, blockquote, br, dd, dl, em, i, ul, ol, li, p, q, strong, h1, h2, h3, h4, h5, h6)
 * - inlinehtml      : accepct inline HTML
 * - nohtml          : accept the input only after stripping out all HTML, extra white space etc...
 * - allowlinebreaks : it's the same rule that "nohtml" but this one accepts linebreaks
 * - editor          : display a textarea with editor (accept all HTML)
 *
 *
 * Problems to fix:
 * - ReCoding the base function "kiwip_validate_options" with objects in folders (more flexible)
 * - Add more rules
 */


/* Define sections */
$sections[] = array(
				'icon' => KIWIP_URL.'/img/icons/copy.png',
				'title' => 'Internal Options',
				'slug' => 'internal_section',
				'desc' => '<p class="description">This is the description of the section.<br />(you can add some html here)</p>',
				'fields' => array(
					array(
						"id"      => 'home-page-id',
						"title"   => __('The <i><b>home</b></i> page ID', $template_textdomain),
						"desc"    => __('Insert the ID of the page you want to display on home.', $template_textdomain),
						"type"    => 'text',
						"class"   => 'numeric'
						),
					array(
						"id"      => 'reseller-page-id',
						"title"   => __('The <i><b>reseller</b></i> page ID', $template_textdomain),
						"desc"    => __('Insert the ID of the reseller page.', $template_textdomain),
						"type"    => 'text',
						"class"   => 'numeric'
						)
					)
				);

/**
 * STEP 4: Define help tabs (optional)
 * The help tabs are at the bottom of the admin panel on the options page and display help or more
 * informations about the fields to fill them. The help tabs can display HTML.
 * 
 * SET THE TAB
 * To degine the tab, you have three elements:
 * - id      : the slugable tab ID
 * - title   : the tab title 
 * - content : the tab content (allow all HTML tags), you can include a php file here
 *
 */
/* Define Helps */
$args['help_tabs'][] = array(
							'id' => 'kiwip-option-1',
							'title' => 'Theme Information 1',
							'content' => '<p>This is the tab content, HTML is allowed. Information 1</p>'
							);


/**
 * STEP 5: Define sidebar for the help tabs (optional)
 * The sidebar allow you to show additional informations about your help tabs like a list 
 * of external links, or simple credentials paragraph.
 * There is no sidebar by default.
 * You can include a php file here.
 */
/* Define Helps */							
$args['help_sidebar'] = '<p>This is the sidebar content, HTML is allowed.</p>';

/**
 * STEP 6: Define extra tabs (optional)
 */
$extra_tabs = array();

/**
 * STEP 7: Create de theme options page
 * Once you have personalized ans complete all your options like $args, $sections, $extra_tabs,
 * you can call the "add_theme_options" method to create the page.
 *
 * method add_theme_options ( array $args, array $sections, array $extra_tabs)
 *
 * 
 */
$KiwipFramework->add_theme_options($args, $sections, $extra_tabs);


/*
$options = get_option('Elda');
echo "<pre>";
print_r($options);
echo "</pre>";


$options = get_option('Kiwip');
echo "<pre>";
print_r($options);
echo "</pre>";
die();
*/

