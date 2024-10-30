<?php

if( ! class_exists( 'CT_List_Table' ) ) {
    require_once(plugin_dir_path( dirname( __FILE__ ) ) . 'libraries/class-ct-list-table.php');
}

/**
 * Class for displaying recorded Cliptakes interviews
 * in a plugin admin page
 * 
 * @link       https://cliptakes.com
 * @since      1.0.0
 *
 * @package    Cliptakes
 * @subpackage Cliptakes/admin
 */
class Cliptakes_Interview_List_Table extends CT_List_Table  {
    /*
	 * Call the parent constructor to override the defaults $args 
	 * @since 1.0.0
	 */
    private $interview_data = array();
    private $orderby;
    private $order;

	public function __construct() {		
		parent::__construct( array( 
				'plural'	=>	_n('interview', 'interviews', 2, 'cliptakes'),	// Plural value used for labels and the objects being listed.
				'singular'	=>	_n('interview', 'interviews', 1, 'cliptakes'),	// Singular label for an object being listed, e.g. 'post'.
				'ajax'		=>	true,		    // If true, the parent class will call the _js_vars() method in the footer		
			) );
	}	
    // just the barebone implementation.
    public function get_columns() {		
        $table_columns = array(		 
            'firstName' => __('First Name', 'cliptakes'),
            'lastName' => __('Last Name', 'cliptakes'),		
            'link' => __('Link', 'cliptakes'),
            'contact' => __('Contact', 'cliptakes'),
            'template' => __('Template', 'cliptakes'),
            'recorded' => __('Date', 'cliptakes'),
            'viewCounter' => __('Views', 'cliptakes'),
        );		

        /**
		 * Filters the columns for the interview list table.
		 *
		 * @since 1.3.0
		 *
		 * @param string $table_columns Columns as an Array of IDs and column names.
		 */
        $table_columns = apply_filters('cliptakes_interview_list_columns', $table_columns);
        return $table_columns;		   
    }	
    
    public function no_items() {
        echo '<div id="ctadmin-interview-list-no-items">' . __('No interviews available.', 'cliptakes') . '</div>';
    }
    
    // filter the table data based on the search key
    public function filter_table_data( $table_data, $search_key ) {
        $filtered_table_data = array_values( array_filter( $table_data, function( $row ) use( $search_key ) {
            foreach( $row as $key => $row_val ) {
                if (
                    in_array( strtolower($key),
                        array("firstname", "lastname", "contact", "template")
                    )
                ) {     
                    if( stripos( $row_val, $search_key ) !== false ) {
                        return true;
                    }	
                }			
            }			
        } ) );

        return $filtered_table_data;

    }

    public function prepare_items() { 
        $per_page = ! empty( $this->items_per_page ) && 0 != $this->items_per_page ? $this->items_per_page : 15;    
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array(
            'contact' => array('contact', false),
            'firstName' => array('firstName', false),
            'lastName' => array('lastName', false),
            'recorded' => array('recorded', false),
            'template' => array('template', false),
        );        
        $this->_column_headers = array($columns, $hidden, $sortable);

        // order data        
        $orderby = sanitize_text_field( $_POST['orderby'] );
        $this->orderby = ! empty( $orderby ) && '' != $orderby ? $orderby : 'recorded';
        $order = sanitize_text_field( $_POST['order'] );
        $this->order = ! empty( $order ) && '' != $order ? $order : 'desc';
        $this->set_pagination_args(
            array(
               'total_items'	=> $this->total_items,
               'per_page'	    => $per_page,
               'total_pages'	=> ceil( $total_items / $per_page )
            )
        );
    }

    function column_default( $item, $column_name ) {
        $actions = array(
            'embed'  => sprintf(
                '<a href="#" class="ctadmin-embed-new-page-link" data-link="%s" data-first-name="%s" data-last-name="%s" title="' . __('Create a new page and embed this video.', 'cliptakes') . '">' . __('Embed (New Page)', 'cliptakes') . '</a>',
                $item['link'],
                $item['firstName'],
                $item['lastName']
            ),
            'download'    => '<a href="https://files.cliptakes.com/'.$item["link"].'.mp4" target="_blank" download="'.$item["firstName"].' '.$item["lastName"].'.mp4" title="' . __('Download Interview', 'cliptakes') . '">' . __('Download', 'cliptakes') . '</a>',
            'delete'  => '<a href="#" class="ctadmin-delete-interview-link" data-interview-id="'.$item['interviewId'].'"title="' . __('Permanently delete this interview.', 'cliptakes') . '">' . __('Delete', 'cliptakes') . '</a>',
        );
        if ( str_starts_with($column_name, "custom") ) {
            if (!array_key_exists("customInfo", $item)) return "";
            // replace accented letters to the corresponding unaccented letters
            $custom_info_key = iconv('UTF-8', 'ASCII//TRANSLIT', $column_name);
            // remove the "custom" prefix and convert to lowercase, removing non-alphabetic characters
            $custom_info_key = preg_replace('/[^a-zA-Z]+/', '', strtolower(substr($column_name, 6)));
            return array_key_exists($custom_info_key, $item["customInfo"]) ? $item["customInfo"][$custom_info_key] : "";
        }
        switch( $column_name ) {                
            case 'firstName':
                return sprintf('%1$s %2$s', $item[ $column_name ], $this->row_actions($actions) );
            case 'link':
                return '<a href="https://api.cliptakes.com/share/'.$item["link"].'" target="_blank">' . __('Watch Video', 'cliptakes') . '</a>';
            default:
                return array_key_exists($column_name, $item) ? $item[ $column_name ] : "";
        }
    }
    
	function ajax_response() {
		check_ajax_referer( 'cliptakes_settings' );

		$this->prepare_items();

		extract( $this->_args );
		extract( $this->_pagination_args, EXTR_SKIP );

		ob_start();
		if ( ! empty( sanitize_text_field( $_REQUEST['no_placeholder'] ) ) )
			$this->display_rows();
		else
			$this->display_rows_or_placeholder();
		$rows = ob_get_clean();

		ob_start();
		$this->print_column_headers();
		$headers = ob_get_clean();

		ob_start();
		$this->pagination('top');
		$pagination_top = ob_get_clean();

		ob_start();
		$this->pagination('bottom');
		$pagination_bottom = ob_get_clean();

		$response = array( 'rows' => $rows );
		$response['pagination']['top'] = $pagination_top;
		$response['pagination']['bottom'] = $pagination_bottom;
		$response['column_headers'] = $headers;

		if ( isset( $total_items ) )
			$response['total_items_i18n'] = sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) );

		if ( isset( $total_pages ) ) {
			$response['total_pages'] = $total_pages;
			$response['total_pages_i18n'] = number_format_i18n( $total_pages );
		}

		die( json_encode( $response ) );
	}
}
