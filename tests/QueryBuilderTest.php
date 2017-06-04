<?php
use vector\ArangoORM\DB\DB;
use vector\ArangoORM\DB\QueryBuilder\QueryBuilder;
use vector\ArangoORM\Models\Core\VertexModel;

class QueryBuilderTest extends BaseTest {

    /**
     * @group queryBuilder
     */
    function testBuildQuery(){

        $subquery = new QueryBuilder();
        $subquery->forDoc("subdoc")->inOutbound( "model", [ Test::getCollectionName() ] )->returnObj( "subdoc" );

        $query = new QueryBuilder();
        $query->forDoc( "model")
            ->inOutbound( "@id", [ "one", "two" ] )
            ->let( "subquery", $subquery )
            ->filter("model.attr == test")
            ->returnObj("model");

        echo $query->getAQL();
    }

}

class Test extends VertexModel {}