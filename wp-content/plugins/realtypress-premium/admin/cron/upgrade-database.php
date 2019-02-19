<?php

/**
 * RealtyPress Unix Cron
 *
 * @since      1.3.0
 */

// Load wordpress
define( 'SAVEQUERIES', false );
define( 'WP_USE_THEMES', true );
require_once( '../../../../../wp-load.php' );

// If wordpress is loaded.
if( defined( 'ABSPATH' ) ) {

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    require_once( ABSPATH . 'wp-content/plugins/realtypress-premium/includes/constants-realtypress.php' );

    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();
    $tbl_name        = $wpdb->prefix . 'rps_agent';
    $sql             = "CREATE TABLE " . $tbl_name . " (
      agent_id bigint(10) NOT NULL AUTO_INCREMENT,
      AgentID bigint(10) NOT NULL,
      OfficeID bigint(10) NOT NULL,
      Name varchar(100) DEFAULT NULL,
      ID varchar(10) DEFAULT NULL,
      LastUpdated varchar(20) DEFAULT NULL,
      Position varchar(50) DEFAULT NULL,
      EducationCredentials varchar(60) DEFAULT NULL,
      Photos text,
      PhotoLastUpdated varchar(20) DEFAULT NULL,
      Specialties varchar(100) DEFAULT NULL,
      Specialty varchar(100) DEFAULT NULL,
      Languages varchar(100) DEFAULT NULL,
      Language varchar(100) DEFAULT NULL,
      TradingAreas varchar(100) DEFAULT NULL,
      TradingArea varchar(100) DEFAULT NULL,
      Phones blob,
      Websites blob,
      Designations blob,
      PRIMARY KEY  (agent_id),
      UNIQUE KEY AgentID_2 (AgentID),
      UNIQUE KEY AgentID_3 (AgentID),
      KEY AgentID (AgentID),
      KEY OfficeID (OfficeID)
    ) $charset_collate;";
    dbDelta( $sql );

    // =============================
    //  Office Table
    // =============================

    $tbl_name = $wpdb->prefix . 'rps_office';
    $sql      = "CREATE TABLE " . $tbl_name . " (
      office_id bigint(10) NOT NULL AUTO_INCREMENT,
      OfficeID bigint(10) NOT NULL,
      Name varchar(150) DEFAULT NULL,
      ID bigint(10) NOT NULL,
      LastUpdated varchar(20) DEFAULT NULL,
      LogoLastUpdated varchar(20) DEFAULT NULL,
      Logos text,
      OrganizationType varchar(150) DEFAULT NULL,
      Designation varchar(150) DEFAULT NULL,
      Address varchar(100) DEFAULT NULL,
      Franchisor varchar(100) DEFAULT NULL,
      StreetAddress varchar(80) DEFAULT NULL,
      AddressLine1 varchar(60) DEFAULT NULL,
      AddressLine2 varchar(60) DEFAULT NULL,
      City varchar(50) DEFAULT NULL,
      Province varchar(35) DEFAULT NULL,
      PostalCode varchar(6) DEFAULT NULL,
      Country varchar(20) DEFAULT NULL,
      AdditionalStreetInfo varchar(30) DEFAULT NULL,
      CommunityName varchar(30) DEFAULT NULL,
      Neighbourhood varchar(30) DEFAULT NULL,
      Subdivision varchar(30) DEFAULT NULL,
      Phones blob,
      Websites blob,
      PRIMARY KEY  (office_id),
      UNIQUE KEY OfficeID_2 (OfficeID),
      KEY OfficeID (OfficeID)
    ) $charset_collate;";
    dbDelta( $sql );

    // =============================
    //  Boards Table
    // =============================

    $tbl_name = $wpdb->prefix . 'rps_boards';
    $sql      = "CREATE TABLE " . $tbl_name . " (
      id int(9) NOT NULL AUTO_INCREMENT,
      OrganizationID int(9) NOT NULL,
      ShortName varchar(75) NOT NULL,
      LongName varchar(200) NOT NULL,
      PRIMARY KEY (id)
    ) $charset_collate;";
    dbDelta( $sql );

    // If no data exists in database
    $boards_count = $wpdb->get_results( " SELECT COUNT(*) FROM `" . REALTYPRESS_TBL_BOARDS . "` ", ARRAY_A );
    if( ! empty( $boards_count ) && $boards_count[0]["COUNT(*)"] == 0 ) {
        $tbl_name = $wpdb->prefix . 'rps_boards';
        $sql      = "INSERT INTO " . $tbl_name . " VALUES (1,1,'Vancouver Island','Vancouver Island Real Estate Board'),(2,2,'BC Northern','BC Northern Real Estate Board'),(3,3,'Victoria','Victoria Real Estate Board'),(4,4,'Chilliwack','Chilliwack & District Real Estate Board'),(5,5,'Montréal','Greater Montréal Real Estate Board'),(6,6,'Fraser Valley','Fraser Valley Real Estate Board'),(7,7,'Winnipeg','Winnipeg REALTORS® Association'),(8,8,'South Okanagan','South Okanagan Real Estate Board'),(9,9,'Calgary','Calgary Real Estate Board'),(10,10,'Edmonton','REALTORS® Association of Edmonton'),(11,11,'Kamloops','Kamloops & District Real Estate Association'),(12,12,'Kootenay','Kootenay Real Estate Board'),(13,13,'London','London and St. Thomas Association of REALTORS®'),(14,14,'Hamilton-Burlington','REALTORS® Association of Hamilton-Burlington'),(15,15,'Oakville-Milton','Oakville, Milton & District Real Estate Board'),(16,16,'Kitchener-Waterloo','Kitchener-Waterloo Association of REALTORS®'),(17,17,'Barrie','Barrie & District Association of REALTORS® Inc.'),(18,19,'Okanagan-Mainline','Okanagan-Mainline Real Estate Board'),(19,20,'Cambridge','Cambridge Association of REALTORS® Inc.'),(20,23,'Brandon','Brandon Area REALTORS®'),(21,24,'Southern Georgian Bay','Southern Georgian Bay Association of REALTORS® '),(22,25,'Red Deer (Central Alberta)','Central Alberta REALTORS® Association'),(23,26,'Lethbridge','Lethbridge & District Association of REALTORS®'),(24,27,'Saskatoon','Saskatoon Region Association of REALTORS® Inc.'),(25,28,'Regina','Association Of Regina REALTORS®'),(26,30,'Simcoe','Simcoe & District Real Estate Board'),(27,31,'Peterborough','Peterborough & Kawarthas Association REALTORS®'),(28,32,'Chatham Kent','Chatham Kent Association of REALTORS®'),(29,33,'Woodstock','Woodstock-Ingersoll Real Estate Board'),(30,34,'Windsor','Windsor-Essex County Association of REALTORS®'),(31,35,'Sudbury','Sudbury Real Estate Board'),(32,36,'Yellowknife','Yellowknife Real Estate Board'),(33,37,'Kingston','Kingston & Area Real Estate Association'),(34,38,'Grande Prairie','Grande Prairie & Area Association of REALTORS®'),(35,39,'Prince Albert','Prince Albert & District Association of REALTORS®'),(36,41,'Brantford','Brantford Regional Real Estate Assn Inc'),(37,43,'Grey Bruce Owen Sound','REALTORS® Association of Grey Bruce Owen Sound'),(38,44,'Guelph','Guelph & District Association of REALTORS®'),(39,45,'Moncton','Greater Moncton REALTORS® du Grand Moncton'),(40,46,'Kawartha Lakes','Kawartha Lakes Real Estate Association'),(41,47,'The Lakelands','Muskoka Haliburton Orillia – The Lakelands Association of REALTORS®'),(42,48,'Laurentides','Chambre immobilière des Laurentides'),(43,49,'Medicine Hat','Medicine Hat Real Estate Board Co-op'),(44,50,'Northumberland Hills','Northumberland Hills Association of REALTORS®'),(45,51,'Huron Perth','Huron Perth Association of REALTORS®'),(46,52,'Québec','Chambre immobilière de Québec'),(47,53,'Tillsonburg','Tillsonburg District Real Estate Board'),(48,54,'Mauricie','Chambre Immobilière de La Mauricie'),(49,57,'Haute-Yamaska','Chambre immobilière de la Haute-Yamaska'),(50,60,'Portage','Portage La Prairie Real Estate Board'),(51,61,'North Bay','North Bay Real Estate Board'),(52,62,'Yukon','Yukon Real Estate Asscociation'),(53,64,'Sault Ste. Marie','Sault Ste. Marie Real Estate Board'),(54,65,'Alberta West','Alberta West REALTORS® Association'),(55,66,'Brooks(South Central Alberta)','REALTORS® Association of South Central Alberta'),(56,69,'ASR','Association of Saskatchewan REALTORS®'),(57,70,'Lloydminster','REALTORS® Association of Lloydminster & District'),(58,74,'MREA','Manitoba Real Estate Association'),(59,76,'Ottawa','Ottawa Real Estate Board'),(60,77,'Renfrew','Renfrew County Real Estate Board'),(61,78,'St-Hyacinthe','Chambre Immobilière de St-Hyacinthe'),(62,81,'PEIA','Prince Edward Island Real Estate Association'),(63,82,'Toronto','Toronto Real Estate Board'),(64,83,'Powell River','Powell River Sunshine Coast Real Estate Board'),(65,84,'Saint John','Saint John Real Estate Board Inc'),(66,85,'Mississauga','Mississauga Real Estate Board'),(67,86,'Brampton','Brampton Real Estate Board'),(68,88,'Durham','Durham Region Association of REALTORS®'),(69,89,'Greater Vancouver','Real Estate Board Of Greater Vancouver'),(70,90,'Timmins','Timmins, Cochrane & Timiskaming District Association of REALTORS®'),(71,91,'Thunder Bay','Thunder Bay Real Estate Board'),(72,92,'Abitibi-Témiscamingue','Chambre immobilière de l\'Abitibi-Témiscamingue'),(73,93,'Rideau St.Lawrence','Rideau - St. Lawrence Real Estate Board'),(74,94,'Centre du Québec','Chambre immobilière du Centre du Québec'),(75,95,'Sarnia','Sarnia-Lambton Real Estate Board'),(76,96,'Bancroft','Bancroft and District Real Estate Board'),(77,97,'Cornwall','Cornwall & District Real Estate Board'),(78,98,'Orangeville','Orangeville & District Real Estate Board'),(79,100,'Quinte','Quinte & District Association of REALTORS® Inc.'),(80,101,'OREA','Ontario Real Estate Association'),(81,103,'AREA','The Alberta Real Estate Association'),(82,105,'BCREA','British Columbia Real Estate Association'),(83,106,'Annapolis Valley','Annapolis Valley Real Estate Board'),(84,107,'NSAR','Nova Scotia Association of REALTORS®'),(85,108,'FCIQ','The Quebec Federation of Real Estate Boards'),(86,109,'Outaouais','Chambre immobilière de l’Outaouais'),(87,110,'Parry Sound','Parry Sound Real Estate Board'),(88,114,'Niagara','Niagara Association of REALTORS®'),(89,115,'Saguenay-Lac St-Jean','Chambre immobilière de Saguenay-Lac St-Jean'),(90,117,'Newfoundland & Labrador','The Newfoundland & Labrador Association of REALTORS®'),(91,118,'NBREA','New Brunswick Real Estate Association'),(92,119,'Lanaudière','Chambre immobilière de Lanaudière'),(93,121,'Fredericton','The Real Estate Board of Fredericton Area Inc.'),(94,122,'Fort McMurray','Fort McMurray REALTORS®'),(95,123,'Estrie','Chambre immobilière de l’Estrie'),(96,125,'CREA','The Canadian Real Estate Association'),(97,275323,'NULL','CREA Beta REALTOR Link® Test')";
        $wpdb->query( $sql );
    }

    // =============================
    //  Property Table
    // =============================

    $tbl_name = $wpdb->prefix . 'rps_property';
    $sql      = "CREATE TABLE " . $tbl_name . " (
      property_id bigint(12) NOT NULL AUTO_INCREMENT,
      PostID bigint(12) NOT NULL,
      Offices varchar(40) NOT NULL,
      Agents varchar(50) NOT NULL,
      Board varchar(4) DEFAULT NULL,
      ListingID bigint(20) NOT NULL,
      DdfListingID varchar(25) NOT NULL,
      LastUpdated varchar(25) DEFAULT NULL,
      Latitude varchar(16) DEFAULT NULL,
      Longitude varchar(16) DEFAULT NULL,
      AmmenitiesNearBy varchar(120) DEFAULT NULL,
      CommunicationType varchar(80) DEFAULT NULL,
      CommunityFeatures varchar(100) DEFAULT NULL,
      Crop varchar(40) DEFAULT NULL,
      DocumentType varchar(10) DEFAULT NULL,
      EquipmentType varchar(70) DEFAULT NULL,
      Easement varchar(60) DEFAULT NULL,
      FarmType varchar(60) DEFAULT NULL,
      Features text,
      IrrigationType varchar(20) DEFAULT NULL,
      Lease varchar(20) DEFAULT NULL,
      LeasePerTime varchar(20) DEFAULT NULL,
      LeasePerUnit varchar(20) DEFAULT NULL,
      LeaseTermRemaining varchar(20) DEFAULT NULL,
      LeaseTermRemainingFreq varchar(20) DEFAULT NULL,
      LeaseType varchar(30) DEFAULT NULL,
      ListingContractDate varchar(15) DEFAULT NULL,
      LiveStockType varchar(20) DEFAULT NULL,
      LoadingType varchar(35) DEFAULT NULL,
      LocationDescription text,
      Machinery varchar(30) DEFAULT NULL,
      MaintenanceFee varchar(20) DEFAULT NULL,
      MaintenanceFeePaymentUnit varchar(20) DEFAULT NULL,
      MaintenanceFeeType varchar(150) DEFAULT NULL,
      ManagementCompany varchar(100) DEFAULT NULL,
      MunicipalID varchar(20) DEFAULT NULL,
      OwnershipType varchar(40) DEFAULT NULL,
      ParkingSpaceTotal varchar(10) DEFAULT NULL,
      Plan varchar(20) DEFAULT NULL,
      PoolType varchar(80) DEFAULT NULL,
      PoolFeatures varchar(80) DEFAULT NULL,
      Price decimal(64,2) DEFAULT 0.00,
      PricePerTime varchar(20) DEFAULT NULL,
      PricePerUnit varchar(20) DEFAULT NULL,
      PropertyType varchar(40) DEFAULT NULL,
      PublicRemarks text,
      RentalEquipmentType varchar(80) DEFAULT NULL,
      RightType varchar(30) DEFAULT NULL,
      RoadType varchar(60) DEFAULT NULL,
      StorageType varchar(40) DEFAULT NULL,
      Structure varchar(90) DEFAULT NULL,
      SignType varchar(45) DEFAULT NULL,
      TransactionType varchar(25) DEFAULT NULL,
      TotalBuildings varchar(10) DEFAULT NULL,
      ViewType varchar(150) DEFAULT NULL,
      WaterFrontType varchar(50) DEFAULT NULL,
      WaterFrontName varchar(100) DEFAULT NULL,
      AdditionalInformationIndicator varchar(20) DEFAULT NULL,
      ZoningDescription varchar(60) DEFAULT NULL,
      ZoningType varchar(60) DEFAULT NULL,
      MoreInformationLink varchar(255) DEFAULT NULL,
      AnalyticsClick blob,
      AnalyticsView blob,
      BusinessType varchar(160) DEFAULT NULL,
      BusinessSubType varchar(160) DEFAULT NULL,
      EstablishedDate varchar(20) DEFAULT NULL,
      Franchise varchar(20) DEFAULT NULL,
      Name varchar(60) DEFAULT NULL,
      OperatingSince varchar(15) DEFAULT NULL,
      BathroomTotal tinyint(3) DEFAULT 0,
      BedroomsAboveGround tinyint(3) DEFAULT NULL,
      BedroomsBelowGround tinyint(3) DEFAULT NULL,
      BedroomsTotal tinyint(3) DEFAULT 0,
      Age varchar(30) DEFAULT NULL,
      Amenities varchar(150) DEFAULT NULL,
      Amperage varchar(10) DEFAULT NULL,
      Anchor varchar(10) DEFAULT NULL,
      Appliances text,
      ArchitecturalStyle varchar(80) DEFAULT NULL,
      BasementDevelopment varchar(70) DEFAULT NULL,
      BasementFeatures varchar(50) DEFAULT NULL,
      BasementType varchar(125) DEFAULT NULL,
      BomaRating varchar(20) DEFAULT NULL,
      CeilingHeight varchar(10) DEFAULT NULL,
      CeilingType varchar(50) DEFAULT NULL,
      ClearCeilingHeight varchar(10) DEFAULT NULL,
      ConstructedDate varchar(10) DEFAULT NULL,
      ConstructionMaterial varchar(70) DEFAULT NULL,
      ConstructionStatus varchar(20) DEFAULT NULL,
      ConstructionStyleAttachment varchar(20) DEFAULT NULL,
      ConstructionStyleOther varchar(20) DEFAULT NULL,
      ConstructionStyleSplitLevel varchar(20) DEFAULT NULL,
      CoolingType varchar(100) DEFAULT NULL,
      EnerguideRating varchar(10) DEFAULT NULL,
      ExteriorFinish varchar(100) DEFAULT NULL,
      FireProtection varchar(100) DEFAULT NULL,
      FireplaceFuel varchar(40) DEFAULT NULL,
      FireplacePresent varchar(5) DEFAULT NULL,
      FireplaceTotal varchar(3) DEFAULT NULL,
      FireplaceType varchar(80) DEFAULT NULL,
      Fixture varchar(60) DEFAULT NULL,
      FlooringType varchar(120) DEFAULT NULL,
      FoundationType varchar(70) DEFAULT NULL,
      HalfBathTotal tinyint(3) DEFAULT NULL,
      HeatingFuel varchar(70) DEFAULT NULL,
      HeatingType varchar(120) DEFAULT NULL,
      LeedsCategory varchar(10) DEFAULT NULL,
      LeedsRating varchar(10) DEFAULT NULL,
      RenovatedDate varchar(10) DEFAULT NULL,
      RoofMaterial varchar(80) DEFAULT NULL,
      RoofStyle varchar(60) DEFAULT NULL,
      StoriesTotal int(3) DEFAULT NULL,
      SizeExterior varchar(20) DEFAULT NULL,
      SizeInterior varchar(20) DEFAULT NULL,
      SizeInteriorFinished varchar(20) DEFAULT NULL,
      StoreFront varchar(20) DEFAULT NULL,
      TotalFinishedArea varchar(20) DEFAULT NULL,
      Type varchar(40) DEFAULT NULL,
      Uffi varchar(30) DEFAULT NULL,
      UnitType varchar(10) DEFAULT NULL,
      UtilityPower varchar(50) DEFAULT NULL,
      UtilityWater varchar(80) DEFAULT NULL,
      VacancyRate varchar(10) DEFAULT NULL,
      SizeTotal varchar(50) DEFAULT NULL,
      SizeTotalText varchar(100) DEFAULT NULL,
      SizeFrontage varchar(30) DEFAULT NULL,
      AccessType varchar(80) DEFAULT NULL,
      Acreage varchar(5) DEFAULT NULL,
      LandAmenities varchar(120) DEFAULT NULL,
      ClearedTotal varchar(10) DEFAULT NULL,
      CurrentUse varchar(40) DEFAULT NULL,
      Divisible varchar(10) DEFAULT NULL,
      FenceTotal varchar(10) DEFAULT NULL,
      FenceType varchar(50) DEFAULT NULL,
      FrontsOn varchar(30) DEFAULT NULL,
      LandDisposition varchar(30) DEFAULT NULL,
      LandscapeFeatures varchar(200) DEFAULT NULL,
      PastureTotal varchar(10) DEFAULT NULL,
      Sewer varchar(60) DEFAULT NULL,
      SizeDepth varchar(25) DEFAULT NULL,
      SizeIrregular varchar(80) DEFAULT NULL,
      SoilEvaluation varchar(10) DEFAULT NULL,
      SoilType varchar(50) DEFAULT NULL,
      SurfaceWater varchar(50) DEFAULT NULL,
      TiledTotal varchar(10) DEFAULT NULL,
      TopographyType varchar(10) DEFAULT NULL,
      StreetAddress varchar(100) DEFAULT NULL,
      AddressLine1 varchar(100) DEFAULT NULL,
      AddressLine2 varchar(100) DEFAULT NULL,
      StreetNumber varchar(20) DEFAULT NULL,
      StreetName varchar(60) DEFAULT NULL,
      StreetSuffix varchar(20) DEFAULT NULL,
      StreetDirectionSuffix varchar(15) DEFAULT NULL,
      UnitNumber varchar(20) DEFAULT NULL,
      City varchar(80) DEFAULT NULL,
      Province varchar(35) DEFAULT NULL,
      PostalCode varchar(6) DEFAULT NULL,
      Country varchar(20) DEFAULT NULL,
      AdditionalStreetInfo varchar(100) DEFAULT NULL,
      CommunityName varchar(100) DEFAULT NULL,
      Neighbourhood varchar(100) DEFAULT NULL,
      Subdivision varchar(100) DEFAULT NULL,
      Utilities blob,
      Parking blob,
      OpenHouse blob,
      AlternateURL blob,
      PRIMARY KEY  (property_id),
      UNIQUE KEY ListingID_2 (ListingID),
      KEY Latitude (Latitude),
      KEY Longitude (Longitude),
      KEY ListingID (ListingID)
    ) $charset_collate;";
    dbDelta( $sql );

    if( $wpdb->get_var( "SHOW TABLES LIKE '$tbl_name'" ) == $tbl_name ) {
        $wpdb->query( "ALTER TABLE $tbl_name DROP INDEX DdfListingID" );
    }

    // =============================
    //  Property Photos Table
    // =============================

    $tbl_name = $wpdb->prefix . 'rps_property_photos';
    $sql      = "CREATE TABLE " . $tbl_name . " (
      details_id bigint(20) NOT NULL AUTO_INCREMENT,
      ListingID bigint(20) NOT NULL,
      SequenceID int(10) DEFAULT NULL,
      Description varchar(200) DEFAULT NULL,
      Photos blob,
      LastUpdated varchar(20) DEFAULT NULL,
      PhotoLastUpdated varchar(35) DEFAULT NULL,
      PRIMARY KEY  (details_id),
      UNIQUE KEY ListingID_2 (ListingID,SequenceID),
      KEY ListingID (ListingID)
    ) $charset_collate;";
    dbDelta( $sql );

    // =============================
    //  Property Rooms Table
    // =============================

    $tbl_name = $wpdb->prefix . 'rps_property_rooms';
    $sql      = "CREATE TABLE " . $tbl_name . " (
      room_id bigint(20) NOT NULL AUTO_INCREMENT,
      ListingID bigint(20) NOT NULL,
      Type varchar(40) DEFAULT NULL,
      Width varchar(20) DEFAULT NULL,
      Length varchar(20) DEFAULT NULL,
      Level varchar(20) DEFAULT NULL,
      Dimension varchar(40) DEFAULT NULL,
      PRIMARY KEY  (room_id),
      KEY ListingID (ListingID)
    ) $charset_collate;";
    dbDelta( $sql );

    if( $wpdb->get_var( "SHOW TABLES LIKE '$tbl_name'" ) == $tbl_name ) {
        $wpdb->query( "ALTER TABLE $tbl_name DROP INDEX ListingID_2" );
    }

}
else {

    echo 'Cannot update database.';
}