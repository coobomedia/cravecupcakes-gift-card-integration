<?php
function tna_edit_taxonomy_args( $args, $tax_slug, $cptui_tax_args ) {
    // Alternatively, you can check for specific taxonomies.
    if ( 'cake_bakeshop_item' === $tax_slug ) {
        $args['meta_box_cb'] = false;
    }
    return $args;
}
add_filter( 'cptui_pre_register_taxonomy', 'tna_edit_taxonomy_args', 10, 3 );
