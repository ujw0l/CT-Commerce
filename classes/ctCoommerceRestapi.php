<?php
/**
 * 
 * 
 * 
 * 
 * this class handles rest api
 * @author ujw0l
 *
 */
class ctCommerceRestApi{


	/**
	 * Register the routes for the objects of the controller.
	 */
	public static function register_endpoints() {
		// register endpoints
		register_rest_route( 'ctc/v1', 'products', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => array($this, 'ctc_get_products' ),
        ) );

        
		register_rest_route( 'ctc/v1', '/posts', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => array($this,  'ctc_get_products_post'),
        ) );
	}

	/**
	 * Get all of the products 
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Request
	 */
	public function ctc_get_products( $request ) {
        $ctcFrontEndProcessing = new ctCommerceFrontendProcessing();

        $products =  $ctcFrontEndProcessing->ctcGetProducts();
        $restData = array_map(function($product){

            foreach($product as $key=>$value):
                if($key==='primaryImage'):
                    if(!empty($product[$key])):
                         $product[$key] = wp_get_attachment_url($product[$key]);
                    else:
                        unset($product[$key]);
                    endif; 
                elseif($key ==='avilableProducts'):
                    $product['productVariation'] = explode(',',str_replace('~',':',$product[$key])); 
                    unset($product[$key]);
                   elseif($key === 'addtionalImages'):
                    if(!empty($product[$key])):
                         $product[$key] = array_map(function($img){
                        return wp_get_attachment_url($img);
                    },explode(',',$product[$key])); 
                    else:
                        unset($product[$key]);
                    endif; 
                   
                 endif;   
            endforeach;   
               return $product;

        },$products);
		// @TODO do your magic here
        return new WP_REST_Response( $restData, 200 );
        
       
	}

	/**
	 * Add a new candy
	 *
	 * @param WP_REST_Request $request Full data about the request.
	 * @return WP_Error|WP_REST_Request
	 */
	public static function ctc_get_products_post( $request ) {
     $ctcFrontEndProcessing = new ctCommerceFrontendProcessing();
     $products =  $ctcFrontEndProcessing->ctcGetProductsPost();

     $restData = array_map(function($product){

            foreach($product as $key=>$value):
                if($key==='productPostId'):
                  $product['postPermaLink'] = get_permalink($product[$key]);
                    unset($product['productPostId']);
                endif;  
            endforeach; 
             return $product;
           
        },$products);

		// @TODO do your magic here
		return new WP_REST_Response( $restData, 200 );
	}
}


