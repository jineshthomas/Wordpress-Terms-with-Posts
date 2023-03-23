?>
<style>
    .result_menu{display: none;}
</style>

<?php

//function for sub terms post list
function display_menu_posts( $parent_term_id ) {

    $child_terms = get_terms( array(
        'taxonomy' => 'menu-category',
        'parent'   => $parent_term_id,
        'orderby'  => 'name', // order terms alphabetically by name
        'order'    => 'ASC',  // show terms in ascending order
    ) );


    if ( ! empty( $child_terms ) ) {
        echo '<ul>';
        foreach ( $child_terms as $child_term ) {
            echo '<li><h2>' . $child_term->name . '</h2>';

            $args = array(
                        'post_type' => 'menu',
                        'post_status' => 'publish',
                        'posts_per_page' => -1,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'menu-category',
                                'field'    => 'term_id',
                                'terms'    => $child_term->term_id,
                                'include_children' => false, // only retrieve posts belonging to the child category
                            ),
                        ),
                        'exclude_tree' => $parent_term_id, // exclude posts belonging to the parent category
                        'orderby' => 'post_name', // order posts by slug
                        'order' => 'ASC', // show posts in ascending order
                    );



            $query = new WP_Query( $args );

            if ( $query->have_posts() ) :
                echo '<ul>';
                while ( $query->have_posts() ) : $query->the_post();
                    echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
                endwhile;
                echo '</ul>';
            endif;

            wp_reset_postdata();

            display_menu_posts( $child_term->term_id );

            echo '</li>';
        }
        echo '</ul>';
    }
}




$taxonomy_name  = 'menu-category';
$terms          = get_terms( array(
                        'taxonomy' => $taxonomy_name ,
                        'hide_empty' => true,
                        'parent' => 0,
                        'orderby'  => 'name', // order terms alphabetically by name
                        'order'    => 'ASC',  // show terms in ascending order
                    ) );

$results        = '';
$menu_count     = 1;

foreach ( $terms as $term ) { 

    $class_add  = '';
    $term_id    = $term->term_id;

    if ($term->parent == 0) {
        if($menu_count == 1){
            $class_add = 'first_menu';
        } 
        $name       = $term->name;
        $slug       = $term->slug;
        echo '<a href="javascript:void(0)" class="main_menu_links '.$class_add.'" data-slug="'.$slug.'">'. $name .' </a> / ';

        $menu_count ++;
    }
}

foreach ( $terms as $term ) {

    $term_id   = $term->term_id;

    $term = get_term_by('term_id', $term_id , $taxonomy_name);
    $name = $term->name;

    $term_slug = $term->slug; 

    echo '<div class="'.$term_slug.' result_menu"> <h2>'.$name.'</h2>';

    $termchildrens   = get_term_children( $term_id, $taxonomy_name );


    //code for sub term missing post list
    $args = array(
        'post_type' => 'menu',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'tax_query' => array(
            array(
                'taxonomy' => $taxonomy_name,
                'field' => 'term_id',
                'terms' => $term_id,
                'include_children' => false,
                'has_children' => false,
            ),
        ),
        'parent' => 0,
        'orderby' => 'title',
        'order' => 'ASC',
    );

    $parent_posts_query = new WP_Query( $args );

    if ( $parent_posts_query->have_posts() ) {
        echo '<ul>';
        while ( $parent_posts_query->have_posts() ) {
            $parent_posts_query->the_post();
            echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
        }
        echo '</ul>';
        wp_reset_postdata();
    }

    //function call for sub terms post list
    if($termchildrens){
        display_menu_posts( $term_id );
    }
    echo '</div>';

}

?>


<script type="text/javascript">

    var f_menu = jQuery('.first_menu').attr('data-slug');
    
    jQuery('.'+f_menu).show();

    jQuery(document).on('click','.main_menu_links',function(){

        var menu_slug = jQuery(this).attr('data-slug');
        jQuery('.result_menu').hide();
        jQuery('.'+menu_slug).show();
    })
</script>
