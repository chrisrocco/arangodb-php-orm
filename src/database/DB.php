<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 4/29/17
 * Time: 1:39 PM
 */

namespace vector\ArangoORM\DB;

use vector\ArangoORM\Models\Core\BaseModel;
use triagens\ArangoDb\CollectionHandler;
use triagens\ArangoDb\Connection;
use triagens\ArangoDb\Document;
use triagens\ArangoDb\DocumentHandler;
use triagens\ArangoDb\EdgeHandler;
use triagens\ArangoDb\Statement;

/**
 * Class DB
 * @package DB
 *
 * -----------------------
 * --------- API ---------
 * -----------------------
 *
 * Properties
 * [_] -> connection                            Statically properties get set the first time they are requested
 * [_] -> document_handler
 * [_] -> collection_handler
 * [_] -> edge_handler
 *
 * CRUD Operations
 * [_] create( collection, document )
 * [_] retrieve( collection, _key )
 * [_] update( document )
 * [_] delete( document )
 * [_] createEdge( collection, from_id, to_id, document )
 *
 * Query Methods
 * [_] query( AQL-string, bind-variables, flat-option )
 * [_] queryModel( AQL-string, bind-variables, model-class )                            // Wraps the resulting documents in a model class
 * [_] getAll( collection )
 * [_] getByExample( {} )
 */
class DB
{
    /*----------------------------------------------------*/
    /*----------------------- CRUD -----------------------*/
    /*----------------------------------------------------*/
    public static function create( $col, $doc){
        $dh = self::getDocumentHandler();
        return $dh->save( $col, $doc, [
                'createCollection'  =>  'true'
            ]);
    }
    public static function retrieve( $col, $_key ){
        $dh = self::getDocumentHandler();

        if(!$dh->has( $col, $_key)) return false;

        return $dh->get( $col, $_key );
    }
    public static function update( $doc ){
        $dh = self::getDocumentHandler();
        $dh->replace( $doc );
    }
    public static function delete( $doc ){
        $dh = self::getDocumentHandler();
        $dh->remove( $doc );
    }
    public static function createEdge( $col, $from, $to, $doc){
        $eh = self::getEdgeHandler();
        return $eh->saveEdge( $col, $from, $to, $doc, [
            'createCollection'  =>  true
        ]);
    }

    /*----------------------------------------------------*/
    /*----------------------- Query -----------------------*/
    /*----------------------------------------------------*/
    /**
     * @param string $query_string
     * @param array $bindVars
     * @param bool $flat
     * @return \triagens\ArangoDb\Cursor
     */
    public static function query($query_string, $bindVars = [], $flat = true){
        $connection = self::getConnection();
        $statement = new Statement(
            $connection, [
                'query' => $query_string,
                'bindVars'  => $bindVars,
                '_flat' => $flat
            ]
        );
        return $statement->execute();
    }
    public static function queryFirst($query_string, $bindVars = [], $flat = true){
        return self::query( $query_string, $bindVars, $flat )->getAll()[0];
    }

    /**
     * @param $query_string
     * @param array $bindVars
     * @param $modelClass BaseModel::class The class of the model type
     * @return BaseModel[]
     */
    public static function queryModel($query_string, $bindVars = [], $modelClass){
        $cursor = self::query($query_string, $bindVars, false);
        $model = new $modelClass;
        return $model::wrapAll($cursor);
    }
    public static function queryFirstModel($query_string, $bindVars = [], $modelClass){
        return self::queryModel( $query_string, $bindVars, $modelClass )[0];
    }
    public static function getAll( $col ){
        $ch = self::getCollectionHandler();
        return $ch->all( $col );
    }
    public static function getByExample( $col, $example ){
        $ch = self::getCollectionHandler();
        return $ch->byExample( $col, $example);
    }

    /*----------------------------------------------------*/
    /*--------------------- Accessors -----------------------*/
    /*----------------------------------------------------*/
    static $connection;
    private static $document_handler;
    private static $edge_handler;
    private static $collection_handler;

    /**
     * @return DocumentHandler
     */
    static function getDocumentHandler(){
        if(self::$document_handler){
            return self::$document_handler;
        }

        $dh = new DocumentHandler(self::getConnection());
        self::$document_handler = $dh;

        return self::getDocumentHandler();
    }

    /**
     * @return EdgeHandler
     */
    static function getEdgeHandler(){
        if(self::$edge_handler){
            return self::$edge_handler;
        }

        $eh = new EdgeHandler(self::getConnection());
        self::$edge_handler = $eh;

        return self::getEdgeHandler();
    }

