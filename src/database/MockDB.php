<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 4/29/17
 * Time: 1:39 PM
 */

namespace rocco\ArangoORM\DB;

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
}