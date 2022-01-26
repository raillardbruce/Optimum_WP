<?php
/*
Plugin Name: Reservations
Description: This is a plugin.
Author: Joe Doe
Version: 1.0.1
*/

// Create the database

function reservation_database()
{
	global $wpdb;

	$posts = $wpdb->prefix . 'posts';
	$reservations = $wpdb->prefix . 'reservations';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE IF NOT EXISTS $reservations (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		first_name varchar(55) NOT NULL,
		last_name varchar(55) NOT NULL,
		phone INT(6) NOT NULL,
		type_reservation INT(1) NOT NULL,
		type_sport VARCHAR(55),
		time_slot VARCHAR(255),
		PRIMARY KEY (id)
	) $charset_collate;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
	add_option('reservation_db_version', '1.0');
}

register_activation_hook(__FILE__, 'reservation_database');

// Add plugin to admin

function add_plugin_to_admin()
{
	function reservation_content()
	{
	}

	add_menu_page('Reservations', 'Reservations', 'manage_options', 'reservation-plugin', 'reservation_content');

	function reservation_cc_content()
	{
		echo "<h1>Les révervations pour des cours collectifs</h1>";
		echo "<div style='margin-right:20px'>";

		if (class_exists('WP_List_Table')) {
			require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
			require_once(plugin_dir_path(__FILE__) . 'reservation-list-table.php');
			$reservationListTable = new ReservationCCListTable();
			$reservationListTable->prepare_items();
			$reservationListTable->display();
		} else {
			echo "WP_List_Table n'est pas disponible.";
		}

		echo "</div>";
	}

	add_submenu_page('reservation-plugin', 'Les révervations pour des cours collectifs', 'Cours collectifs', 'manage_options', 'reservation-cours-collectifs', 'reservation_cc_content');


	function reservation_cp_content()
	{
		echo "<h1>Les révervations pour des cours particuliers</h1>";
		echo "<div style='margin-right:20px'>";

		if (class_exists('WP_List_Table')) {
			require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
			require_once(plugin_dir_path(__FILE__) . 'reservation-list-table.php');
			$reservationListTable = new ReservationCPListTable();
			$reservationListTable->prepare_items();
			$reservationListTable->display();
		} else {
			echo "WP_List_Table n'est pas disponible.";
		}

		echo "</div>";
	}

	add_submenu_page('reservation-plugin', 'Les révervations pour des cours particuliers', 'Cours particuliers', 'manage_options', 'reservation-cours-particuliers', 'reservation_cp_content');


	function reservation_yoga_content()
	{
		echo "<h1>Les révervations pour des séances de yoga</h1>";
		echo "<div style='margin-right:20px'>";

		if (class_exists('WP_List_Table')) {
			require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
			require_once(plugin_dir_path(__FILE__) . 'reservation-list-table.php');
			$reservationListTable = new ReservationYogaListTable();
			$reservationListTable->prepare_items();
			$reservationListTable->display();
		} else {
			echo "WP_List_Table n'est pas disponible.";
		}

		echo "</div>";
	}

	add_submenu_page('reservation-plugin', 'Les révervations pour des séances de yoga', 'Séances de Yoga', 'manage_options', 'reservation-seances-yoga', 'reservation_yoga_content');
}

add_action('admin_menu', 'add_plugin_to_admin');

// Cette fonction sert à récupérer les séances de sport en fonction de leurs catégories
function select_postid_by_category($slug_category)
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'reservations';

	if (isset($_POST['reservations_cc'])) {
		$first_name = sanitize_text_field($_POST["first_name"]);
		$last_name = sanitize_text_field($_POST["last_name"]);
		$phone = sanitize_text_field($_POST["phone"]);
		$type_reservation = 1;
		$type_sport = 'Cross training';
		$time_slot = sanitize_text_field($_POST["time_slot"]);





		if ($first_name != '' && $last_name != '' && $phone  != '' && $type_reservation  != '') {

			$wpdb->insert(
				$table_name,
				array(
					'first_name' => $first_name,
					'last_name' => $last_name,
					'phone' => $phone,
					'type_reservation' => $type_reservation,
					'type_sport' => $type_sport,
					'time_slot' => $time_slot,
				)
			);
		}
	}

	$table_name_relation = $wpdb->prefix . 'term_relationships';
	$table_terms = $wpdb->prefix . 'terms';
	$table_term_taxonomy = $wpdb->prefix . 'term_taxonomy';

	$taxonomy_id = $wpdb->get_results("SELECT term_id FROM $table_terms WHERE slug = '$slug_category'", ARRAY_A);
	$taxonomy_id = implode(", ", $taxonomy_id[0]);

	$term_taxonomy_id = $wpdb->get_results("SELECT term_taxonomy_id FROM $table_term_taxonomy WHERE term_id = $taxonomy_id", ARRAY_A);
	$term_taxonomy_id = implode(", ", $term_taxonomy_id[0]);

	$infos_cc = $wpdb->get_results("SELECT object_id FROM $table_name_relation WHERE term_taxonomy_id = $term_taxonomy_id", ARRAY_A);
	$infos_cc = implode(", ", $infos_cc[0]);

	$timeslot_cc = get_post_meta($infos_cc, '_ma_valeur', true);

	echo "<form method='POST'>";
	echo "<input type='text' name='first_name' placeholder='Prénom' style='width:100%; color:#000000;' required>";
	echo "<input type='text' name='last_name' placeholder='Nom de famille' style='width:100%; color:#000000; text-tranform:uppercase;' required>";
	echo "<input type='tel' name='phone' placeholder='Numéro de téléphone' style='width:100%; color:#000000;' required>";
	echo "<p>Sélectionner votre sport</p>";

	echo "<select name='time_slot'>";

	foreach ($timeslot_cc as $timeslot_cc_0 => $timeslot_cc_1) {
		if ($timeslot_cc_1 === '') {
			unset($timeslot_cc[$timeslot_cc_0]);
		}
	}

	foreach ($timeslot_cc as $timeslot) {
		echo $timeslot;
		echo '<option value="' . $timeslot . '">' . $timeslot . '</option>';
	}

	echo "</select>";
	echo "<input type='submit' name='reservations_cc' value='Envoyez'>";
	echo "</form>";
}