    /**
     * @return CollectionHandler
     */
    static function getCollectionHandler(){
        if(self::$collection_handler){
            return self::$collection_handler;
        }

        $ch = new CollectionHandler(self::getConnection());
        self::$collection_handler = $ch;

        return self::getCollectionHandler();
    }

    /**
     * @return Connection
     * @throws \Exception
     */
    static function getConnection(){
        if(self::$connection){
            return self::$connection;
        }

        if( !isset( self::$connection_settings )){
            throw new \Exception( "No database connection specified!" );
        }

        $connection = new Connection( self::$connection_settings );
        self::$connection = $connection;

        return self::getConnection();
    }

    /**
     * @var $connection_settings mixed The same connection settings you use for ArangoDB
     */
    private static $connection_settings;
    static function connect( $settings ){
        self::$connection_settings = $settings;
    }

    static function buildFromSchema( $collection_schema ){
        $ch = self::getCollectionHandler();
        foreach ($collection_schema as $name => $details){
            if($ch->has($name)) continue;

            /* Type is Required */
            $type = $details['type'];
            $type_code = 0;
            if($type === 'edge') $type_code = 3;
            if($type === 'vertex') $type_code = 2;

            $ch->create($name, [ 'type' => $type_code ]);
            print "created $type collection: $name \n";

            /* Indexes are optional */
            if( !isset( $details['indexes'])) continue;

            $indexes = $details['indexes'];
            foreach ($indexes as $index) {
                /* Indexes must have a type */
                switch ($index['type']) {
                    case self::GEO :
                        $geoJson = null;
                        $constraint = null;
                        $ignoreNull = null;
                        if (isset($index['options']['geoJson'])) {
                            $geoJson = json_decode($index['options']['geoJson']);
                        }
                        if (isset($index['options']['constraint'])) {
                            $constraint = json_decode($index['options']['constraint']);
                        }
                        if (isset($index['options']['ignoreNull'])) {
                            $ignoreNull = json_decode($index['options']['ignoreNull']);
                        }
                        $ch->createGeoIndex( $name, $index['fields'], $geoJson, $constraint, $ignoreNull );
                        break;
                    case self::HASH :
                        $unique = null;
                        $sparse = null;
                        if (isset($index['options']['unique'])) {
                            $unique = json_decode($index['options']['unique']);
                        }
                        if (isset($index['options']['sparse'])) {
                            $sparse = json_decode($index['options']['sparse']);
                        }
                        $ch->createHashIndex( $name, $index['fields'], $unique, $sparse);
                        break;
                    case self::SKIP_LIST :
                        $unique = null;
                        $sparse = null;
                        if (isset($index['options']['unique'])) {
                            $unique = json_decode($index['options']['unique']);
                        }
                        if (isset($index['options']['sparse'])) {
                            $sparse = json_decode($index['options']['sparse']);
                        }
                        $ch->createSkipListIndex( $name, $index['fields'], $unique, $sparse);
                        break;
                    case self::FULL_TEXT :
                        $minLength = null;
                        if (isset($index['options']['minLength'])) {
                            $minLength = json_decode($index['options']['minLength']);
                        }
                        $ch->createFullTextIndex( $name, $index['fields'], $minLength);
                        break;
                    default :
                        break;
                }
            }
        }
    }

    static function createCollectionIfNotExsists( $collection_name ){
        $ch = self::getCollectionHandler();
        if(!$ch->has( $collection_name )) $ch->create( $collection_name );
    }
    static function dropCollection( $collection_name ){
        $ch = self::getCollectionHandler();
        if($ch->has( $collection_name )) $ch->drop( $collection_name );
    }

    static function createDocuments( $document_schema ){
        $dh = self::getDocumentHandler();

        foreach ( $document_schema as $collection_name => $document_set ){
            $ch = self::getCollectionHandler();
            $ch->truncate( $collection_name );
            foreach ( $document_set as $document ){
                $d = Document::createFromArray( $document );
                $dh->store( $d, $collection_name );

                print "populated $collection_name " . PHP_EOL;
            }
        }
    }
    static function nuke(  ){
        $ch = self::getCollectionHandler();
        $collections = $ch->getAllCollections([ 'excludeSystem' => true ]);
        foreach ($collections as $name => $type){

            print 'droppping ' . $name . "\n";
            $ch->drop( $name );
        }
    }
    static function truncate(  ){
        $ch = self::getCollectionHandler();
        $collections = $ch->getAllCollections([ 'excludeSystem' => true ]);
        foreach ($collections as $name => $type){

            print 'truncating ' . $name . "\n";
            $ch->truncate( $name );
        }
    }

    const GEO = "geo";
    const SKIP_LIST = "skipList";
    const FULL_TEXT = "fullText";
    const HASH = "hash";
}