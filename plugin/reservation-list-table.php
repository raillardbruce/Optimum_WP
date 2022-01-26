<?php

class ReservationCCListTable extends WP_List_Table {

	function get_data() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'reservations';
		return $wpdb->get_results("SELECT * FROM $table_name WHERE type_reservation = 1", ARRAY_A);
	}

	function get_columns() {
		return array(
			'first_name' => 'Prénom',
			'last_name' => 'Nom de famille',
			'phone' => 'Numéro de téléphone',
			'type_sport' => 'Sport',
			'time_slot' => 'Créneau horaire'
		);
	}

	function get_sortable_columns() {
		return array(
			'id' => array('id', false),
		);
	}

	function column_default($item, $column_name) {
		return $item[$column_name];
	}
	
	function usort_reorder($a, $b) {
		$orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'id';
		$order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
		$result = strcmp($a[$orderby], $b[$orderby]);
		return ($order === 'asc') ? $result : -$result;
	}

	function prepare_items() {
		$data = $this->get_data();
		$columns = $this->get_columns();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, array(), $sortable);
		usort($data, array($this,'usort_reorder'));
		$this->process_bulk_action();
		$this->items = $data;
	}
}

class ReservationCPListTable extends WP_List_Table {

	function get_data() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'reservations';
		return $wpdb->get_results("SELECT * FROM $table_name WHERE type_reservation = 2", ARRAY_A);
	}

	function get_columns() {
		return array(
			'first_name' => 'Prénom',
			'last_name' => 'Nom de famille',
			'phone' => 'Numéro de téléphone',
			'type_sport' => 'Sport',
			'time_slot' => 'Créneau horaire'
		);
	}

	function get_sortable_columns() {
		return array(
			'id' => array('id', false),
		);
	}

	function column_default($item, $column_name) {
		return $item[$column_name];
	}
	
	function usort_reorder($a, $b) {
		$orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'id';
		$order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
		$result = strcmp($a[$orderby], $b[$orderby]);
		return ($order === 'asc') ? $result : -$result;
	}

	function prepare_items() {
		$data = $this->get_data();
		$columns = $this->get_columns();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, array(), $sortable);
		usort($data, array($this,'usort_reorder'));
		$this->process_bulk_action();
		$this->items = $data;
	}
}

class ReservationYogaListTable extends WP_List_Table {

	function get_data() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'reservations';
		return $wpdb->get_results("SELECT * FROM $table_name WHERE type_reservation = 3", ARRAY_A);
	}

	function get_columns() {
		return array(
			'first_name' => 'Prénom',
			'last_name' => 'Nom de famille',
			'phone' => 'Numéro de téléphone',
			'type_sport' => 'Sport',
		);
	}

	function get_sortable_columns() {
		return array(
			'id' => array('id', false),
		);
	}

	function column_default($item, $column_name) {
		return $item[$column_name];
	}
	
	function usort_reorder($a, $b) {
		$orderby = (!empty($_GET['orderby'])) ? $_GET['orderby'] : 'id';
		$order = (!empty($_GET['order'])) ? $_GET['order'] : 'asc';
		$result = strcmp($a[$orderby], $b[$orderby]);
		return ($order === 'asc') ? $result : -$result;
	}

	function prepare_items() {
		$data = $this->get_data();
		$columns = $this->get_columns();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, array(), $sortable);
		usort($data, array($this,'usort_reorder'));
		$this->process_bulk_action();
		$this->items = $data;
	}
}