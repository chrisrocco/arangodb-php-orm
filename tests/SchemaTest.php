<?php
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\String_;
use vector\ArangoORM\DB\DB;
use vector\ArangoORM\Models\Core\VertexModel;

class SchemaTest extends BaseTest {

    function testOptionalSchema(){
        IDontHaveSchema::create(
            [
                "anything" => "any value"
            ]
        );
    }

    /**
     * @depends testOptionalSchema
     */
    function testRequiredSchemaPass(  ){
        $model = IHaveSchema::create([
            "objTest"  =>  IDontHaveSchema::create([]),
            "numTest"  =>  123,
            "strTest"  =>  "abc",
        ]);
    }

    function testRequiredSchemaFail(){
        $this->expectException( Exception::class );

        IHaveSchema::create([
            "numTest"   =>  "abc"
        ]);
    }

}

class IHaveSchema extends VertexModel {
    static $collection = "iHaveSchema";
    static $schema = [
        "objTest"      => IDontHaveSchema::class,
        "numTest"   =>  "number",
        "strTest"   =>  "string"
    ];
}

class IDontHaveSchema extends VertexModel {
    static $collection = "iDontHaveSchema";
}