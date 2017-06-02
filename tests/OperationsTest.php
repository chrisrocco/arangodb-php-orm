<?php
use vector\ArangoORM\DB\DB;

/**
 *  Corresponding Class to test YourClass class
 *
 *  For each class in your library, there should be a corresponding Unit-Test for it
 *  Unit-Tests should be as much as possible independent from other test going on.
 *
 * @author yourname
 *
 * Just check if the YourClass has no syntax error
 *
 * This is just a simple check to make sure your library has no syntax error. This helps you troubleshoot
 * any typo before you even use this library in a real project.
 *
 */
class OperationsTest extends BaseTest
{

    /**
     * @group operations
     */
    public function testInit()
    {
        $collection_schema = json_decode(file_get_contents(__DIR__ . '/data/collection_schema.json'), true);
        DB::buildFromSchema($collection_schema);
        $ch = DB::getCollectionHandler();

        $result = true;
        foreach ($collection_schema as $key => $value) {
            if (!$ch->has($key)) $result = false;
        }
        self::assertTrue($result);
    }


    /**
     * @group operations
     * @after testInit
     */
    /*public function testPopulate()
    {
        $document_schema = json_decode(file_get_contents(__DIR__ . '/data/document_schema.json'), true);
        DB::createDocuments($document_schema);

        // for each collection
        // get the number of defined documents
        // query all
        // make sure the collection has at least that many documents

        $result = true;
        foreach ($document_schema as $key => $value) {
            $doc_count = count($value);
            $cursor = DB::getAll($key);
            if (!($cursor->getCount() >= $doc_count)) {
                $result = false;
            }
            self::assertTrue($result);
        }
    }*/

    /**
     * @group operations
     */
    public function testTruncate()
    {
        DB::truncate();
    }

    /**
     * @group nuke
     */
    public function testNuke()
    {
        DB::nuke();
        $ch = DB::getCollectionHandler();
        $collections = $ch->getAllCollections(['excludeSystem' => true]);
        self::assertEquals(0, count($collections));
    }

}