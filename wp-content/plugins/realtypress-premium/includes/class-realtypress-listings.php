<?php

/**
 * RealtyPress Listings Class
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    Realtypress
 * @subpackage Realtypress/includes
 */

if( ! defined( 'ABSPATH' ) ) exit;

if( ! class_exists( 'RealtyPress_Listings' ) ) {

    class RealtyPress_Listings {

        function __construct()
        {
            $this->crud = new RealtyPress_DDF_CRUD( date( 'Y-m-d' ) );
        }

        public function rps_build_search_query( $input )
        {

            $tbl_property = REALTYPRESS_TBL_PROPERTY;

            $search_prepare = array();
            $search_sql     = '';

            $input_bedrooms_max = ( ! empty( $input['input_bedrooms_max'] ) ) ? sanitize_text_field( $input['input_bedrooms_max'] ) : REALTYPRESS_RANGE_BEDS_MAX;
            $input_baths_max    = ( ! empty( $input['input_baths_max'] ) ) ? sanitize_text_field( $input['input_baths_max'] ) : REALTYPRESS_RANGE_BATHS_MAX;
            $input_price_max    = ( ! empty( $input['input_price_max'] ) ) ? sanitize_text_field( $input['input_price_max'] ) : REALTYPRESS_RANGE_PRICE_MAX;

            $input['input_office_id']        = sanitize_text_field( $input['input_office_id'] );
            $input['input_agent_id']         = sanitize_text_field( $input['input_agent_id'] );
            $input['input_property_type']    = ( ! empty( $input['input_property_type'] ) ) ? sanitize_text_field( $input['input_property_type'] ) : '';
            $input['input_business_type']    = ( ! empty( $input['input_business_type'] ) ) ? sanitize_text_field( $input['input_business_type'] ) : '';
            $input['input_transaction_type'] = ( ! empty( $input['input_transaction_type'] ) ) ? sanitize_text_field( $input['input_transaction_type'] ) : '';

            $input['input_street_address'] = ( ! empty( $input['input_street_address'] ) ) ? sanitize_text_field( stripslashes( $input['input_street_address'] ) ) : '';

            $input['input_city']        = sanitize_text_field( stripslashes( $input['input_city'] ) );
            $input['input_province']    = ( ! empty( $input['input_province'] ) ) ? sanitize_text_field( $input['input_province'] ) : '';
            $input['input_postal_code'] = sanitize_text_field( $input['input_postal_code'] );

            $input['input_bedrooms'] = sanitize_text_field( $input['input_bedrooms'] );
            // $input['input_bedrooms_max']  = sanitize_text_field( $input['input_bedrooms_max'] );

            $input['input_baths'] = sanitize_text_field( $input['input_baths'] );
            // $input['input_baths_max']     = sanitize_text_field( $input['input_baths_max'] );

            $input['input_price'] = sanitize_text_field( $input['input_price'] );
            // $input['input_price_max']     = sanitize_text_field( $input['input_price_max'] );

            $input['input_mls']         = sanitize_text_field( $input['input_mls'] );
            $input['input_open_house']  = ( ! empty( $input['input_open_house'] ) ) ? sanitize_text_field( $input['input_open_house'] ) : false;
            $input['input_waterfront']  = ( ! empty( $input['input_waterfront'] ) ) ? sanitize_text_field( $input['input_waterfront'] ) : false;
            $input['input_pool']        = ( ! empty( $input['input_pool'] ) ) ? sanitize_text_field( $input['input_pool'] ) : false;
            $input['input_condominium'] = ( ! empty( $input['input_condominium'] ) ) ? sanitize_text_field( $input['input_condominium'] ) : false;

            $input['input_sold']   = ( ! empty( $input['input_sold'] ) ) ? sanitize_text_field( $input['input_sold'] ) : false;
            $input['input_custom'] = ( ! empty( $input['input_custom'] ) ) ? sanitize_text_field( $input['input_custom'] ) : false;

            $input['input_neighbourhood']  = sanitize_text_field( stripslashes( $input['input_neighbourhood'] ) );
            $input['input_community_name'] = sanitize_text_field( stripslashes( $input['input_community_name'] ) );
            $input['input_building_type']  = ( ! empty( $input['input_building_type'] ) ) ? sanitize_text_field( $input['input_building_type'] ) : '';
            $input['input_description']    = sanitize_text_field( stripslashes( $input['input_description'] ) );

            // Business Type
            if( ! empty( $input['input_business_type'] ) ) {

                $search_sql .= ( ! empty( $search_sql ) ) ? ' OR ' : ' AND ';
                $search_sql .= "( " . $tbl_property . ".BusinessType LIKE %s";
                $search_sql .= " OR  " . $tbl_property . ".BusinessType LIKE %s";
                $search_sql .= " OR  " . $tbl_property . ".BusinessType LIKE %s";
                $search_sql .= " OR  " . $tbl_property . ".BusinessType = %s )";

                $search_prepare[] = $input['input_business_type'] . ',%';
                $search_prepare[] = '%, ' . $input['input_business_type'] . ',%';
                $search_prepare[] = '%, ' . $input['input_business_type'];
                $search_prepare[] = $input['input_business_type'];

            }

            // Office ID
            if( ! empty( $input['input_office_id'] ) ) {
                $exp_office_id = explode( ',', $input['input_office_id'] );
                foreach( $exp_office_id as $office ) {

                    $search_sql .= ( ! empty( $search_sql ) ) ? ' OR ' : ' AND ';
                    $search_sql .= "( " . $tbl_property . ".Offices LIKE %s";
                    $search_sql .= " OR  " . $tbl_property . ".Offices LIKE %s";
                    $search_sql .= " OR  " . $tbl_property . ".Offices LIKE %s";
                    $search_sql .= " OR  " . $tbl_property . ".Offices = %d )";

                    $search_prepare[] = $office . ',%';
                    $search_prepare[] = '%,' . $office . ',%';
                    $search_prepare[] = '%,' . $office;
                    $search_prepare[] = $office;

                }
            }

            // Agent ID
            if( ! empty( $input['input_agent_id'] ) ) {
                $exp_agent_id = explode( ',', $input['input_agent_id'] );


                foreach( $exp_agent_id as $agent ) {

                    $search_sql .= ( ! empty( $search_sql ) ) ? ' OR ' : 'AND ( ';
                    $search_sql .= "( " . $tbl_property . ".Agents LIKE %s";
                    $search_sql .= " OR  " . $tbl_property . ".Agents LIKE %s";
                    $search_sql .= " OR  " . $tbl_property . ".Agents LIKE %s";
                    $search_sql .= " OR  " . $tbl_property . ".Agents = %d ) ";

                    $search_prepare[] = $agent . ',%';
                    $search_prepare[] = '%,' . $agent . ',%';
                    $search_prepare[] = '%,' . $agent;
                    $search_prepare[] = $agent;
                }
                $search_sql .= ' )';

            }

            // Neighbourhood
            if( ! empty( $input['input_neighbourhood'] ) ) {
                $input['input_neighbourhood'] = str_replace( "’", "'", $input['input_neighbourhood'] );
                $exp_neighbourhood            = explode( ',', $input['input_neighbourhood'] );
                $neighbourhood_count          = count( $exp_neighbourhood );

                $merged = get_option( 'rps-appearance-advanced-merge-neighbourhood-community', false );

                $i = 1;

                if( $merged == true ) {
                    $search_sql .= " AND ( ( ";
                }
                else {
                    $search_sql .= " AND ( ";
                }

                foreach( $exp_neighbourhood as $neighbourhood ) {
                    $neighbourhood    = trim( $neighbourhood );
                    $search_sql       .= $tbl_property . ".Neighbourhood LIKE %s";
                    $search_sql       .= ( $neighbourhood_count == $i ) ? '' : ' || ';
                    $search_prepare[] = '%' . $neighbourhood . '%';
                    $i ++;
                }
                $search_sql .= " )";

                // Merged Query
                if( $merged == true ) {
                    $i          = 1;
                    $search_sql .= " || ( ";

                    foreach( $exp_neighbourhood as $neighbourhood ) {
                        $neighbourhood    = trim( $neighbourhood );
                        $search_sql       .= $tbl_property . ".CommunityName LIKE %s";
                        $search_sql       .= ( $neighbourhood_count == $i ) ? '' : ' || ';
                        $search_prepare[] = '%' . $neighbourhood . '%';
                        $i ++;
                    }

                    $search_sql .= " ) )";
                }

            }


            // CommunityName
            if( ! empty( $input['input_community_name'] ) ) {
                $input['input_community_name'] = str_replace( "’", "'", $input['input_community_name'] );
                $exp_community_name            = explode( ',', $input['input_community_name'] );
                $community_name_count          = count( $exp_community_name );

                $merged = get_option( 'rps-appearance-advanced-merge-neighbourhood-community', false );

                $i = 1;

                if( $merged == true ) {
                    $search_sql .= " AND ( ( ";
                }
                else {
                    $search_sql .= " AND ( ";
                }

                foreach( $exp_community_name as $community_name ) {
                    $community_name   = trim( $community_name );
                    $search_sql       .= $tbl_property . ".CommunityName = %s";
                    $search_sql       .= ( $community_name_count == $i ) ? '' : ' || ';
                    $search_prepare[] = $community_name;
                    $i ++;
                }
                $search_sql .= " )";

                // Merged Query
                if( $merged == true ) {
                    $i          = 1;
                    $search_sql .= " || ( ";
                    foreach( $exp_community_name as $community_name ) {
                        $community_name   = trim( $community_name );
                        $search_sql       .= $tbl_property . ".Neighbourhood = %s";
                        $search_sql       .= ( $community_name_count == $i ) ? '' : ' || ';
                        $search_prepare[] = $community_name;
                        $i ++;
                    }
                    $search_sql .= " ) )";
                }

            }

            // Postal Code
            if( ! empty( $input['input_postal_code'] ) ) {
                $exp_postal_code   = explode( ',', $input['input_postal_code'] );
                $postal_code_count = count( $exp_postal_code );

                $i          = 1;
                $search_sql .= " AND ( ";
                foreach( $exp_postal_code as $postal_code ) {
                    $postal_code = trim( $postal_code );
                    $postal_code = str_replace( ' ', '', $postal_code );

                    if( strpos( $postal_code, '*' ) !== false ) {

                        // Wildcard Query
                        $postal_code = str_replace( '*', '%', $postal_code );
                        $search_sql  .= $tbl_property . ".PostalCode LIKE %s";
                        $search_sql  .= ( $postal_code_count == $i ) ? '' : ' || ';
                    }
                    else {

                        // Non-Wildcard Query
                        $search_sql .= $tbl_property . ".PostalCode = %s";
                        $search_sql .= ( $postal_code_count == $i ) ? '' : ' || ';
                    }
                    $search_prepare[] = $postal_code;
                    $i ++;
                }
                $search_sql .= " ) ";
            }

            // Property Type
            if( ! empty( $input['input_property_type'] ) ) {
                $exp_property_type   = explode( ',', $input['input_property_type'] );
                $property_type_count = count( $exp_property_type );

                $i          = 1;
                $search_sql .= " AND ( ";
                foreach( $exp_property_type as $property_type ) {
                    $property_type    = trim( $property_type );
                    $search_sql       .= $tbl_property . ".PropertyType = %s";
                    $search_sql       .= ( $property_type_count == $i ) ? '' : ' || ';
                    $search_prepare[] = $property_type;
                    $i ++;
                }
                $search_sql .= " )";
            }

            // Transaction Type
            if( ! empty( $input['input_transaction_type'] ) ) {
                $exp_transaction_type   = explode( ',', $input['input_transaction_type'] );
                $transaction_type_count = count( $exp_transaction_type );

                $i          = 1;
                $search_sql .= " AND ( ";
                foreach( $exp_transaction_type as $transaction_type ) {
                    $transaction_type = trim( $transaction_type );
                    $search_sql       .= $tbl_property . ".TransactionType = %s";
                    $search_sql       .= ( $transaction_type_count == $i ) ? '' : ' || ';
                    $search_prepare[] = $transaction_type;
                    $i ++;
                }
                $search_sql .= " )";
            }

            // Street Address
            if( ! empty( $input['input_street_address'] ) ) {

                $input['input_street_address'] = str_replace( "’", "'", $input['input_street_address'] );

                $street_addresses = $this->rps_convert_street_type( $input['input_street_address'] );

                $search_sql .= 'AND ( ';
                foreach( $street_addresses as $key => $street_address ) {
                    $search_sql       .= ( $key != 0 ) ? 'OR ' : '';
                    $search_sql       .= $tbl_property . ".AddressLine1 LIKE %s ";
                    $search_prepare[] = '%' . $street_address . '%';
                }
                $search_sql .= ')';

            }

            // City
            if( ! empty( $input['input_city'] ) ) {

                $input['input_city'] = str_replace( "’", "'", $input['input_city'] );
                $prefixes            = array( 'st. ', 'st ', 'saint ', 'saint-' );
                foreach( $prefixes as $prefix ) {

                    if( substr( strtolower( $input['input_city'] ), 0, strlen( $prefix ) ) === $prefix ) {
                        $replaced            = trim( str_replace( $prefix, '', strtolower( $input['input_city'] ) ) );
                        $replaced            = ucwords( $replaced );
                        $input['input_city'] = 'St. ' . $replaced . ',';
                        $input['input_city'] .= 'St ' . $replaced . ',';
                        $input['input_city'] .= 'Saint ' . $replaced;
                        $input['input_city'] .= 'Saint-' . $replaced;
                        break;
                    }
                }

                if( $input['input_city'] == "Sackville" || $input['input_city'] == "Lower Sackville" || $input['input_city'] == "Middle Sackville" || $input['input_city'] == "Upper Sackville" ) {
                    $input['input_city'] = 'Sackville,Lower Sackville,Middle Sackville,Upper Sackville';
                }
                elseif( $input['input_city'] == "Halifax Regional Municipality" ) {
                    $input['input_city'] = 'Halifax';
                }
                elseif( $input['input_city'] == "Clarington" || $input['input_city'] == "Courtice" ) {
                    $input['input_city'] = 'Clarington,Courtice';
                }

                $cities     = explode( ',', $input['input_city'] );
                $city_count = count( $cities );

                $i          = 1;
                $search_sql .= " AND ( ";
                foreach( $cities as $city ) {

                    $city = trim( $city );
                    if( strpos( $city, '*' ) !== false ) {
                        $search_sql       .= $tbl_property . ".City LIKE %s";
                        $search_sql       .= ( $city_count == $i ) ? '' : ' || ';
                        $search_prepare[] = str_replace( '*', '%', $city );
                    }
                    else {
                        $search_sql       .= $tbl_property . ".City = %s";
                        $search_sql       .= ( $city_count == $i ) ? '' : ' || ';
                        $search_prepare[] = $city;
                    }

                    $i ++;
                }
                $search_sql .= " )";
            }

            // Bedrooms
            if( ! empty( $input['input_bedrooms'] ) ) {
                $input_bedrooms = explode( ',', $input['input_bedrooms'] );
                if( ! empty( $input_bedrooms[0] ) ) {
                    $search_sql       .= " && " . $tbl_property . ".BedroomsTotal >= %d";
                    $search_prepare[] = $input_bedrooms[0];
                }
                if( $input_bedrooms[1] != $input_bedrooms_max ) {
                    $search_sql       .= " && " . $tbl_property . ".BedroomsTotal <= %d";
                    $search_prepare[] = $input_bedrooms[1];
                }
            }

            // Baths
            if( ! empty( $input['input_baths'] ) ) {
                $input_baths = explode( ',', $input['input_baths'] );
                if( ! empty( $input_baths[0] ) ) {
                    $search_sql       .= " && " . $tbl_property . ".BathroomTotal >= %d";
                    $search_prepare[] = $input_baths[0];
                }
                if( $input_baths[1] != $input_baths_max ) {
                    $search_sql       .= " && " . $tbl_property . ".BathroomTotal <= %d";
                    $search_prepare[] = $input_baths[1];
                }
            }

            // Price & Lease

            // if( !empty( $input['input_price'] ) ) {
            //   $price_sql = [];
            //   $lease_sql = [];

            //   $input_price = explode( ',', $input['input_price'] );
            //   if( !empty( $input_price[0] ) ) {
            //     $price_sql[] = $tbl_property . ".Price >= %d";
            //     $search_prepare[] = $input_price[0];
            //   }
            //   if( !empty( $input_price[1] ) && $input_price[1] < $input_price_max ) {
            //    $price_sql[] = $tbl_property . ".Price <= %d";
            //    $search_prepare[] = $input_price[1];
            //   }
            //   if( !empty( $input_price[0] ) ) {
            //     $lease_sql[] = $tbl_property . ".Lease >= %d";
            //     $search_prepare[] = $input_price[0];
            //   }
            //   if( !empty( $input_price[1] ) && $input_price[1] < $input_price_max ) {
            //    $lease_sql[] = $tbl_property . ".Lease <= %d";
            //    $search_prepare[] = $input_price[1];
            //   }

            //   if( !empty( $price_sql ) && !empty( $lease_sql ) ) {
            //     $search_sql .= " && ( ( " . implode(' && ', $price_sql ) . " ) && ( " . implode(' && ', $lease_sql ) . ") ) ";
            //   }
            //   elseif( !empty( $lease_sql ) ) {
            //     $search_sql .= " && ( " . implode(' && ', $lease_sql ) . " ) ";
            //   }
            //   elseif( !empty( $price_sql ) ) {
            //     $search_sql .= " && ( " . implode(' && ', $price_sql ) . " ) ";
            //   }

            // }


            if( ! empty( $input['input_price'] ) ) {
                $input_price = explode( ',', $input['input_price'] );

                // Max Price
                // =========
                if( ! empty( $input_price[0] ) ) {

                    $input_price[0] = trim( $input_price[0] );

                    if( ! empty( $input['input_transaction_type'] ) && strtolower( $input['input_transaction_type'] ) == 'for sale' ) {

                        // For Sale
                        $search_sql       .= " && " . $tbl_property . ".Price >= %d";
                        $search_prepare[] = $input_price[0];
                    }
                    elseif( ! empty( $input['input_transaction_type'] ) && ( strtolower( $input['input_transaction_type'] ) == 'for lease' || strtolower( $input['input_transaction_type'] ) == 'for rent' ) ) {

                        // For Lease or Rent
                        $search_sql       .= " && " . $tbl_property . ".Lease >= %d";
                        $search_prepare[] = $input_price[0];
                    }
                    else {

                        // For Sale or Rent and transaction type not set.
                        $search_sql .= " && ( ";

                        $search_sql       .= "( " . $tbl_property . ".Price >= %d && ( " . $tbl_property . ".Lease = '0.00' || " . $tbl_property . ".Lease = 'null' ) )";
                        $search_prepare[] = $input_price[0];
                        // $search_prepare[] = '0.00';
                        // $search_prepare[] = 'null';

                        $search_sql .= " || ";

                        $search_sql       .= "( " . $tbl_property . ".Lease >= %d && ( " . $tbl_property . ".Price = '0.00' || " . $tbl_property . ".Price = 'null' ) )";
                        $search_prepare[] = $input_price[0];
                        // $search_prepare[] = '0.00';
                        // $search_prepare[] = 'null';

                        $search_sql .= " )";
                    }

                }

                // Max Price
                // =========
                if( ! empty( $input_price[1] ) && $input_price[1] < $input_price_max ) {

                    $input_price[1] = trim( $input_price[1] );

                    if( ! empty( $input['input_transaction_type'] ) && strtolower( $input['input_transaction_type'] ) == 'for sale' ) {

                        // For Sale
                        $search_sql       .= " && " . $tbl_property . ".Price <= %d";
                        $search_prepare[] = $input_price[1];
                    }
                    elseif( ! empty( $input['input_transaction_type'] ) && ( strtolower( $input['input_transaction_type'] ) == 'for lease' || strtolower( $input['input_transaction_type'] ) == 'for rent' ) ) {

                        // For Lease or Rent
                        $search_sql       .= " && " . $tbl_property . ".Lease <= %d";
                        $search_prepare[] = $input_price[1];
                    }
                    else {

                        $search_sql .= " && ( ";

                        $search_sql       .= "( " . $tbl_property . ".Price <= %d && ( " . $tbl_property . ".Lease = '0.00' || " . $tbl_property . ".Lease = 'null' ) )";
                        $search_prepare[] = $input_price[1];

                        $search_sql .= " || ";

                        $search_sql       .= "( " . $tbl_property . ".Lease <= %d && ( " . $tbl_property . ".Price = '0.00' || " . $tbl_property . ".Price = 'null' ) )";
                        $search_prepare[] = $input_price[1];

                        $search_sql .= " )";
                    }

                }

            }

            // Condominium
            if( ! empty( $input['input_condominium'] ) ) {
                $search_sql       .= " && " . $tbl_property . ".OwnershipType LIKE '%s'";
                $search_prepare[] = '%Condo%';
            }

            // Pool
            if( ! empty( $input['input_pool'] ) ) {
                $search_sql .= " && Length(" . $tbl_property . ".PoolType) > 2";
            }

            // Waterfront
            if( ! empty( $input['input_waterfront'] ) ) {
                $search_sql .= " && Length(" . $tbl_property . ".WaterFrontType) > 4";
            }

            // Open House
            if( ! empty( $input['input_open_house'] ) ) {
                $search_sql .= " && Length(" . $tbl_property . ".OpenHouse) > 10";
            }

            if( ! empty( $input['input_sold'] ) ) {
                $search_sql .= " && " . $tbl_property . ".Sold = '1' ";
            }
            else {
                if( get_option( 'rps-appearance-advanced-include-sold', true ) != true ) {
                    $search_sql .= " && " . $tbl_property . ".Sold != '1' ";
                }
            }

            if( ! empty( $input['input_custom'] ) ) {
                $search_sql .= " && " . $tbl_property . ".CustomListing = '1' ";
            }
            else {
                if( get_option( 'rps-appearance-advanced-include-custom-listings', true ) != true ) {
                    $search_sql .= " && " . $tbl_property . ".CustomListing != '1' ";
                }
            }

            // Listing ID (MLS Number)
            if( ! empty( $input['input_mls'] ) ) {
                $exp_mls   = explode( ',', $input['input_mls'] );
                $mls_count = count( $exp_mls );

                $i          = 1;
                $search_sql .= " AND ( ";
                foreach( $exp_mls as $mls ) {
                    $mls              = trim( $mls );
                    $search_sql       .= $tbl_property . ".DdfListingID = %s";
                    $search_sql       .= ( $mls_count == $i ) ? '' : ' || ';
                    $search_prepare[] = $mls;
                    $i ++;
                }
                $search_sql .= " )";

            }

            // Building Type
            if( ! empty( $input['input_building_type'] ) ) {
                $exp_building_type   = explode( ',', $input['input_building_type'] );
                $building_type_count = count( $exp_building_type );

                $i          = 1;
                $search_sql .= " AND ( ";
                foreach( $exp_building_type as $building_type ) {
                    $building_type    = trim( $building_type );
                    $search_sql       .= $tbl_property . ".Type = %s";
                    $search_sql       .= ( $building_type_count == $i ) ? '' : ' || ';
                    $search_prepare[] = $building_type;
                    $i ++;
                }
                $search_sql .= " )";
            }


            // Province
            if( ! empty( $input['input_province'] ) ) {
                
                $exp_province   = explode( ',', $input['input_province'] );
                $province_count = count( $exp_province );

                $i          = 1;
                $search_sql .= " AND ( ";
                foreach( $exp_province as $province ) {
                    $province         = trim( $province );
                    $search_sql       .= $tbl_property . ".Province = %s";
                    $search_sql       .= ( $province_count == $i ) ? '' : ' || ';
                    $search_prepare[] = $province;
                    $i ++;
                }
                $search_sql .= " )";
            }


            // Description
            if( ! empty( $input['input_description'] ) ) {
                $exp_description   = explode( '|', $input['input_description'] );
                $description_count = count( $exp_description );

                $i          = 1;
                $search_sql .= " AND ( ";
                foreach( $exp_description as $description ) {

                    // $description = trim( $description );

                    $search_sql       .= $tbl_property . ".PublicRemarks LIKE %s";
                    $search_sql       .= ( $description_count == $i ) ? '' : ' || ';
                    $search_prepare[] = '%' . $description . '%';

                    $i ++;
                }
                $search_sql .= " )";
            }

            $return = array(
                'search_sql'     => $search_sql,
                'search_prepare' => $search_prepare
            );

            return $return;

        }

        public function rps_convert_street_type( $street_address )
        {

            $addresses = array( $street_address );

            // Get supposed Street Type from Street Address
            $address = explode( ' ', $street_address );

            $street_type = array_pop( $address );
            $street_type = preg_replace( "/[^A-Za-z0-9 ]/", '', $street_type );

            $address = implode( ' ', $address );

            $longname = array( 'Autoroute' => 'AUT', 'Avenue' => array( 'AV', 'AVE' ), 'Boulevard' => array( 'BLVD', 'BOUL' ), 'By-pass' => 'BYPASS', 'Freeway' => 'FWY', 'Gardens' => 'GDNS', 'Place' => 'PL', 'Plateau' => 'PLAT', 'Point' => 'PT', 'Private' => 'PVT', 'Promenade' => 'PROM', 'Carré' => 'CAR', 'Carrefour' => 'CARREF', 'Centre' => array( 'CTR', 'C' ), 'Chemin' => 'CH', 'Circle' => 'CIR', 'Circuit' => 'CIRCT', 'Concession' => 'CONC', 'Corners' => 'CRNRS', 'Court' => 'CRT', 'Crescent' => 'CRES', 'Croissant' => 'CROIS', 'Crossing' => 'CROSS', 'Cul-de-sac' => 'CDS', 'Diversion' => 'DIVERS', 'Drive' => 'DR', 'Esplanade' => 'ESPL', 'Estates' => 'ESTATE', 'Expressway' => 'EXPY', 'Extension' => 'EXTEN', 'Grounds' => 'GRNDS', 'Harbour' => 'HARBR', 'Heights' => 'HTS', 'Highlands' => 'HGHLDS', 'Highway' => 'HWY', 'Impasse' => 'IMP', 'Limits' => 'LMTS', 'Mountain' => 'MTN', 'Orchard' => 'ORCH', 'Park' => 'PK', 'Parkway' => 'PKY', 'Passage' => 'PASS', 'Range' => 'RG', 'Road' => 'RD', 'Rond-point' => 'RDPT', 'Route' => 'RTE', 'Ruelle' => 'RLE', 'Sentier' => 'SENT', 'Square' => 'SQ', 'Street' => 'ST', 'Subdivision' => 'SUBDIV', 'Terrace' => 'TERR', 'Terrasse' => 'TSSE', 'Lookout' => 'LKOUT', 'Thicket' => 'THICK', 'Townline' => 'TLINE', 'Turnabout' => 'TRNABT', 'Village' => 'VILLGE', 'Pathway' => 'PTWAY'
            );

            foreach( $longname as $long => $short ) {

                if( is_array( $short ) ) {
                    if( in_array( strtoupper( $street_type ), $short ) || strtolower( $street_type ) == strtolower( $long ) ) {

                        foreach( $short as $short_value ) {
                            $addresses[] = $address . ' ' . $short_value;
                        }
                        $addresses[] = $address . ' ' . $long;
                        $addresses   = rps_array_iunique( $addresses );
                    }
                }
                else {
                    if( strtolower( $street_type ) == strtolower( $long ) ) {
                        // Matches long name create short names
                        $addresses[] = $address . ' ' . $short;
                    }
                    elseif( strtolower( $street_type ) == strtolower( $short ) ) {
                        // Matches short name create long names
                        $addresses[] = $address . ' ' . $long;
                    }
                }

            }

            return $addresses;

        }

        public function rps_search_posts( $get )
        {

            global $wpdb;

            $tbl_property = REALTYPRESS_TBL_PROPERTY;

            $return = new stdClass();

            $get['posts_per_page'] = ( ! empty( $get['posts_per_page'] ) ) ? sanitize_text_field( $get['posts_per_page'] ) : get_option( 'rps-result-per-page', 12 );
            $get['paged']          = ( ! empty( $get['paged'] ) ) ? sanitize_text_field( $get['paged'] ) : 0;
            $get['sort']           = ( ! empty( $get['sort'] ) ) ? sanitize_text_field( $get['sort'] ) : get_option( 'rps-result-default-sort-by', $tbl_property . '.ListingContractDate DESC, ' . $tbl_property . '.LastUpdated DESC, ' . $tbl_property . '.property_id DESC' );
            $get['view']           = ( ! empty( $get['view'] ) ) ? sanitize_text_field( $get['view'] ) : get_option( 'rps-result-default-view-', 'grid' );

            $map_sql   = '';
            $limit_sql = '';

            if( $get['view'] == 'map' ) {

                $select_sql = $wpdb->posts . '.ID, ';
                $select_sql .= $tbl_property . '.Latitude, ';
                $select_sql .= $tbl_property . '.Longitude, ';
                $select_sql .= $tbl_property . '.ListingID ';

                $map_sql .= " AND (" . $tbl_property . ".Latitude IS NULL || " . $tbl_property . ".Latitude != '' || " . $tbl_property . ".Longitude IS NULL   || " . $tbl_property . ".Longitude != '' ) ";

            }
            else {

                $posts_per_page = rps_get_posts_per_page( $get['posts_per_page'] );
                $paged          = $get['paged'];
                $limit          = ( ( $posts_per_page * $paged ) - $posts_per_page );

                $select_sql = $wpdb->posts . '.ID, ';
                $select_sql .= $tbl_property . '.PostID';

                $limit_sql = 'LIMIT ' . $limit . ', ' . $posts_per_page;
            }

            $build                           = array();
            $build['input_office_id']        = ( ! empty( $get['input_office_id'] ) ) ? $get['input_office_id'] : '';
            $build['input_agent_id']         = ( ! empty( $get['input_agent_id'] ) ) ? $get['input_agent_id'] : '';
            $build['input_property_type']    = ( ! empty( $get['input_property_type'] ) ) ? $get['input_property_type'] : '';
            $build['input_business_type']    = ( ! empty( $get['input_business_type'] ) ) ? $get['input_business_type'] : '';
            $build['input_transaction_type'] = ( ! empty( $get['input_transaction_type'] ) ) ? $get['input_transaction_type'] : '';
            $build['input_street_address']   = ( ! empty( $get['input_street_address'] ) ) ? $get['input_street_address'] : '';
            $build['input_city']             = ( ! empty( $get['input_city'] ) ) ? $get['input_city'] : '';
            $build['input_province']         = ( ! empty( $get['input_province'] ) ) ? $get['input_province'] : '';
            $build['input_postal_code']      = ( ! empty( $get['input_postal_code'] ) ) ? $get['input_postal_code'] : '';
            $build['input_bedrooms']         = ( ! empty( $get['input_bedrooms'] ) ) ? $get['input_bedrooms'] : '';
            $build['input_bedrooms_max']     = ( ! empty( $get['input_bedrooms_max'] ) ) ? $get['input_bedrooms_max'] : '';
            $build['input_baths']            = ( ! empty( $get['input_baths'] ) ) ? $get['input_baths'] : '';
            $build['input_baths_max']        = ( ! empty( $get['input_baths_max'] ) ) ? $get['input_baths_max'] : '';
            $build['input_price']            = ( ! empty( $get['input_price'] ) ) ? $get['input_price'] : '';
            $build['input_price_max']        = ( ! empty( $get['input_price_max'] ) ) ? $get['input_price_max'] : '';
            $build['input_mls']              = ( ! empty( $get['input_mls'] ) ) ? $get['input_mls'] : '';

            $build['input_open_house']  = ( isset( $get['input_open_house'] ) ) ? $get['input_open_house'] : '';
            $build['input_waterfront']  = ( isset( $get['input_waterfront'] ) ) ? $get['input_waterfront'] : '';
            $build['input_pool']        = ( isset( $get['input_pool'] ) ) ? $get['input_pool'] : '';
            $build['input_condominium'] = ( isset( $get['input_condominium'] ) ) ? $get['input_condominium'] : '';

            $build['input_sold']   = ( isset( $get['input_sold'] ) ) ? $get['input_sold'] : '';
            $build['input_custom'] = ( isset( $get['input_custom'] ) ) ? $get['input_custom'] : '';

            $build['input_neighbourhood']  = ( ! empty( $get['input_neighbourhood'] ) ) ? $get['input_neighbourhood'] : '';
            $build['input_community_name'] = ( ! empty( $get['input_community_name'] ) ) ? $get['input_community_name'] : '';
            $build['input_postal_code']    = ( ! empty( $get['input_postal_code'] ) ) ? $get['input_postal_code'] : '';
            $build['input_description']    = ( ! empty( $get['input_description'] ) ) ? $get['input_description'] : '';
            $build['input_building_type']  = ( ! empty( $get['input_building_type'] ) ) ? $get['input_building_type'] : '';

            $query = $this->rps_build_search_query( $build );

            /**
             *  Count Query
             *  -----------
             */

            $count_query = "
                 SELECT count(*) as found_posts
                   FROM $wpdb->posts FORCE KEY ( PRIMARY ) 
             INNER JOIN $tbl_property
                     ON $wpdb->posts.post_excerpt = $tbl_property.ListingID
                  WHERE $wpdb->posts.post_status = 'publish'
                    AND $wpdb->posts.post_type = 'rps_listing'
                        " . $map_sql . " 
                        " . $query['search_sql'];

            // Prepare sql statement if required
            if( ! empty( $query['search_prepare'] ) ) {
                $count_query = $wpdb->prepare( $count_query, $query['search_prepare'] );
            }

            // Run count query
            $count = $wpdb->get_results( $count_query, OBJECT );

            /**
             *  Paginated Query
             *  ---------------
             */

            // If posts were found
            if( ! empty( $count[0]->found_posts ) && $count[0]->found_posts > 0 ) {

                // Order by
                $order = ( ! empty( $get['sort'] ) ) ? $get['sort'] : $tbl_property . '.ListingContractDate DESC, ' . $tbl_property . '.LastUpdated DESC, ' . $tbl_property . '.property_id DESC';

                $result_query = "
                   SELECT $select_sql
                     FROM $wpdb->posts FORCE KEY ( PRIMARY ) 
               INNER JOIN $tbl_property
                       ON $wpdb->posts.post_excerpt = $tbl_property.ListingID
                    WHERE $wpdb->posts.post_status = 'publish'
                      AND $wpdb->posts.post_type = 'rps_listing'
                          " . $map_sql . " 
                          " . $query['search_sql'] . "
                 ORDER BY $order 
                          $limit_sql ";

                // Prepare sql statement if required
                if( ! empty( $query['search_prepare'] ) ) {
                    if( $get['view'] == 'map' ) {
                        
                        // Prepare map sql statement
                        $result_query = $wpdb->prepare( $result_query, $query['search_prepare'] );
                    }
                    else {
                        
                        // Prepare grid/list sql statement
                        $result_query = $wpdb->prepare( $result_query, $query['search_prepare'] );
                    }
                }

                // Run post result query
                $results = $wpdb->get_results( $result_query, OBJECT );

                // Prepare to return if result is not empty
                if( ! empty( $results ) ) {
                    if( $get['view'] != 'map' ) {
                        $return->post_count    = $posts_per_page;
                        $return->max_num_pages = round_up( ( $count[0]->found_posts / $posts_per_page ), 1 );
                    }

                    $return->found_posts = $count[0]->found_posts;
                    $return->posts       = $results;

                    return $return;
                }

            }

            $return->post_count    = 0;
            $return->found_posts   = 0;
            $return->max_num_pages = 0;
            $return->posts         = new stdClass();

            return $return;

        }

        public function get_search_marker_points( $query )
        {

            $marker_points = array();
            if( $query->found_posts != 0 ) {
                $i = 0;
                foreach( $query->posts as $post ) {

                    if( ! empty( $post->Latitude ) &&
                        ! empty( $post->Longitude ) &&
                        ! empty( $post->ListingID ) ) {

                        $marker_points[$i]['lat'] = $post->Latitude;
                        $marker_points[$i]['lon'] = $post->Longitude;
                        $marker_points[$i]['lid'] = $post->ListingID;

                    }


                    $i ++;
                }
            }

            return $marker_points;
        }

        public function fix_default_marker_points( $verbose = false )
        {

            global $wpdb;

            // Get listing id from post excerpt
            $query         = " SELECT property_id,
                        ListingID,
                        Longitude,
                        Latitude,
                        StreetAddress,
                        AddressLine1,
                        City,
                        Province,
                        Country,
                        PostalCode
                   FROM " . REALTYPRESS_TBL_PROPERTY . "
                  WHERE ( Latitude = '56.130366' && Longitude = '-106.346771' ) || 
                        ( Latitude = '51.253775' && Longitude = '-85.3232139' ) ||
                        ( Latitude = '53.7608608' && Longitude = '-98.81387629999999' ) ||
                        ( Latitude = '52.9399159' && Longitude = '-106.4508639' ) ||
                        ( Latitude = '53.9332706' && Longitude = '-116.5765035' ) ||
                        ( Latitude = '53.7266683' && Longitude = '-127.6476206' ) && 
                        CustomListing != '1' ";
            $results       = $wpdb->get_results( $query, ARRAY_A );
            $results_count = count( $results );

            $output = '';
            $count  = 0;

            foreach( $results as $key => $value ) {

                $address                  = array();
                $address['StreetAddress'] = $value['StreetAddress'];
                $address['City']          = $value['City'];
                $address['Province']      = $value['Province'];
                $address['PostalCode']    = ( ! empty( $value['PostalCode'] ) ) ? rps_format_postal_code( $value['PostalCode'] ) : '';
                $geo_data                 = $this->crud->get_geo_coding_data( $address );

                // Address without PostalCode
                if( $this->crud->rps_is_geo_coding_response_default( $geo_data ) == true || $geo_data['status'] == 'ZERO_RESULTS' ) {
                    $output                  .= "GeoCall - Default response, attempting address variation 1!<br>";
                    $variation               = $address;
                    $variation['PostalCode'] = '';
                    $geo_data                = $this->crud->get_geo_coding_data( $variation );
                }

                // Address without StreetAddress
                if( $this->crud->rps_is_geo_coding_response_default( $geo_data ) == true || $geo_data['status'] == 'ZERO_RESULTS' ) {
                    $output                     .= "GeoCall - Default response, attempting address variation 2!<br>";
                    $variation                  = $address;
                    $variation['StreetAddress'] = '';
                    $geo_data                   = $this->crud->get_geo_coding_data( $variation );
                }

                // Address without StreetAddress and PostalCode
                if( $this->crud->rps_is_geo_coding_response_default( $geo_data ) == true || $geo_data['status'] == 'ZERO_RESULTS' ) {
                    $output                     .= "GeoCall - Default response, attempting address variation 3!<br>";
                    $variation                  = $address;
                    $variation['PostalCode']    = '';
                    $variation['StreetAddress'] = '';
                    $geo_data                   = $this->crud->get_geo_coding_data( $variation );
                }

                if( ! empty( $geo_data['Latitude'] ) && ! empty( $geo_data['Longitude'] ) ) {
                    $update = $wpdb->query( " UPDATE " . REALTYPRESS_TBL_PROPERTY . " SET Latitude = " . $geo_data['Latitude'] . ",Longitude = " . $geo_data['Longitude'] . "  WHERE property_id = '" . $value['property_id'] . "'" );
    
                    if( $update == true ) {
                        $echo_address = array_filter( $address );
                        $output       .= 'Fixed! ' . $value['ListingID'] . ' | ' . implode( ', ', $echo_address ) . '<br>';
                        $count        = $count + 1;
                    }
                    else {
                        $output .= 'Variation attempts failed! Unable to GeoCode address<br>';
                    }
                    
                }

                if( $geo_data == 'OVER_QUERY_LIMIT' ) {
                    $output .= "***********************************<br>";
                    $output .= "  Over GeoCoding Query Limit !<br>";
                    $output .= "***********************************<br>";
                    break 1;
                }
    
                if( $geo_data === false ) {
                    $output .= "***********************************<br>";
                    $output .= "  No API key has been entered !<br>";
                    $output .= "***********************************<br>";
                    break 1;
                }

            }

            $output .= '<h4>Fixed ' . $count . ' of ' . $results_count . ' listings.</h4>';

            if( $verbose == true ) {
                echo $output;
            }

        }

        public function fix_orphaned_listing_posts( $verbose = false )
        {

            global $wpdb;

            // Select all orphaned posts from wp_posts table.
            $orphaned = $wpdb->get_results( "
                SELECT $wpdb->posts.* 
                FROM $wpdb->posts 
                LEFT JOIN " . REALTYPRESS_TBL_PROPERTY . "
                ON $wpdb->posts.post_excerpt = " . REALTYPRESS_TBL_PROPERTY . ".ListingID 
                WHERE " . REALTYPRESS_TBL_PROPERTY . ".ListingID IS NULL 
                AND CustomListing != '1'  
                AND $wpdb->posts.post_type = 'rps_listing' ",
                                            ARRAY_A );

            $output = '<h3>Orphaned</h3>';
            if( ! empty( $orphaned ) ) {

                $output .= '<h4>' . count( $orphaned ) . ' Orphaned Posts Found</h4>';

                $orphan_ids = array();
                foreach( $orphaned as $orphan ) {
                    $orphan_ids[] = $orphan['ID'];

                    if( ! empty( $orphan['post_excerpt'] ) ) {
                        $orphan_deletions              = array();
                        $orphan_deletions['ListingID'] = $orphan['post_excerpt'];
                        $orphan_deletions['PostID']    = $orphan['ID'];
                        $this->crud->delete_local_listing( $orphan_deletions );
                    }
                    else {
                        wp_delete_post( $orphan['ID'], true );
                    }
                }

                $output .= '<p><strong class="rps-text-red">Deleting Orphaned Posts (' . count( $orphan_ids ) . ') : </strong>';
                $output .= implode( ',', $orphan_ids );
                $output .= '</p>';
                $output .= '<p>Orphaned Posts Deleted!</p>';
                $output .= '<hr>';
            }
            else {
                $output .= '<h4>No Orphans Found!</h4>';
                $output .= '<hr>';
            }

            if( $verbose == true ) {
                echo $output;
            }
        }

        public function fix_duplicate_listings( $verbose = false )
        {

            global $wpdb;

            $duplicates = $wpdb->get_results( "
                SELECT post_excerpt, ID, COUNT(*) cnt 
                  FROM $wpdb->posts
                 WHERE post_type = 'rps_listing'
              GROUP BY post_excerpt 
                HAVING cnt > 1",
                                              ARRAY_A );

            $output = '<h3>Duplicates</h3>';
            if( ! empty( $duplicates ) ) {
                $output .= '<h4>' . count( $duplicates ) . ' Duplicates Found</h4>';

                $dupe_ids = array();
                foreach( $duplicates as $dupe ) {
                    $dupe_ids[] = $dupe['ID'];
                    wp_delete_post( $dupe['ID'], true );
                }

                $output .= '<p><strong class="rps-text-red">Deleting Duplicate Posts (' . count( $dupe_ids ) . ') : </strong>';
                $output .= implode( ',', $dupe_ids );
                $output .= '</p>';
                $output .= '<p>Duplicate Posts Deleted!</p>';
                $output .= '<hr>';
            }
            else {
                $output .= '<h4>No Duplicates Found!</h4>';
                $output .= '<hr>';
            }

            if( $verbose == true ) {
                echo $output;
            }

        }


        public function fix_broken_post_relations( $verbose = false )
        {

            global $wpdb;

            $relations = $wpdb->get_results( "
                SELECT property.PostID, property.ListingID, property.property_id, property.CustomListing, posts.ID
                FROM " . REALTYPRESS_TBL_PROPERTY . " AS property
                JOIN $wpdb->posts AS posts 
                ON property.ListingID = posts.post_excerpt
                WHERE property.PostID != posts.ID  
                AND property.CustomListing != '1' ",
                                             ARRAY_A );

            $output = '<h3>Fix Table Relations</h3>';
            if( ! empty( $relations ) ) {

                $output .= '<h4>' . count( $relations ) . ' Mismatched Post Relations</h4>';
                foreach( $relations as $relation ) {
                    $update_query = " UPDATE " . REALTYPRESS_TBL_PROPERTY . " SET PostID = '" . $relation['ID'] . "' WHERE property_id = '" . $relation['property_id'] . "' ";
                    $wpdb->query( $update_query );
                }
                $output .= '<p><strong class="rps-text-red">Fixing Broken Post Relations (' . count( $relations ) . ') : </strong>';
                $output .= '</p>';
                $output .= '<p>RealtyPress Broken Post Relations Fixed!</p>';
                $output .= '<hr>';

            }
            else {

                $output .= '<h4>No RealtyPress Broken Post Relations Found!</h4>';
                $output .= '<hr>';

            }

            if( $verbose == true ) {
                echo $output;
            }

        }

        //  public function fix_missing_image_files( $verbose = false ) {
        //
        //        global $wpdb;
        //
        //    $all_listings = $wpdb->get_results(" SELECT post_excerpt, ID FROM $wpdb->posts WHERE post_type = 'rps_listing' ", ARRAY_A );
        //
        //    $output = '<h3>Broken Images</h3>';
        //    if( !empty( $all_listings ) ) {
        //
        //      $output .= '<h4>Searching ' . count( $all_listings ) . ' Listings for Broken Images</h4>';
        //
        //
        //        $missing_ids = array();
        //        foreach( $all_listings as $listing ) {
        //
        //          $listing_photo = $this->crud->get_local_listing_photos( $listing['post_excerpt'] );
        //
        //          if( !empty( $listing_photo[0] ) ) {
        //            $missing_deletions = array();
        //            if( rps_use_amazon_s3_storage() == true ||
        //                rps_use_lw_object_storage() == true ) {
        //
        //              $ch = curl_init();
        //              curl_setopt($ch, CURLOPT_URL, REALTYPRESS_LISTING_PHOTO_URL . '/' . $photos['Photo']['id'] . '/' . $photos['Photo']['filename'] );
        //              // don't download content
        //              curl_setopt($ch, CURLOPT_NOBODY, 1);
        //              curl_setopt($ch, CURLOPT_FAILONERROR, 1);
        //              curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //
        //              if( curl_exec($ch) !== FALSE ) {
        //                // Image exits do nothing
        //              }
        //              else {
        //                // Image does not exist add to missing_deletions array.
        //                $missing_ids[] = $listing['ID'];
        //
        //                $missing_deletions['ListingID'] = $listing['post_excerpt'];
        //                $missing_deletions['PostID']    = $listing['ID'];
        //                $this->crud->delete_local_listing( $missing_deletions );
        //              }
        //
        //            }
        //            else {
        //              if( !file_exists( REALTYPRESS_LISTING_PHOTO_PATH . '/' . $photos['Photo']['id'] . '/' . $photos['Photo']['filename'] ) ) {
        //                // Image does not exist add to missing_deletions array.
        //                $missing_ids[] = $listing['ID'];
        //
        //                $missing_deletions['ListingID'] = $listing['post_excerpt'];
        //                $missing_deletions['PostID']    = $listing['ID'];
        //                $this->crud->delete_local_listing( $missing_deletions );
        //              }
        //            }
        //
        //          }
        //        }
        //
        //        if( count( $missing_ids ) > 0 ) {
        //          $output .= '<p><strong>Deleting Broken Image Listings (' . count( $missing_ids ) . ') : </strong>';
        //            $output .= implode( ',', $missing_ids );
        //          $output .= '</p>';
        //          $output .= '<p>Broken Image Listings Deleted! .</p>';
        //        }
        //        else {
        //          $output .= '<h4>No Broken Images Found!</h4>';
        //          $output .= '<hr>';
        //        }
        //
        //    } else {
        //      $output .= '<h4>No Listings found to search for missing images.</h4>';
        //      $output .= '<hr>';
        //    }
        //
        //    if( $verbose == true ) {
        //      echo $output;
        //    }
        //
        //  }

        public function get_listing_board( $board_lookup_id )
        {

            global $wpdb;

            $tbl_boards = REALTYPRESS_TBL_BOARDS;

            $boards_sql = " SELECT * FROM $tbl_boards WHERE $tbl_boards.OrganizationID = $board_lookup_id LIMIT 1 ";
            $result     = $wpdb->get_results( $boards_sql, ARRAY_A );

            if( ! empty( $result ) ) {
                return $result[0];
            }

            return false;
        }

        public function get_distinct_values( $column )
        {
            global $wpdb;

            $query   = " SELECT $column FROM " . REALTYPRESS_TBL_PROPERTY . " GROUP BY $column ORDER BY $column ASC";
            $results = $wpdb->get_results( $query, ARRAY_A );

            return $results;
        }

        public function clean_cs_distinct_values( $array, $key )
        {

            $results = array();
            foreach( $array as $csv ) {
                $values = explode( ',', $csv[$key] );
                foreach( $values as $value ) {
                    $results[][$key] = trim( $value );
                }
            }
            $results = array_map( "unserialize", array_unique( array_map( "serialize", $results ) ) );
            sort( $results );

            return $results;
        }

        private function build_args( $array )
        {

            $args = array();
            foreach( $array as $bla => $values ) {

                foreach( $values as $key => $value ) {
                    if( ! empty( $value ) )
                        $args[] = $value;
                }
            }

            return $args;
        }

        public function build_dropdown( $name, $id, $title = '', $meta_values, $selected = '', $add_class = '' )
        {

            $meta_values = $this->build_args( $meta_values );

            $output = '<select name="' . $name . '" id="' . $id . '" class="form-control ' . $add_class . '">';
            if( ! empty( $title ) ) {
                $output .= '<option value="">' . $title . '</option>';
            }
            foreach( $meta_values as $value ) {
                if( strtolower( $value ) == strtolower( $selected ) ) {
                    $output .= '<option value="' . $value . '" selected>' . $value . '</option>';
                }
                else {
                    $output .= '<option value="' . $value . '">' . $value . '</option>';
                }
            }
            $output .= '</select>';

            return $output;

        }

        /**
         * ------------------------------------------------------------------------------------
         *    IMAGES
         * ------------------------------------------------------------------------------------
         * @param      $file
         * @param      $in_dir
         * @param      $out_dir
         * @param      $out_width
         * @param      $out_height
         * @param int  $quality
         * @param bool $verbose
         * @return array
         */

        function rps_resize_image( $file, $in_dir, $out_dir, $out_width, $out_height, $quality = 100, $verbose = false )
        {

            // Get image info
            list( $width, $height ) = getimagesize( $in_dir . '/' . $file );

            $result = array();
            if( $out_width == $width && $out_height == $height ) {

                // Check if image dimensions already match requested resize dimensions, if they do skip resize.
                $result['output'] = '';
                $result['result'] = false;

            }
            else {

                if( $width > $height ) {
                    $width_t  = $out_width;
                    $height_t = round( ( $height / $width ) * $out_width );
                    $off_x    = 0;
                    $off_y    = ceil( ( $out_height - $height_t ) / 2 );
                }
                elseif( $height > $width ) {
                    $height_t = $out_height;
                    $width_t  = round( ( $width / $height ) * $out_height );
                    $off_x    = ceil( ( $out_width - $width_t ) / 2 );
                    $off_y    = 0;
                }
                else {
                    $width_t  = $out_width;
                    $height_t = $out_height;
                    $off_x    = 0;
                    $off_y    = 0;
                }

                if( 0 == filesize( $in_dir . '/' . $file ) ) {

                    // file is empty
                    $result['output'] = ( ( $verbose == true ) ? 'Empty File: ' . $file . '<br>' : '' );
                    $result['result'] = true;

                }
                else {

                    $thumb   = imagecreatefromjpeg( $in_dir . '/' . $file );
                    $thumb_p = imagecreatetruecolor( $out_width, $out_height );
                    $bg      = imagecolorallocate( $thumb_p, 255, 255, 255 );

                    imagefill( $thumb_p, 0, 0, $bg );
                    imagecopyresampled( $thumb_p, $thumb, $off_x, $off_y, 0, 0, $width_t, $height_t, $width, $height );
                    imagejpeg( $thumb_p, $in_dir . '/' . $file, $quality );

                    $result['output'] = ( ( $verbose == true ) ? 'Resized: ' . $file . '<br>' : '' );
                    $result['result'] = true;

                }

            }

            return $result;

        }

        function rps_resize_image_max( $incoming, $outgoing, $max_width, $max_height, $verbose )
        {

            $file  = basename( $incoming );
            $image = imagecreatefromjpeg( $incoming );

            $w = imagesx( $image );
            $h = imagesy( $image );

            if( ( ! $w ) || ( ! $h ) ) {

                $result['output'] = ( ( $verbose == true ) ? '-- not a valid image ---> ' . $file . '<br>' : '' );
                $result['result'] = false;

            }
            else {

                if( ( $w <= $max_width ) && ( $h <= $max_height ) ) {
                    // $result['output'] = ( ( $verbose == true ) ? '-- ratio ok ---> ' .$file . '<br>' : '' );
                    $result['output'] = '';
                    $result['result'] = false;
                }
                else {

                    // Max width
                    $ratio = $max_width / $w;
                    $new_w = $max_width;
                    $new_h = $h * $ratio;

                    // Max height
                    if( $new_h > $max_height ) {
                        $ratio = $max_height / $h;
                        $new_h = $max_height;
                        $new_w = $w * $ratio;
                    }

                    $new_image = imagecreatetruecolor( $new_w, $new_h );
                    imagecopyresampled( $new_image, $image, 0, 0, 0, 0, $new_w, $new_h, $w, $h );
                    imagejpeg( $new_image, $outgoing, 50 );

                    $result['output'] = ( ( $verbose == true ) ? 'Resized: ' . $file . '<br>' : '' );
                    $result['result'] = true;
                }
            }

            return $result;
        }

        function rps_custom_resize_image( $inpath, $outpath, $out_width, $out_height, $quality = 100, $verbose = false )
        {

            // Get image info
            list( $width, $height ) = getimagesize( $inpath );

            $result = array();

            // Check if image dimensions already match requested resize dimensions, if they do skip resize.
            if( $out_width == $width && $out_height == $height ) {

                // $result['output'] = ( ( $verbose == true ) ? '-- ratio ok ---> ' .$file . '<br>' : '' );
                $result['output'] = '';
                $result['result'] = false;

            }
            else {

                if( $width > $height ) {
                    $width_t  = $out_width;
                    $height_t = round( ( $height / $width ) * $out_width );
                    $off_x    = 0;
                    $off_y    = ceil( ( $out_height - $height_t ) / 2 );
                }
                elseif( $height > $width ) {
                    $height_t = $out_height;
                    $width_t  = round( ( $width / $height ) * $out_height );
                    $off_x    = ceil( ( $out_width - $width_t ) / 2 );
                    $off_y    = 0;
                }
                else {
                    $width_t  = $out_width;
                    $height_t = $out_height;
                    $off_x    = 0;
                    $off_y    = 0;
                }

                $thumb   = imagecreatefromjpeg( $inpath );
                $thumb_p = imagecreatetruecolor( $out_width, $out_height );
                $bg      = imagecolorallocate( $thumb_p, 255, 255, 255 );

                imagefill( $thumb_p, 0, 0, $bg );
                imagecopyresampled( $thumb_p, $thumb, $off_x, $off_y, 0, 0, $width_t, $height_t, $width, $height );
                imagejpeg( $thumb_p, $outpath, $quality );


                $result['output'] = ( ( $verbose == true ) ? 'Resized: ' . $file . '<br>' : '' );
                $result['result'] = true;

            }

            return $result;

        }

        function rps_custom_listing_resize_image( $inpath, $outpath, $out_width, $out_height, $quality = 100, $verbose = false )
        {

            $result = array();

            // Get image info
            list( $width, $height ) = getimagesize( $inpath );

            $source_ratio = $width / $height;
            $resize_ratio = $out_width / $out_height;

            if( $width <= $out_width && $height <= $out_height ) {
                $resized_width  = $width;
                $resized_height = $height;
            }
            elseif( $resize_ratio > $source_ratio ) {
                $resized_width  = (int) ( $out_height * $source_ratio );
                $resized_height = $out_height;
            }
            else {
                $resized_width  = $out_width;
                $resized_height = (int) ( $out_width / $source_ratio );
            }

            $thumb   = imagecreatefromjpeg( $inpath );
            $thumb_p = imagecreatetruecolor( $resized_width, $resized_height );
            imagecopyresampled( $thumb_p, $thumb, 0, 0, 0, 0, $resized_width, $resized_height, $width, $height );
            imagejpeg( $thumb_p, $outpath, $quality );

            $result['output'] = ( ( $verbose == true ) ? 'Resized: ' . $file . '<br>' : '' );
            $result['result'] = true;

            return $result;

        }

        function rps_png2jpg( $originalFile, $outputFile )
        {

            $input = imagecreatefrompng( $originalFile );
            list( $width, $height ) = getimagesize( $originalFile );
            $output = imagecreatetruecolor( $width, $height );
            $white  = imagecolorallocate( $output, 255, 255, 255 );
            imagefilledrectangle( $output, 0, 0, $width, $height, $white );
            imagecopy( $output, $input, 0, 0, 0, 0, $width, $height );
            imagejpeg( $output, $outputFile, 100 );
        }

        function rps_resize_image_in_memory( $image, $photo_type, $photo_size )
        {

            if( $photo_type == "Property" && ( $photo_size == 'Photo' || $photo_size == 'ThumbnailPhoto' ) ) {

                if( $photo_size == 'Photo' ) {
                    $out_width  = 256;
                    $out_height = 200;
                }
                elseif( $photo_size == 'ThumbnailPhoto' ) {
                    $out_width  = 130;
                    $out_height = 95;
                }
                else {
                    return false;
                }

                $thumb  = imagecreatefromstring( $image );
                $width  = imagesx( $thumb );
                $height = imagesy( $thumb );

                // Check if image dimensions already match requested resize dimensions, if they do return original data.
                if( $width == $out_width && $height == $out_height ) {
                    return $image;
                }
                else {

                    if( $width > $height ) {
                        $width_t  = $out_width;
                        $height_t = round( ( $height / $width ) * $out_width );
                        $off_x    = 0;
                        $off_y    = ceil( ( $out_height - $height_t ) / 2 );
                    }
                    elseif( $height > $width ) {
                        $height_t = $out_height;
                        $width_t  = round( ( $width / $height ) * $out_height );
                        $off_x    = ceil( ( $out_width - $width_t ) / 2 );
                        $off_y    = 0;
                    }
                    else {
                        $width_t  = $out_width;
                        $height_t = $out_height;
                        $off_x    = 0;
                        $off_y    = 0;
                    }

                    $thumb_p = imagecreatetruecolor( $out_width, $out_height );
                    $bg      = imagecolorallocate( $thumb_p, 255, 255, 255 );

                    imagefill( $thumb_p, 0, 0, $bg );
                    imagecopyresampled( $thumb_p, $thumb, $off_x, $off_y, 0, 0, $width_t, $height_t, $width, $height );

                    ob_start();
                    imagejpeg( $thumb_p );

                    return ob_get_clean();

                }

            }

            // Image type does not need resized, return original data
            return $image;
        }


        /**
         * Return all files found in specified path and return as array.
         * @since    1.0.0
         *
         * @param string $dir Directory to list
         * @return array
         */
        public function rps_resize_photo_files( $photo_type, $verbose = false )
        {

            $photo_path = REALTYPRESS_LISTING_PHOTO_PATH . "/*";
            $filelist   = glob( $photo_path, GLOB_ONLYDIR );

            if( $photo_type == 'Photo' ) {
                $width  = 256;
                $height = 200;
            }
            elseif( $photo_type == 'ThumbnailPhoto' ) {
                $width  = 130;
                $height = 95;
            }

            $fixed_count   = 0;
            $total_count   = 0;
            $result_output = '';
            if( ! empty( $width ) && ! empty( $height ) ) {

                foreach( $filelist as $path ) {
                    $photos = glob( $path . '/*-' . $photo_type . '-*', GLOB_BRACE );

                    foreach( $photos as $photo ) {

                        $result        = $this->rps_resize_image( basename( $photo ), $path, $path, $width, $height, 80, $verbose );
                        $fixed_count   = ( $result['result'] == true ) ? ( $fixed_count + 1 ) : $fixed_count;
                        $total_count   = ( $total_count + 1 );
                        $result_output .= $result['output'];

                    }
                }
            }

            $return                  = array();
            $return['photo_path']    = $photo_path;
            $return['path_count']    = count( $filelist );
            $return['fixed_count']   = $fixed_count;
            $return['total_count']   = $total_count;
            $return['result_output'] = $result_output;

            return $return;

        }

        /**
         * Return all files found in specified path and return as array.
         * @since    1.0.0
         *
         * @param string $dir Directory to list
         * @return array
         */
        public function rps_resize_photo_files_max( $verbose = false )
        {

            $photo_path = REALTYPRESS_LISTING_PHOTO_PATH . "/*";
            $filelist   = glob( $photo_path, GLOB_ONLYDIR );

            $fixed_count   = 0;
            $total_count   = 0;
            $result_output = '';
            if( ! empty( $filelist ) ) {

                foreach( $filelist as $path ) {
                    $photos = glob( $path . '/*-LargePhoto-*', GLOB_BRACE );

                    foreach( $photos as $photo ) {

                        $result        = $this->rps_resize_image_max( $photo, $photo, 850, 850, $verbose );
                        $fixed_count   = ( $result['result'] == true ) ? ( $fixed_count + 1 ) : $fixed_count;
                        $total_count   = ( $total_count + 1 );
                        $result_output .= $result['output'];

                    }
                }
            }

            $return                  = array();
            $return['photo_path']    = $photo_path;
            $return['path_count']    = count( $filelist );
            $return['fixed_count']   = $fixed_count;
            $return['total_count']   = $total_count;
            $return['result_output'] = $result_output;

            return $return;

        }

        /**
         * Return all files found in specified path and return as array.
         * @since    1.0.0
         *
         * @param string $dir Directory to list
         * @return array
         */
        public function rps_resize_agent_photo_files( $verbose = false )
        {

            $photo_path = REALTYPRESS_AGENT_PHOTO_PATH . "/*";
            $filelist   = glob( $photo_path, GLOB_ONLYDIR );

            $fixed_count   = 0;
            $total_count   = 0;
            $result_output = '';
            if( ! empty( $filelist ) ) {

                foreach( $filelist as $path ) {
                    $photos = glob( $path . '/*-LargePhoto*', GLOB_BRACE );

                    foreach( $photos as $photo ) {

                        $result        = $this->rps_resize_image_max( $photo, $photo, 250, 300, $verbose );
                        $fixed_count   = ( $result['result'] == true ) ? ( $fixed_count + 1 ) : $fixed_count;
                        $total_count   = ( $total_count + 1 );
                        $result_output .= $result['output'];

                    }
                }
            }

            $return                  = array();
            $return['photo_path']    = $photo_path;
            $return['path_count']    = count( $filelist );
            $return['fixed_count']   = $fixed_count;
            $return['total_count']   = $total_count;
            $return['result_output'] = $result_output;

            return $return;

        }

        /**
         * Return all files found in specified path and return as array.
         * @since    1.0.0
         *
         * @param string $dir Directory to list
         */
        public function rps_create_resize_photo_file( $photo_type, $inpath, $outpath, $verbose = true )
        {

            //die(pp($photo_type).pp($inpath).pp($outpath));

            if( $photo_type == 'Photo' ) {
                $width  = 256;
                $height = 200;
            }
            elseif( $photo_type == 'ThumbnailPhoto' ) {
                $width  = 130;
                $height = 95;
            }

            if( ! empty( $width ) && ! empty( $height ) ) {
                $this->rps_custom_resize_image( $inpath, $outpath, $width, $height, 80, $verbose );
            }

        }

        /**
         * Return all files found in specified path and return as array.
         * @since    1.0.0
         *
         * @param string $dir Directory to list
         */
        public function rps_create_resize_custom_photo_file( $photo_type, $inpath, $outpath, $verbose = true )
        {

            //die(pp($photo_type).pp($inpath).pp($outpath));

            if( $photo_type == 'AgentThumbnail' ) {
                $width  = 200;
                $height = 300;
            }
            if( $photo_type == 'Agent' ) {
                $width  = 300;
                $height = 400;
            }
            elseif( $photo_type == 'Office' ) {
                $width  = 250;
                $height = 250;
            }

            if( ! empty( $width ) && ! empty( $height ) ) {
                $this->rps_custom_listing_resize_image( $inpath, $outpath, $width, $height, 80, $verbose );
            }

        }


        /**
         * Return all files found in specified path and return as array.
         * @since    1.0.0
         *
         * @param string $dir Directory to list
         * @return array
         */

        public function rps_remove_obsolete_photo_files()
        {

            global $wpdb;

            $photo_path = REALTYPRESS_LISTING_PHOTO_PATH . "/*";
            $filelist   = glob( $photo_path, GLOB_ONLYDIR );

            $output = array();
            if( ! empty( $filelist ) ) {
                foreach( $filelist as $path ) {

                    $listing_id = str_replace( REALTYPRESS_LISTING_PHOTO_PATH . '/', '', $path );
                    $data_count = $wpdb->get_results( " SELECT COUNT(*) FROM `" . $wpdb->prefix . "posts` WHERE `post_excerpt` = '" . $listing_id . "' ", ARRAY_A );

                    if( $data_count[0]["COUNT(*)"] == 0 ) {
                        if( file_exists( $path ) ) {
                            array_map( 'unlink', glob( $path . '/*.jpg' ) );
                            $rmdir    = rmdir( $path );
                            $output[] = ( $rmdir == true ) ? 'Removed => ' . $path . '<br>' : 'Failed => ' . $path . '<br>';
                        }
                    }

                }
            }


            return $output;

        }

        /**
         * Options for recurring select elements.
         * @since    1.0.0
         *
         * @param string $dir Directory to list
         * @return array
         */

        public function rps_get_select_options( $name )
        {

            if( $name == 'AmmenitiesNearBy' ) {
                return array(
                    'Airport',
                    'Highway',
                    'Golf Course',
                    'Park',
                    'Public Transit',
                    'Recreation',
                    'Schools',
                    'Shopping',
                    'Ski Hill',
                    'Ski Area'
                );
            }
            elseif( $name == 'BuildingType' ) {
                return array(
                    ''                           => 'Select a Building Type',
                    'Apartment'                  => 'Apartment',
                    'Commercial Mix'             => 'Commercial Mix',
                    'Duplex'                     => 'Duplex',
                    'Fourplex'                   => 'Fourplex',
                    'Garden Home'                => 'Garden Home',
                    'House'                      => 'House',
                    'Manufactured Home'          => 'Manufactured Home',
                    'Manufactured Home/Mobile'   => 'Manufactured Home/Mobile',
                    'Manufacturing'              => 'Manufacturing',
                    'Mobile Home'                => 'Mobile Home',
                    'Modular'                    => 'Modular',
                    'Multi-Family'               => 'Multi-Family',
                    'Multi-Tenant Industrial'    => 'Multi-Tenant Industrial',
                    'No Building'                => 'No Building',
                    'Offices'                    => 'Offices',
                    'Other'                      => 'Other',
                    'Parking'                    => 'Parking',
                    'Recreational'               => 'Recreational',
                    'Residential Commercial Mix' => 'Residential Commercial Mix',
                    'Retail'                     => 'Retail',
                    'Row / Townhouse'            => 'Row / Townhouse',
                    'Special Purpose'            => 'Special Purpose',
                    'Triplex'                    => 'Triplex',
                    'Two Apartment House'        => 'Two Apartment House',
                    'Unknown'                    => 'Unknown',
                    'Warehouse'                  => 'Warehouse'
                );
            }
            elseif( $name == 'BusinessSubType' ) {
                return array(
                    ''                               => 'Select a Business Sub Type',
                    'Carpentry'                      => 'Carpentry',
                    'Carpeting/Rugs'                 => 'Carpeting/Rugs',
                    'Caterer'                        => 'Caterer',
                    'Clothing store'                 => 'Clothing store',
                    'Coffee shop'                    => 'Coffee shop',
                    'Computer store'                 => 'Computer store',
                    'Confectionary'                  => 'Confectionary',
                    'Construction'                   => 'Construction',
                    'Consultants'                    => 'Consultants',
                    'Contract maintenance'           => 'Contract maintenance',
                    'Convenience store'              => 'Convenience store',
                    'Cottage/Cabin Rental'           => 'Cottage/Cabin Rental',
                    'Craft store'                    => 'Craft store',
                    'Crop farm'                      => 'Crop farm',
                    'Dairy farm'                     => 'Dairy farm',
                    'Day Care'                       => 'Day Care',
                    'Deli'                           => 'Deli',
                    'Department store'               => 'Department store',
                    'Diner'                          => 'Diner',
                    'Donut/Coffee shop'              => 'Donut/Coffee shop',
                    'Dry cleaning'                   => 'Dry cleaning',
                    'Dry Cleaning/laundry'           => 'Dry Cleaning/laundry',
                    'Dry goods/fashion'              => 'Dry goods/fashion',
                    'Electronics store'              => 'Electronics store',
                    'Entertainment'                  => 'Entertainment',
                    'Factory'                        => 'Factory',
                    'Farm/ranch'                     => 'Farm/ranch',
                    'Fast foods'                     => 'Fast foods',
                    'Fish & Chips'                   => 'Fish & Chips',
                    'Fish farm'                      => 'Fish farm',
                    'Florist/Gifts'                  => 'Florist/Gifts',
                    'Food store'                     => 'Food store',
                    'Frozen foods'                   => 'Frozen foods',
                    'Furniture repair'               => 'Furniture repair',
                    'Furniture/household'            => 'Furniture/household',
                    'Gas station'                    => 'Gas station',
                    'General commercial'             => 'General commercial',
                    'General industrial'             => 'General industrial',
                    'General retail'                 => 'General retail',
                    'General sales/services'         => 'General sales/services',
                    'Gifts'                          => 'Gifts',
                    'Go Carts'                       => 'Go Carts',
                    'Goat farm'                      => 'Goat farm',
                    'Golf course'                    => 'Golf course',
                    'Gravel yard'                    => 'Gravel yard',
                    'Greenhouse'                     => 'Greenhouse',
                    'Grocery'                        => 'Grocery',
                    'Hairdressing Salon'             => 'Hairdressing Salon',
                    'Hardware store'                 => 'Hardware store',
                    'Hardware/decorating'            => 'Hardware/decorating',
                    'Health Centre'                  => 'Health Centre',
                    'Health foods'                   => 'Health foods',
                    'Heavy industrial'               => 'Heavy industrial',
                    'Hobby farm'                     => 'Hobby farm',
                    'Hog farm'                       => 'Hog farm',
                    'Horse farm'                     => 'Horse farm',
                    'Hotel'                          => 'Hotel',
                    'Hotel/motel'                    => 'Hotel/motel',
                    'Ice Cream shop'                 => 'Ice Cream shop',
                    'Industrial'                     => 'Industrial',
                    'Industrial Commercial'          => 'Industrial Commercial',
                    'Inn (6 bedrooms plus)'          => 'Inn (6 bedrooms plus)',
                    'Institution'                    => 'Institution',
                    'Jewelry'                        => 'Jewelry',
                    'Laundromat'                     => 'Laundromat',
                    'Lawn & garden'                  => 'Lawn & garden',
                    'Light industrial'               => 'Light industrial',
                    'Lumber'                         => 'Lumber',
                    'Manufacturing'                  => 'Manufacturing',
                    'Manufacturing/Warehouse'        => 'Manufacturing/Warehouse',
                    'Manufacturing/wholesale'        => 'Manufacturing/wholesale',
                    'Marina'                         => 'Marina',
                    'Marina/Resort'                  => 'Marina/Resort',
                    'Marine equipment'               => 'Marine equipment',
                    'Market garden'                  => 'Market garden',
                    'Massotherapy'                   => 'Massotherapy',
                    'Misc retail'                    => 'Misc retail',
                    'Miscellaneous services'         => 'Miscellaneous services',
                    'Miscellanous services'          => 'Miscellanous services',
                    'Mixed - IC&I'                   => 'Mixed - IC&I',
                    'Mixed farm'                     => 'Mixed farm',
                    'Mixed use farm'                 => 'Mixed use farm',
                    'Mobile home park'               => 'Mobile home park',
                    'Motel'                          => 'Motel',
                    'Moving/Trucking'                => 'Moving/Trucking',
                    'Night club'                     => 'Night club',
                    'Nursing/Hospital'               => 'Nursing/Hospital',
                    'Office/residential'             => 'Office/residential',
                    'Offices'                        => 'Offices',
                    'Orchard'                        => 'Orchard',
                    'Other'                          => 'Other',
                    'Outside storage'                => 'Outside storage',
                    'Paint & Wallpaper'              => 'Paint & Wallpaper',
                    'Parking Lot'                    => 'Parking Lot',
                    'Personal consumer service'      => 'Personal consumer service',
                    'Pet & supplies'                 => 'Pet & supplies',
                    'Pharmacy'                       => 'Pharmacy',
                    'Photography'                    => 'Photography',
                    'Pizza shop'                     => 'Pizza shop',
                    'Plants/Nurseries'               => 'Plants/Nurseries',
                    'Plumbing'                       => 'Plumbing',
                    'Poultry farm'                   => 'Poultry farm',
                    'Print shop'                     => 'Print shop',
                    'Professional office(s)'         => 'Professional office(s)',
                    'Pub'                            => 'Pub',
                    'Recreation'                     => 'Recreation',
                    'Repair shop'                    => 'Repair shop',
                    'Residential'                    => 'Residential',
                    'Residential/Commercial'         => 'Residential/Commercial',
                    'Residential/Commercial/Offices' => 'Residential/Commercial/Offices',
                    'Resort'                         => 'Resort',
                    'Restaurant'                     => 'Restaurant',
                    'Restaurant/Banquet'             => 'Restaurant/Banquet',
                    'Restaurant/fast food'           => 'Restaurant/fast food',
                    'Restaurant/pub'                 => 'Restaurant/pub',
                    'Retail & offices'               => 'Retail & offices',
                    'Retail misc.'                   => 'Retail misc.',
                    'Retail/offices/residential'     => 'Retail/offices/residential',
                    'Retirement home'                => 'Retirement home',
                    'Sales/service'                  => 'Sales/service',
                    'School'                         => 'School',
                    'Seafood sales'                  => 'Seafood sales',
                    'See remarks'                    => 'See remarks',
                    'Shoe repair'                    => 'Shoe repair',
                    'Shopping center'                => 'Shopping center',
                    'Single tenant rental'           => 'Single tenant rental',
                    'Special purpose'                => 'Special purpose',
                    'Specialty retail'               => 'Specialty retail',
                    'Sporting Goods'                 => 'Sporting Goods',
                    'Sports & Recreation'            => 'Sports & Recreation',
                    'Storage/Mini'                   => 'Storage/Mini',
                    'Strip mall'                     => 'Strip mall',
                    'Sub shop'                       => 'Sub shop',
                    'Tailor shop'                    => 'Tailor shop',
                    'Tanning Salon'                  => 'Tanning Salon',
                    'Taxi'                           => 'Taxi',
                    'Tourist'                        => 'Tourist',
                    'Travel agency'                  => 'Travel agency',
                    'Tree farm'                      => 'Tree farm',
                    'Truck garage'                   => 'Truck garage',
                    'Trucking'                       => 'Trucking',
                    'Variety store'                  => 'Variety store',
                    'Vegetable farm'                 => 'Vegetable farm',
                    'Vegetable/fruit farm'           => 'Vegetable/fruit farm',
                    'Vineyard'                       => 'Vineyard',
                    'Warehouse'                      => 'Warehouse',
                    'Wholesale'                      => 'Wholesale',
                    'Wood shop'                      => 'Wood shop'
                );
            }
            elseif( $name == 'BusinessType' ) {
                return array(
                    ''                                  => 'Select a Business Type',
                    'Accommodation'                     => 'Accommodation',
                    'Agriculture'                       => 'Agriculture',
                    'Arts and Entertainment'            => 'Arts and Entertainment',
                    'Automobile'                        => 'Automobile',
                    'Construction'                      => 'Construction',
                    'Education Services'                => 'Education Services',
                    'Fishing and Hunting'               => 'Fishing and Hunting',
                    'Food Services and Beverage'        => 'Food Services and Beverage',
                    'Forestry'                          => 'Forestry',
                    'Health Care and Social Assistance' => 'Health Care and Social Assistance',
                    'Hospitality'                       => 'Hospitality',
                    'Industrial'                        => 'Industrial',
                    'Institutional'                     => 'Institutional',
                    'Manufacturing'                     => 'Manufacturing',
                    'Mining and Oil and Gas'            => 'Mining and Oil and Gas',
                    'Other'                             => 'Other',
                    'Personal Services'                 => 'Personal Services',
                    'Professional'                      => 'Professional',
                    'Real Estate'                       => 'Real Estate',
                    'Recreation'                        => 'Recreation',
                    'Remediation Services'              => 'Remediation Services',
                    'Residential'                       => 'Residential',
                    'Resort'                            => 'Resort',
                    'Restaurant'                        => 'Restaurant',
                    'Retail and Wholesale'              => 'Retail and Wholesale',
                    'Scientific and Hi Tech Services'   => 'Scientific and Hi Tech Services',
                    'Service'                           => 'Service',
                    'Special Purpose'                   => 'Special Purpose',
                    'Transportation and Warehousing'    => 'Transportation and Warehousing',
                    'Utilities'                         => 'Utilities',
                    'Waste Management'                  => 'Waste Management'
                );
            }
            elseif( $name == 'OwnershipType' ) {
                return array(
                    ''                       => 'Select an Ownership Type',
                    'Condominium'            => 'Condominium',
                    'Condominium/Strata'     => 'Condominium/Strata',
                    'Cooperative'            => 'Cooperative',
                    'Freehold'               => 'Freehold',
                    'Leasehold'              => 'Leasehold',
                    'Leasehold Condo/Strata' => 'Leasehold Condo/Strata',
                    'Life Lease'             => 'Life Lease',
                    'Other, See Remarks'     => 'Other, See Remarks',
                    'Strata'                 => 'Strata',
                    'Shares in Co-operative' => 'Shares in Co-operative',
                    'Timeshare/Fractional'   => 'Timeshare/Fractional',
                    'Undivided Co-ownership' => 'Undivided Co-ownership',
                    'Unknown'                => 'Unknown'
                );
            }
            elseif( $name == 'PropertyType' ) {
                return array(
                    ''                                => 'Select a Property Type',
                    'Agriculture'                     => 'Agriculture',
                    'Business'                        => 'Business',
                    'Hospitality'                     => 'Hospitality',
                    'Industrial'                      => 'Industrial',
                    'Institutional - Special Purpose' => 'Institutional - Special Purpose',
                    'Multi-family'                    => 'Multi-family',
                    'Office'                          => 'Office',
                    'Other'                           => 'Other',
                    'Parking'                         => 'Parking',
                    'Recreational'                    => 'Recreational',
                    'Retail'                          => 'Retail',
                    'Single Family'                   => 'Single Family',
                    'Vacant Land'                     => 'Vacant Land'
                );
            }
            elseif( $name == 'Province' ) {
                return array(
                    ''                        => 'Select a Province',
                    'Alberta'                 => 'Alberta',
                    'British Columbia'        => 'British Columbia',
                    'Manitoba'                => 'Manitoba',
                    'New Brunswick'           => 'New Brunswick',
                    'Newfoundland & Labrador' => 'Newfoundland & Labrador',
                    'Northwest Territories'   => 'Northwest Territories',
                    'Nova Scotia'             => 'Nova Scotia',
                    'Nunavut'                 => 'Nunavut',
                    'Ontario'                 => 'Ontario',
                    'Prince Edward Island'    => 'Prince Edward Island',
                    'Quebec'                  => 'Quebec',
                    'Saskatchewan'            => 'Saskatchewan',
                    'Yukon'                   => 'Yukon'
                );
            }
            elseif( $name == 'ProvinceShortName' ) {
                return array(
                    ''   => 'Select a Province',
                    'AB' => 'Alberta',
                    'BC' => 'British Columbia',
                    'MB' => 'Manitoba',
                    'NB' => 'New Brunswick',
                    'NL' => 'Newfoundland & Labrador',
                    'NT' => 'Northwest Territories',
                    'NS' => 'Nova Scotia',
                    'NU' => 'Nunavut',
                    'ON' => 'Ontario',
                    'PE' => 'Prince Edward Island',
                    'QC' => 'Quebec',
                    'SK' => 'Saskatchewan',
                    'YT' => 'Yukon'
                );
            }
            elseif( $name == 'LeasePerTime' ) {
                return array(
                    ''         => 'Select a Lease Term',
                    'Daily'    => 'Daily',
                    'Fixed'    => 'Fixed',
                    'Monthly'  => 'Monthly',
                    'Seasonal' => 'Seasonal',
                    'Weekly'   => 'Weekly',
                    'Yearly'   => 'Yearly',
                    'Unknown'  => 'Unknown'
                );
            }
            elseif( $name == 'LeasePerUnit' ) {
                return array(
                    ''              => 'Select a Lease Unit',
                    'Square Feet'   => 'Square Feet',
                    'Square Meters' => 'Square Meters',
                    'Acres'         => 'Acres'
                );
            }
            elseif( $name == 'PricePerUnit' ) {
                return array(
                    ''              => 'Select a Price Unit',
                    'Square Feet'   => 'Square Feet',
                    'Square Meters' => 'Square Meters',
                    'Acres'         => 'Acres'
                );
            }
            elseif( $name == 'RoomLevel' ) {
                return array(
                    ''             => 'Select a Level',
                    'Above'        => 'Above',
                    'Basement'     => 'Basement',
                    'Flat'         => 'Flat',
                    'Fifth level'  => 'Fifth level',
                    'Fourth level' => 'Fourth level',
                    'Ground level' => 'Ground level',
                    'In between'   => 'In between',
                    'Main level'   => 'Main level',
                    'Lower level'  => 'Lower level',
                    'Other'        => 'Other',
                    'Second level' => 'Second level',
                    'Sub-basement' => 'Sub-basement',
                    'Third level'  => 'Third level',
                    'Unknown'      => 'Unknown',
                    'Upper Level'  => 'Upper Level'
                );
            }
            elseif( $name == 'TransactionType' ) {
                return array(
                    ''          => 'Select a Transaction Type',
                    'for lease' => 'For Lease',
                    'for rent'  => 'For Rent',
                    'for sale'  => 'For Sale',
                    // 'for sale_or_rent' => 'For Sale or Rent'
                );
            }
            elseif( $name == 'RoomType' ) {
                return array(
                    ''                         => 'Select a Room',
                    '1pc Bathroom'             => '1pc Bathroom',
                    '1pc Ensuite bath'         => '1pc Ensuite bath',
                    '2pc Bathroom'             => '2pc Bathroom',
                    '2pc Ensuite bath'         => '2pc Ensuite bath',
                    '3pc Bathroom'             => '3pc Bathroom',
                    '3pc Ensuite bath'         => '3pc Ensuite bath',
                    '4pc Bathroom'             => '4pc Bathroom',
                    '4pc Ensuite bath'         => '4pc Ensuite bath',
                    '5pc Bathroom'             => '5pc Bathroom',
                    '5pc Ensuite bath'         => '5pc Ensuite bath',
                    '6pc Bathroom'             => '6pc Bathroom',
                    '6pc Ensuite bath'         => '6pc Ensuite bath',
                    'Addition'                 => 'Addition',
                    'Additional bedroom'       => 'Additional bedroom',
                    'Atrium'                   => 'Atrium',
                    'Attic'                    => 'Attic',
                    'Attic (finished)'         => 'Attic (finished)',
                    'Bath (# pieces 1-6)'      => 'Bath (# pieces 1-6)',
                    'Bathroom'                 => 'Bathroom',
                    'Bedroom'                  => 'Bedroom',
                    'Bedroom 2'                => 'Bedroom 2',
                    'Bedroom 3'                => 'Bedroom 3',
                    'Bedroom 4'                => 'Bedroom 4',
                    'Bedroom 5'                => 'Bedroom 5',
                    'Bedroom 6'                => 'Bedroom 6',
                    'Bonus Room'               => 'Bonus Room',
                    'Breakfast'                => 'Breakfast',
                    'Cold room'                => 'Cold room',
                    'Computer Room'            => 'Computer Room',
                    'Conservatory'             => 'Conservatory',
                    'Den'                      => 'Den',
                    'Dinette'                  => 'Dinette',
                    'Dining nook'              => 'Dining nook',
                    'Dining room'              => 'Dining room',
                    'Eat in kitchen'           => 'Eat in kitchen',
                    'Eating area'              => 'Eating area',
                    'Enclosed porch'           => 'Enclosed porch',
                    'Ensuite'                  => 'Ensuite',
                    'Ensuite (# pieces 2-6)'   => 'Ensuite (# pieces 2-6)',
                    'Exercise room'            => 'Exercise room',
                    'Family room'              => 'Family room',
                    'Family room/Fireplace'    => 'Family room/Fireplace',
                    'Flex room'                => 'Flex room',
                    'Florida room'             => 'Florida room',
                    'Florida/Fireplace'        => 'Florida/Fireplace',
                    'Foyer'                    => 'Foyer',
                    'Fruit cellar'             => 'Fruit cellar',
                    'Full bathroom'            => 'Full bathroom',
                    'Full ensuite bathroom'    => 'Full ensuite bathroom',
                    'Games room'               => 'Games room',
                    'Great room'               => 'Great room',
                    'Gym'                      => 'Gym',
                    'Hall'                     => 'Hall',
                    'Hobby room'               => 'Hobby room',
                    'Inlaw suite'              => 'Inlaw suite',
                    'Kitchen'                  => 'Kitchen',
                    'Kitchen/Dining room'      => 'Kitchen/Dining room',
                    'Laundry room'             => 'Laundry room',
                    'Library'                  => 'Library',
                    'Living room'              => 'Living room',
                    'Living room/Dining room'  => 'Living room/Dining room',
                    'Living room/Fireplace'    => 'Living room/Fireplace',
                    'Lobby'                    => 'Lobby',
                    'Loft'                     => 'Loft',
                    'Master bedroom'           => 'Master bedroom',
                    'Media'                    => 'Media',
                    'Mud room'                 => 'Mud room',
                    'Muskoka Room'             => 'Muskoka Room',
                    'Not known'                => 'Not known',
                    'Nursery'                  => 'Nursery',
                    'Office'                   => 'Office',
                    'Other'                    => 'Other',
                    'Pantry'                   => 'Pantry',
                    'Partial bathroom'         => 'Partial bathroom',
                    'Playroom'                 => 'Playroom',
                    'Porch'                    => 'Porch',
                    'Recreation room'          => 'Recreation room',
                    'Recreational, Games room' => 'Recreational, Games room',
                    'Roughed-In Bathroom'      => 'Roughed-In Bathroom',
                    'Sauna'                    => 'Sauna',
                    'Second Kitchen'           => 'Second Kitchen',
                    'Sitting room'             => 'Sitting room',
                    'Solarium'                 => 'Solarium',
                    'Storage'                  => 'Storage',
                    'Study'                    => 'Study',
                    'Sunroom'                  => 'Sunroom',
                    'Sunroom/Fireplace'        => 'Sunroom/Fireplace',
                    'Utility room'             => 'Utility room',
                    'Walk-In Closet'           => 'Walk-In Closet',
                    'Walk Up Attic'            => 'Walk Up Attic',
                    'Wardrobe'                 => 'Wardrobe',
                    'Wine Cellar'              => 'Wine Cellar',
                    'Workshop'                 => 'Workshop'
                );

            }
            elseif( $name == 'TrueOrFalse' ) {
                return array(
                    'True' => 'Yes',
                    ''     => 'No'
                );
            }
            elseif( $name == 'Position' ) {
                return array(
                    ''                                 => 'Select a Position',
                    'Assistant'                        => 'Assistant',
                    'Associate'                        => 'Associate',
                    'Associate Broker'                 => 'Associate Broker',
                    'Broker'                           => 'Broker',
                    'Broker Manager'                   => 'Broker Manager',
                    'Broker of Record'                 => 'Broker of Record',
                    'Brokerage Staff'                  => 'Brokerage Staff',
                    'Employee'                         => 'Employee',
                    'Honorary'                         => 'Honorary',
                    'Immediate Past President'         => 'Immediate Past President',
                    'None'                             => 'None',
                    'Office Administrator'             => 'Office Administrator',
                    'Office Manager'                   => 'Office Manager',
                    'Other'                            => 'Other',
                    'Personal Real Estate Corporation' => 'Personal Real Estate Corporation',
                    'President'                        => 'President',
                    'Professional Subscriber'          => 'Professional Subscriber',
                    'Sales Person'                     => 'Sales Person',
                    'Sales Person on Exemption'        => 'Sales Person on Exemption',
                    'Sales Representative'             => 'Sales Representative'
                );
            }
            elseif( $name == 'PhoneType' ) {
                return array(
                    ''          => 'Phone Type',
                    'Cell'      => 'Cell',
                    'Direct'    => 'Direct',
                    'Fax'       => 'Fax',
                    'Telephone' => 'Telephone'
                );
            }
            elseif( $name == 'WebsiteType' ) {
                return array(
                    ''         => 'Website Type',
                    'Website'  => 'Website',
                    'Facebook' => 'Facebook',
                    'LinkedIn' => 'LinkedIn',
                    'Twitter'  => 'Twitter'

                );
            }


            return array();

        }

    }
}
