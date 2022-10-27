<?php
/**
 * Plugin Name: Extend GraphQL filters with ACF fields
 * Description: Extend GraphQL filters with ACF fields
 * Version: 1.0 | 2022.10.01
 * Author: Viktor Strelianyi | vstr.dev@gmail.com
 * Text Domain: vs-extend-graphql-filters
*/

function vs_extend_graphql_filters_init() {

	// ADD CUSTOM GRAPHQL FILTERS

	// BIDS
	add_action('graphql_register_types', function () {
		$customposttype_graphql_single_name = "Bid";

		// add lot ID filter field
		register_graphql_field('RootQueryTo' . $customposttype_graphql_single_name . 'ConnectionWhereArgs', 'lotId', [
			'type' => 'ID', // String/Integer/ID
			'description' => 'filter by this lot ID'
		]);

		register_graphql_field('RootQueryTo' . $customposttype_graphql_single_name . 'ConnectionWhereArgs', 'auctionId', [
			'type' => 'Integer', // String/Integer/ID
			'description' => 'filter by this auction ID'
		]);

		// add bid status filter field
		register_graphql_field('RootQueryTo' . $customposttype_graphql_single_name . 'ConnectionWhereArgs', 'bidStatus', [
			'type' => 'String', // String/Integer/ID
			'description' => 'filter by this bid status'
		]);

		// add bid status filter field
		register_graphql_field('RootQueryTo' . $customposttype_graphql_single_name . 'ConnectionWhereArgs', 'type', [
			'type' => 'String', // String/Integer/ID
			'description' => 'filter by this bid type'
		]);

		// add user name filter field
		register_graphql_field('RootQueryTo' . $customposttype_graphql_single_name . 'ConnectionWhereArgs', 'userName', [
			'type' => 'ID', // String/Integer/ID
			'description' => 'filter by this username'
		]);
	});

	// ARTISTS
	add_action('graphql_register_types', function () {
		$customposttype_graphql_single_name = "Artist";

		// add artist show filter field
		register_graphql_field('RootQueryTo' . $customposttype_graphql_single_name . 'ConnectionWhereArgs', 'show', [
			'type' => 'String', // String/Integer/ID
			'description' => 'filter by show field'
		]);
	});

	// AUCTIONS
	add_action('graphql_register_types', function () {
		$customposttype_graphql_single_name = "Auction";

		// add artist show filter field
		register_graphql_field('RootQueryTo' . $customposttype_graphql_single_name . 'ConnectionWhereArgs', 'auctionStatus', [
			'type' => 'String', // String/Integer/ID
			'description' => 'filter by status field'
		]);
	});

	// SHOP ITEMS
	add_action('graphql_register_types', function () {
		$customposttype_graphql_single_name = "ShopItem";

		// add show item show filter field
		register_graphql_field('RootQueryTo' . $customposttype_graphql_single_name . 'ConnectionWhereArgs', 'show', [
			'type' => 'String', // String/Integer/ID
			'description' => 'filter by show field'
		]);

		register_graphql_field('RootQueryTo' . $customposttype_graphql_single_name . 'ConnectionWhereArgs', 'artistIds', [
			'type' => [ 'list_of' => 'ID' ], // String/Integer/ID
			'description' => 'filter by artist IDs array field'
		]);
	});

	// SHOP CATEGORIES
	add_action('graphql_register_types', function () {
		register_graphql_field( 'ShopCategoryToShopItemConnectionWhereArgs', 'show', [
			'type' => 'String', // String/Integer/ID
			'description' => 'filter by show field'
		]);
	});

	add_filter('graphql_post_object_connection_query_args', function ($query_args, $source, $args, $context, $info) {

		$query_args['meta_query'] = [];

		// filter bids by lot ID
		$lot_id = $args['where']['lotId'];
		if ( isset( $lot_id ) ) {
			array_push( $query_args['meta_query'], [
				'key' => 'lot-bid',
				'value' => $lot_id,
				'compare' => 'LIKE'
				] );
			}

		// filter by auction ID
		$auction_id = $args['where']['auctionId'];
		if ( isset( $lot_id ) ) {
			array_push( $query_args['meta_query'], [
				'key' => 'auction-bid',
				'value' => $auction_id,
				'compare' => 'LIKE'
				] );
			}

		// filter by bid status ( active/idle )
		$bid_status = $args['where']['bidStatus'];
		if ( isset( $bid_status ) ) {
			array_push( $query_args['meta_query'], [
				'key' => 'bid_status',
				'value' => $bid_status,
				'compare' => '='
			] );
		}

		// filter by bid type ( automatic/normal )
		$type = $args['where']['type'];
		if ( isset( $type ) ) {
			array_push( $query_args['meta_query'], [
				'key' => 'type',
				'value' => $type,
				'compare' => '='
			] );
		}

		// filter by user name
		$username = $args['where']['userName'];
		if ( isset( $username ) ) {
			array_push( $query_args['meta_query'], [
				'key' => 'username',
				'value' => $username,
				'compare' => '='
			] );
		}

		// filter by artist show field
		$show = $args['where']['show'];
		if ( isset( $show ) ) {
			array_push( $query_args['meta_query'], [
				'key' => 'show',
				'value' => $show,
				'compare' => '=='
			] );
		}

		// filter by shop item show field
		$showShopItem = $args['where']['show'];
		if ( isset( $showShopItem ) ) {
			array_push( $query_args['meta_query'], [
				'key' => 'show',
				'value' => $showShopItem,
				'compare' => '=='
			] );
		}

		// filter by shop item by artist id field
		$artist_ids = $args['where']['artistIds'];
		if ( isset( $artist_ids ) ) {
			$artistsArr = [ 'relation' => 'OR', ];

			foreach ($artist_ids as $value) {
				array_push(
					$artistsArr,
					[
						'key' => 'shop_item_artist',
						'value' => $value,
						'compare' => 'LIKE'
					]
				);
			};

			array_push(
				$query_args['meta_query'],
				$artistsArr
					// [
					// 'relation' => 'OR',
					// [
					// 	'key' => 'shop_item_artist',
					// 	'value' => '2058',
					// 	'compare' => 'LIKE'
					// ],
					// [
					// 	'key' => 'shop_item_artist',
					// 	'value' => '1869',
					// 	'compare' => 'LIKE'
					// ]
					// ]
			);
		}

		// filter by auction status field: active/idle
		$auction_status = $args['where']['auctionStatus'];
		if ( isset( $auction_status ) ) {
			array_push( $query_args['meta_query'], [
				'key' => 'auction_status',
				'value' => $auction_status,
				'compare' => '=='
			] );
		}

		return $query_args;
	}, 10, 5);

}
add_action( 'init', 'vs_extend_graphql_filters_init' );
