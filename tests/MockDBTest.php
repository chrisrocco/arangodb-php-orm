<?php
use vector\ArangoORM\DB\DB;
use vector\ArangoORM\DB\MockDB;

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

        return $generated;
    }

    function testOneToOne(){
        $dh = DB::getDocumentHandler();
        $ch = DB::getCollectionHandler();
        if(!$ch->has("a")) $ch->create( "a" );
        if(!$ch->has("b")) $ch->create( "b" );
        if(!$ch->has("a_to_b")) $ch->create( "a_to_b", [ 'type' => 3 ] );
        for ( $i = 0; $i < 100; $i++ ){
            $user = \triagens\ArangoDb\Document::createFromArray( [] );
            $market = \triagens\ArangoDb\Document::createFromArray( [] );
            $dh->store( $user, "a" );
            $dh->store( $market, "b" );
        }
        MockDB::oneToOne( "a", "b", "a_to_b" );

        $aCount = DB::getAll( "a" )->getCount();
        $bCount = DB::getAll( "b" )->getCount();
        $min = $aCount;
        if( $bCount < $aCount ) $min = $bCount;

        self::assertEquals( DB::getAll("a_to_b")->getCount(), $min );

        $ch->drop( "a_to_b" );
        $ch->drop( "a" );
        $ch->drop( "b" );
    }

    function testOneToMany(){
        $dh = DB::getDocumentHandler();
        $ch = DB::getCollectionHandler();
        DB::createCollectionIfNotExsists( "one" );
        DB::createCollectionIfNotExsists( "toMany" );
        for ( $i = 0; $i < 100; $i++ ){
            $user = \triagens\ArangoDb\Document::createFromArray( [] );
            $market = \triagens\ArangoDb\Document::createFromArray( [] );
            $dh->store( $user, "one" );
            $dh->store( $market, "toMany" );
        }
        MockDB::oneToMany( "one", "toMany", "some_edge_collection", "outbound" );
        $ch->drop( "one" );
        $ch->drop( "toMany" );
        $ch->drop( "some_edge_collection" );
    }

}