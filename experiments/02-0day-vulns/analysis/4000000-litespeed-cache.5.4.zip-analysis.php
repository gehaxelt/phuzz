<?php
/***
*
*Found actions: 1
*Found functions:1
*Extracted functions:1
*Total parameter names extracted: 1
*Overview: {'::bulk_edit_purge': {'wpmelon_adv_bulk_edit'}}
*
***/

/** Function ::bulk_edit_purge() called by wp_ajax hooks: {'wpmelon_adv_bulk_edit'} **/
/** Parameters found in function ::bulk_edit_purge(): {"post": ["type", "data"]} **/
function bulk_edit_purge()
	{
		if ( empty( $_POST[ 'type' ] ) || $_POST[ 'type' ] != 'saveproducts' || empty( $_POST[ 'data' ] ) ) return ;

		/*
		* admin-ajax form-data structure
		* array(
		*		"type" => "saveproducts",
		*		"data" => array(
		*			"column1" => "464$###0$###2#^#463$###0$###4#^#462$###0$###6#^#",
		*			"column2" => "464$###0$###2#^#463$###0$###4#^#462$###0$###6#^#"
		*		)
		*	)
		*/
		$stock_string_arr = array() ;
		foreach ( $_POST[ 'data' ] as $stock_value ) {
			$stock_string_arr = array_merge( $stock_string_arr, explode( '#^#', $stock_value ) ) ;
		}

		$lscwp_3rd_woocommerce = new self() ;

		if ( count( $stock_string_arr ) < 1 ) {
			return ;
		}

		foreach ( $stock_string_arr as $edited_stock ) {
			$product_id = strtok( $edited_stock, '$' );
			$product = wc_get_product( $product_id ) ;

			if ( empty( $product ) ) {
				do_action( 'litespeed_debug', '3rd woo purge: ' . $product_id . ' not found.' ) ;
				continue ;
			}

			$lscwp_3rd_woocommerce->purge_product( $product );
		}
	}


