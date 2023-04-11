<?php 
/**
 * Plugin Name: Search by slug
 * Plugin URI: 
 * Description: Search by slug post, page, menu
 * Version: 1.0.1
 * Author: Andrii Bezvesilnyi
 * Author URI: 
 *
 * 
 */


if(is_admin()  ) {


    function slug_search_admin($search, $wp_query) {
     

        global $wpdb;
        if( empty($search)) {
            return $search;
        }

        $qwe = $wp_query->query_vars;
        $n = !empty($q['exact'])?'':'%';

        $qwe_s = $qwe['s'];
        $search = "";
        $searchhand = "";
      
        if(str_contains( $qwe_s, "slug:")) {
            
   

            foreach( (array) $qwe['search_terms'] as $item) {
                $item = esc_sql($wpdb->esc_like($item));
                $item = str_replace(["post_title","slug:"], ["post_name",""], $item);
     
                $search .= "{$searchhand}($wpdb->posts.post_name LIKE '{$n}{$item}{$n}')"; 
                $searchhand = " OR ";
                
            }

            if ( ! empty( $search ) ) {
                $search = " AND ({$search}) ";
                if ( ! is_user_logged_in() )
                    $search .= " AND ($wpdb->posts.post_password = '') ";
            }

            $wp_query->set( 'orderby', 'post_name' );
       
           return $search;
        }
       
     
    }

    add_filter('posts_search', 'slug_search_admin', 500, 2);


    add_filter('acf/fields/relationship/query', 'my_acf_fields_relationship_query', 10, 3);
    function my_acf_fields_relationship_query( $args, $field, $post_id ) {
        $args['posts_per_page'] = -1;
       
        $search_text = $args['s'];

        if(str_contains( $search_text, "slug:")) {
           
            add_filter( 'post_search_columns', 'change_search_columns_filter', 10, 3 );
            $args['s'] = str_replace("slug:", "", $search_text);
            
        
        }
     
        return $args;
    }

   
    function change_search_columns_filter( $search_columns, $search, $query ){
        
        $search_columns = array_diff( $search_columns, [ 'post_title','post_excerpt', 'post_content' ] );
        $search_columns[] =  'post_name';
        
       
        return $search_columns;
    }

        

   
}