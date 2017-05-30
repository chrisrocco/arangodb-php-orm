<?php
use rocco\ArangoORM\DB\DB;
use rocco\ArangoORM\DB\MockDB;

/**
 *  Corresponding Class to test YourClass class
 *
 *  For each class in your library, there should be a corresponding Unit-Test for it
 *  Unit-Tests should be as much as possible independent from other test going on.
 *
 * @author yourname
 */
class MockDBTest extends BaseTest
{

    function testGenerateMockData()
    {
        $how_many = 100;

        $title_templates = [
            "Christmas in %s",
            "The %s Carolers",
            "Caroling in %s",
            "%s Holiday Entertainment",
            "%s Christmas Choristers"
        ];

        $cities = [
            "Birmingham",
            "Dallas",
            "Washington",
            "NOLA",
            "New York",
            "Pittsburg",
            "Orlando"
        ];

        $generated = MockDB::generateMockData( $title_templates, $cities, $how_many );

        self::assertEquals( $how_many, count($generated) );
    }

    function testMockDocuments(){
        $user_template = [
            "name"  =>  "@name",
            "email" =>  "@email"
        ];

        $name_templates = [ "Chris %s", "John %s", "Tim %s", "Caleb %s" ];
        $name_values = [ "Carter", "Rayy", "Bird", "Johnstone" ];

        $email_templates = [ "%s@gmail.com", "%s@aol.com", "%s@yahoo.com" ];
        $email_values = [ "chris", "caleb", "tim", "john" ];

        $how_many = 50;
        $generated = MockDB::mockDocuments( $user_template, [
            "@name" => [
                "templates" =>  $name_templates,
                "values"    =>  $name_values
            ],
            "@email" => [
                "templates" => $email_templates,
                "values" => $email_values
            ]
        ], $how_many );

        self::assertEquals( $how_many, count($generated) );
    }

    function testOneToOne(){
        $dh = DB::getDocumentHandler();
        $ch = DB::getCollectionHandler();
        if(!$ch->has("users")) $ch->create( "users" );
        if(!$ch->has("markets")) $ch->create( "markets" );
        for ( $i = 0; $i < 100; $i++ ){
            $user = \triagens\ArangoDb\Document::createFromArray( [] );
            $market = \triagens\ArangoDb\Document::createFromArray( [] );
            $dh->store( $user, "users" );
            $dh->store( $market, "markets" );
        }
        MockDB::oneToOne( "users", "markets", "in_market" );

        $usersCount = DB::getAll( "users" )->getCount();
        $marketsCount = DB::getAll( "markets" )->getCount();
        $min = $usersCount;
        if( $marketsCount < $usersCount ) $min = $marketsCount;

        self::assertEquals( DB::getAll("in_market")->getCount(), $min );
    }

}