// Create the form

function show_reservation_cross_training_form()
{
	ob_start();

	select_postid_by_category('cross-training');

	return ob_get_clean();
}

add_shortcode('show_reservation_cross_training_form', 'show_reservation_cross_training_form');

function show_reservation_cardio_training_form()
{
	ob_start();

	select_postid_by_category('cardio-training');

	return ob_get_clean();
}

add_shortcode('show_reservation_cardio_training_form', 'show_reservation_cardio_training_form');

function show_reservation_renforcement_musculaire_form()
{
	ob_start();

	select_postid_by_category('renforcement-musculaire');

	return ob_get_clean();
}

add_shortcode('show_reservation_renforcement_musculaire_form', 'show_reservation_renforcement_musculaire_form');

function show_reservation_preparation_physique_form()
{
	ob_start();

	select_postid_by_category('preparation-physique');

	return ob_get_clean();
}

add_shortcode('show_reservation_preparation_physique_form', 'show_reservation_preparation_physique_form');

function show_reservation_yoga_form()
{
	ob_start();

	select_postid_by_category('seance-de-yoga');

	return ob_get_clean();
}

add_shortcode('show_reservation_yoga_form', 'show_reservation_yoga_form');

// Add post type 'events'

function cours_init()
{
	$labels_post = array(

		// Le nom au pluriel
		'name' => 'Séances de sport',

		// Le libellé affiché dans le menu
		'singular_name' => 'Séance de sport',

		// Les différents libellés de l'administration
		'menu_name' => 'Séances de sport',
		'add_new' => 'Ajouter',
		'add_new_item' => 'Ajouter',
		'edit_item' => 'Modifier',
		'new_item' => 'Ajouter',
		'all_items' => 'Tout',
		'view_item' => 'Voir',
		'search_items' => 'Rechercher',
		'not_found' => 'Aucun trouvé',
		'not_found_in_trash' => 'Aucun trouvé trouvé dans la corbeille',
	);

	// On peut définir ici d'autres options pour notre custom post type
	$args_post = array(
		'labels' => $labels_post,
		'has_archive' => true,
		'public' => true,

		// On définit les options disponibles dans l'éditeur de notre custom post type ( un titre, un auteur...)
		'supports' => array('title', 'editor', 'excerpt', 'author', 'revisions', 'thumbnail'),

		// Différentes options supplémentaires
		'taxonomies' => array('types'),
		'rewrite' => array("slug" => "cours"),
		'menu_icon' => 'dashicons-universal-access',
		'show_in_rest' => true,
	);

	register_post_type('cours', $args_post);

	$labels_category = array(
		'name' => 'Types de séance',
		'parent_item' => 'Catégorie parente',
	);

	$args_category = array(
		'labels' => $labels_category,
		'public' => true,
		'show_in_rest' => true,
		'hierarchical' => true,
	);

	register_taxonomy('types', 'cours', $args_category);
}

add_action('init', 'cours_init');

// Add meta box date to event

function add_timeslot_meta_box()
{
	function timeslot_cours($post)
	{
		$val = get_post_meta($post->ID, '_ma_valeur', true);

		echo '<div id="cour_metabox">';

		foreach ($val as $value => $values) {
			if ($values === '') {
				unset($val[$value]);
			}
		}

		foreach ($val as $timeslot) {
			echo '<input style="width:100%;" class="class_cour_metabox" type="text" name="timeslot[]" value="' . $timeslot . '" />';
		}

		echo '<input style="width:100%;" class="class_cour_metabox" type="text" name="timeslot[]" value="" /></div>';
		echo '<button onclick="cloner()">Ajouter un créneau</button>';
		echo '<script type="text/javascript">
		function cloner() {
			var clone = document.querySelector("input.class_cour_metabox").cloneNode(true);
			clone.value = "";
			document.getElementById("cour_metabox").appendChild(clone);
		}
	</script>';
	}

	add_meta_box('id_timeslot_cours', 'Créneau horaire', 'timeslot_cours', 'cours', 'side', 'default');
}

