<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 4/29/17
 * Time: 11:54 PM
 */

namespace vector\ArangoORM\Models\Core;

use vector\ArangoORM\DB\DB;
use triagens\ArangoDb\Edge;

/**
 * Class EdgeModel
 * @package Models\Core
 *
 * The only difference from the base model are the special _to and _from properties.
 * After creation, the Edge is modeled as a regular document ( Edge's super class ). Can still be updated.
 */
class EdgeModel extends BaseModel
{

    /**
     * Creates a new record in the database, wraps it in a model, and returns it.
     * @param $to       VertexModel
     * @param $from     VertexModel
     * @param $data     array   PHP array of object attributes
     * @return mixed
     */
    public static function create( $to, $from, $data = []) {
        self::addMetaData( $data );

        $edge_doc = Edge::createFromArray( $data );
        $key = DB::createEdge( static::getCollectionName(), $from->id(), $to->id(), $edge_doc );
        $doc = DB::retrieve( static::getCollectionName(), $key );
//        $edge_doc->setInternalKey($key);
        return static::wrap( $doc );
    }

    public function setTo( $to ){
        $this->update('_to', $to);
    }
    public function setFrom( $from ){
        $this->update('_from', $from);
    }
    public function getTo(  ){
        return $this->get( '_to' );
    }
    public function getFrom(  ){
        return $this->get( '_from' );
    }
}