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

}