<?php
use vector\ArangoORM\Models\Core\VertexModel;

class SchemaTest extends BaseTest {

    /**
     * @group schema
     */
    function testOptionalSchema(){
        $model = IDontHaveSchema::create(
            [
                "anything" => "any value"
            ]
        );

        $val = $model->get( "boob" );
    }

    /**
     * @group schema
     * @depends testOptionalSchema
     */
    function testRequiredSchemaPass(  ){
        $model = IHaveSchema::create([
            "objTest"  =>  IDontHaveSchema::create([]),
            "numTest"  =>  123,
            "strTest"  =>  "abc",
        ]);
    }

    /**
     * @group schema
     */
    function testRequiredSchemaFail(){
        $this->expectException( Exception::class );

        IHaveSchema::create([
            "numTest"   =>  "abc"
        ]);
    }

    /**
     * @group schema
     */
    function testLooseSchema(){
        $model = IHaveSchema::create([
            "numTest"       => "string",
            "strTest"       =>  123,
            "immaString"      => "asdf",
            "optionalPropertiesAllowed_butNotEnforced"  =>  "anything"
        ]);

        $model->get( "i might not exists but it's cool since the schema is loose" );
    }

}

class IHaveSchema extends VertexModel {
    static $collection = "iHaveSchema";
    static $schema = [
        "numTest"   =>  "number",
        "strTest"   =>  "string"
    ];
}

class IDontHaveSchema extends VertexModel {
    static $collection = "iDontHaveSchema";
}