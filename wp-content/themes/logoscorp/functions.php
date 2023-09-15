<?php
/* ---------------------------------------------------------------------------
 * Logoscorp Stuffs
 * --------------------------------------------------------------------------- */
// Hide Admin Bar in frontend
//show_admin_bar(false);

function my_theme_enqueue_styles() { 
    // enqueue the child stylesheet
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    // Enqueue Logoscorp CSS
    wp_dequeue_style('logoscorp');
    wp_enqueue_style('logoscorp', get_stylesheet_directory_uri() . '/css/logoscorp.css');
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );


// Add Logoscorp class to the body of all pages
add_filter('body_class','my_body_classes');
function my_body_classes($classes) {

	$classes[] = 'logoscorp';

	return $classes;

}

// Enqueue logoscorp.js
function logoscorp_enqueue_scripts() {
    wp_enqueue_script(
		'logoscorp-scripts',
		get_stylesheet_directory_uri() . '/js/logoscorp.js',
		array('jquery')
	);
}

add_action( 'wp_enqueue_scripts', 'logoscorp_enqueue_scripts' );

/**
* cronjob_import_offices_tealca() Function
* 
* This function allow to import tealca offices from api using wordpress virtual cronjob
*
*/
function cronjob_import_offices_tealca() {
    //if(!file_exists('/app/tealca-oficinas.log')) {
    $fp = fopen('/app/tealca-oficinas.log', 'a+');
    try {
        include('/app/wp-content/plugins/tealca-oficinas/admin/Functionalities.php');
        date_default_timezone_get('America/Caracas');
        $time = date("Y-m-d h:i:s a", time());
        fwrite($fp, "* PROCESO DE IMPORTACION DE OFICINAS TEALCA - " . $time . " *\n");
        fwrite($fp, "LLamando al cronjob del proceso de importación de oficinas de tealca desde ".__DIR__." - $time\n");
        $cronJobFileLogs = '/app/tealca-oficinas.log';
        $cronJobImportOfficeFunctionalities = new Functionalities($cronJobFileLogs);
        $importResult = $cronJobImportOfficeFunctionalities->logosImportGetOffices(true);
        fwrite($fp, json_encode($importResult));
        fwrite($fp, "\n\n");
    }
    catch(\Throwable $th) {
        fwrite($fp, "Error- " . $th->getMessage() . "\n");
    }
    fclose($fp);
    //}
}

/*================================================
#Load the translations from the child theme folder
================================================*/
function wpcninja_translation() {
	load_child_theme_textdomain( 'Divi', get_stylesheet_directory() . '/lang/theme/' );
	load_child_theme_textdomain( 'et_builder', get_stylesheet_directory() . '/lang/builder/' );
}
add_action( 'after_setup_theme', 'wpcninja_translation' );

function cyb_session_start() {
	if( ! session_id() ) {
		session_start();
	}
}
add_action('init', 'cyb_session_start', 1);

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('estilos-css', get_stylesheet_directory_uri() . '/estilos/stylesheet.css');
});

//Change post order for taxonomy ubicaciones used in ubicaciones template 
function change_blog_module_order_ubicaciones( $query ) {
    if(is_tax( 'ubicaciones' )) {
        $query->set( 'orderby', 'title' );
        $query->set( 'order', 'ASC' );
    }
}
add_action( 'pre_get_posts', 'change_blog_module_order_ubicaciones' );



 
