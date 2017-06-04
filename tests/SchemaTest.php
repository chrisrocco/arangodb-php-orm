<?php
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\String_;
use vector\ArangoORM\DB\DB;
use vector\ArangoORM\Models\Core\VertexModel;

class SchemaTest extends BaseTest {

    function testOptionalSchema(){
        $model = IDontHaveSchema::create(
            [
                "anything" => "any value"
            ]
        );

        $val = $model->get( "boob" );
    }

    /**
     * @depends testOptionalSchema
     */
    function testRequiredSchemaPass(  ){
        $model = IHaveStrictSchema::create([
            "objTest"  =>  IDontHaveSchema::create([]),
            "numTest"  =>  123,
            "strTest"  =>  "abc",
        ]);
    }

    function testRequiredSchemaFail(){
        $this->expectException( Exception::class );

        IHaveStrictSchema::create([
            "numTest"   =>  "abc"
        ]);
    }

    function testLooseSchema(){
        $model = IHaveLooseSchema::create([
            "immaString"      => "asdf",
            "optionalPropertiesAllowed_butNotEnforced"  =>  "anything"
        ]);

        $model->get( "i might not exists but it's cool since the schema is loose" );
    }

    function testStrictSchema(){
        $this->expectException( Exception::class );

        $model = IHaveStrictSchema::create([
            "objTest"  =>  IDontHaveSchema::create([]),
            "numTest"  =>  123,
            "strTest"  =>  "abc",
        ]);

        $model->get( "i must exists because the schema is strict" );
    }

}

class IHaveStrictSchema extends VertexModel {
    static $collection = "iHaveSchema";
    static $schema = [
        "objTest"      => IDontHaveSchema::class,
        "numTest"   =>  "number",
        "strTest"   =>  "string"
    ];
}

class IHaveLooseSchema extends VertexModel {
    static $collection = "iHaveLooseSchema";
    static $strictSchema = false;
    static $schema = [
        "immaString" => "string"
    ];
}

class IDontHaveSchema extends VertexModel {
    static $collection = "iDontHaveSchema";
}