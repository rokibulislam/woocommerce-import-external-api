<?php

namespace WCMystore\WooCommerce;

class WCOrderProducts {

    public function get_ordered_products( $order_obj ) {
       
        $items = $order_obj->get_items();
       
        $products = [];
       
        foreach ( $items as $item ) {
            $id = $item->get_product_id();
            $product = new \WC_Product( $id );

            $products[] = [
                'id'           => $id,
                'name'         => $product->get_name(),
                'slug'         => $product->get_slug(),
                'total'        => $item->get_total(),
                'quantity'     => $item->get_quantity(),
            ];
        }

        return $products;
    }
}
