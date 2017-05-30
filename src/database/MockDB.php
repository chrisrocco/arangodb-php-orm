<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 4/29/17
 * Time: 1:39 PM
 */

namespace rocco\ArangoORM\DB;

use triagens\ArangoDb\Exception;

class MockDB
{
    static function generateMockData( $templates, $values, $how_many ){
        $output = [];
        for( $i = 0; $i < $how_many; $i++ ){
            $rand_key_t = array_rand( $templates, 1 );
            $rand_key_v = array_rand( $values, 1 );
            $t = $templates[ $rand_key_t ];
            $v = $values[ $rand_key_v ];
            $generated = str_replace( "%s", $v, $t );
            $output[] = $generated;
        }
        return $output;
    }

    static function mockDocuments( $document_template, $bind_templates, $how_many ){
        $mockData = [];
        foreach ( $bind_templates as $bind_char => $template ){
            $mockData[$bind_char] = self::generateMockData( $template['templates'], $template['values'], $how_many );
        }

        $output = [];
        for( $i = 0; $i < $how_many; $i++ ){

            $tmp = json_encode( $document_template );
            foreach ( $mockData as $bind_char => $values ){
                $tmp = str_replace( $bind_char, $values[$i], $tmp );
            }

            $output[] = json_decode( $tmp, true );
        }
        return $output;
    }

    static function oneToOne( $collection_from, $collection_to, $edge_collection ){
        // get contents of both collections
        // for each in from, make a {direction} edge to to ( if it exists )
        $from = DB::getAll( $collection_from )->getAll();
        $to = DB::getAll( $collection_to )->getAll();

        $eh = DB::getEdgeHandler();
        foreach ( $from as $index => $doc ){
            if( $index <= count( $to ) ){
                $eh->saveEdge( $edge_collection, $doc->getInternalId(), $to[$index]->getInternalId(), [], [
                    'createCollection'  =>  true
                ]);
            }
        }
    }

    // garuntees at least one edge from $collection_from
    static function oneToMany( $collection_one, $collection_toMany, $edge_collection, $edge_direction ){
        $max_edges = 5;
        $one = DB::getAll( $collection_one )->getAll();
        $toMany = DB::getAll( $collection_toMany )->getAll();
        // for each in collection_from
        // make {random} edges to random documents in collection_to
        $eh = DB::getEdgeHandler();
        foreach ( $toMany as $doc ){
            $randomOther = $one[rand(0, count($one)-1)];
            if( $edge_direction == "outbound" ){
                $eh->saveEdge( $edge_collection, $doc->getInternalId(), $randomOther->getInternalId(), [], [
                    'createCollection'  =>  true
                ]);
                continue;
            }
            if( $edge_direction == "inbound" ){
                $eh->saveEdge( $edge_collection, $randomOther->getInternalId(), $doc->getInternalId(), [], [
                    'createCollection'  =>  true
                ]);
                continue;
            }
            throw new Exception( "Invalid direction parameter. Must be either 'inbound' or 'outbound' ");
        }
    }

    // does not garuntee every vertex will have an edge
    static function ManyToMany( $collection_from, $collection_to, $edge_direction ){
        // for random document in collection from
        //  make random edges to random document in edge direction
    }
}