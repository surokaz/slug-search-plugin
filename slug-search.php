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
        $n = !empty($qwe['exact'])?'':'%';

        $qwe_s = $qwe['s'];
        $search = "";
        $searchhand = "";
       
           foreach( (array) $qwe['search_terms'] as $item) {
                $item = esc_sql($wpdb->esc_like($item));

                if(str_contains( $qwe_s, "slug:")) {
                    $item = str_replace(["post_title","slug:"], ["post_name",""], $item);
                }
                $search .= "{$searchhand}($wpdb->posts.post_name LIKE '{$n}{$item}{$n}')"; 
                $searchhand = " AND ";
                
            }

            if ( ! empty( $search ) ) {
                $search = " AND ({$search}) ";
                if ( ! is_user_logged_in() )
                    $search .= " AND ($wpdb->posts.post_password = '') ";
            }

            // $wp_query->set( 'orderby', 'post_name' );
            return $search;
      
        

        
       
     
    }

    add_filter('posts_search', 'slug_search_admin', 500, 2);
       

   
}