add_action('add_meta_boxes', 'add_timeslot_meta_box');

// Update meta on event post save

function save_timeslot_cours($post_id)
{

	if (isset($_POST['timeslot']) && $_POST['timeslot'] !== "") {
		update_post_meta($post_id, '_ma_valeur', $_POST['timeslot']);
	}
}

add_action('save_post', 'save_timeslot_cours');

// Add event post type to home and main query

function add_event_post_type($query)
{
	if (is_home() && $query->is_main_query()) {
		$query->set('post_type', array('post', 'cours'));
		return $query;
	}
}

add_action('pre_get_posts', 'add_event_post_type');


function show_sessions($slug_category)
{
	echo '<div style="display: flex; justify-content: space-evenly;">';

	global $wpdb;

	$table_name_post = $wpdb->prefix . 'term_relationships';
	$table_terms = $wpdb->prefix . 'terms';
	$table_term_taxonomy = $wpdb->prefix . 'term_taxonomy';

	$taxonomy_id = $wpdb->get_results("SELECT term_id FROM $table_terms WHERE slug = '$slug_category'", ARRAY_A);
	$taxonomy_id = implode(", ", $taxonomy_id[0]);

	$term_taxonomy_id = $wpdb->get_results("SELECT term_taxonomy_id FROM $table_term_taxonomy WHERE term_id = $taxonomy_id", ARRAY_A);
	$term_taxonomy_id = implode(", ", $term_taxonomy_id[0]);



	$sessions_cc = $wpdb->get_results("SELECT object_Id FROM $table_name_post WHERE term_taxonomy_id = $term_taxonomy_id", ARRAY_A);

	foreach ($sessions_cc as $session_cc) {
		$session_cc_id = implode(", ", $session_cc);

		$session_cc = get_post($session_cc_id);

		$session_cc_title = apply_filters('the_title', $session_cc->post_title);
		$session_cc_thumbnail = get_the_post_thumbnail($session_cc_id);
		$session_cc_content = apply_filters('the_content', $session_cc->post_content);



		echo '<div class="card" style="width: 45%;">';
		echo '<div class="card-img-top">' . $session_cc_thumbnail . '</div>';
		echo '<div class="card-body">';
		echo '<h5 class="card-title">' . $session_cc_title . '</h5>';
		echo '<p class="card-text">' . $session_cc_content . '</p>';

		$sessions_taxonomy = $wpdb->get_results("SELECT term_taxonomy_id FROM $table_name_post WHERE object_Id = $session_cc_id", ARRAY_A);
		foreach ($sessions_taxonomy as $session_taxonomy_01) {
			$session_taxonomy_id = implode(", ", $session_taxonomy_01);
			if ($session_taxonomy_id == 5) {
				echo '<a href="http://localhost:8888/fitness-nc/reservation-cross-training/" class="btn btn-primary">Inscription</a>';
			}
			if ($session_taxonomy_id == 4) {
				echo '<a href="http://localhost:8888/fitness-nc/reservation-cardio-training/" class="btn btn-primary">Inscription</a>';
			}
		}
		echo '</div></div>';
	}

	echo '</div>';
}


// Short code to display event date meta data

function show_sessions_cc()
{
	ob_start();

	show_sessions('cour-collectif');

	return ob_get_clean();
}

add_shortcode('show_sessions_cc', 'show_sessions_cc');


function show_sessions_cp()
{
	ob_start();

	show_sessions('cour-particulier');

	return ob_get_clean();
}

add_shortcode('show_sessions_cp', 'show_sessions_cp');


function show_sessions_yoga()
{
	ob_start();

	echo '<div style="display: flex; justify-content: space-evenly;">';

	global $wpdb;

	$table_name_post = $wpdb->prefix . 'term_relationships';

	$sessions_cc = $wpdb->get_results("SELECT object_Id FROM $table_name_post WHERE term_taxonomy_id = 10", ARRAY_A);

	foreach ($sessions_cc as $session_cc) {
		$session_cc_id = implode(", ", $session_cc);

		$session_cc = get_post($session_cc_id);

		$session_cc_title = apply_filters('the_title', $session_cc->post_title);
		$session_cc_thumbnail = get_the_post_thumbnail($session_cc_id);
		$session_cc_content = apply_filters('the_content', $session_cc->post_content);



		echo '<div class="card" style="width: 45%;">';
		echo '<div class="card-img-top">' . $session_cc_thumbnail . '</div>';
		echo '<div class="card-body">';
		echo '<h5 class="card-title">' . $session_cc_title . '</h5>';
		echo '<p class="card-text">' . $session_cc_content . '</p>';
		echo '<a href="http://localhost:8888/fitness-nc/formulaire-de-reservation-yoga/" class="btn btn-primary">Inscription</a>';
		echo '</div></div>';
	}

	echo '</div>';

	return ob_get_clean();
}

add_shortcode('show_sessions_yoga', 'show_sessions_yoga');
