<?php
/**
 * Created by PhpStorm.
 * User: chris
 * Date: 6/4/2017
 * Time: 1:45 AM
 */

namespace vector\ArangoORM\DB\QueryBuilder;


use vector\ArangoORM\Models\Core\BaseModel;

class QueryBuilder
{
    private $AQL;

    /**
     * @return string
     */
    public function getAQL()
    {
        return $this->AQL;
    }

    private function appendAQL( $aql ){
        $this->AQL = $this->AQL . $aql;
    }

    function __construct( )
    {
        $this->AQL = "";
    }

    /*---------------------------*/
    /*----- builder methods -----*/
    /*---------------------------*/

    function forDoc( $var_name ){
        $this->appendAQL( "FOR $var_name " );
        return $this;
    }

    /**
     * @param $node_id
     * @param $collections_array array
     * @return $this
     */
    function inOutbound( $node_id, $collections_array ){
        $AQL = "IN OUTBOUND $node_id " . implode( ", ", $collections_array ) . " ";
        $this->appendAQL( $AQL );
        return $this;
    }

    /**
     * @param $node_id
     * @param $collections_array array
     * @return $this
     */
    function inInbound( $node_id, $collections_array ){
        $AQL = "IN INBOUND $node_id " . implode( ", ", $collections_array ) . " ";
        $this->appendAQL( $AQL );
        return $this;
    }

    /**
     * @param $node_id
     * @param $collections_array array
     * @return $this
     */
    function inAny( $node_id, $collections_array ){
        $AQL = "IN ANY $node_id " . implode( ", ", $collections_array ) . " ";
        $this->appendAQL( $AQL );
        return $this;
    }

    /**
     * @param $var_nam
     * @param $query_builder QueryBuilder
     * @return QueryBuilder
     */
    function let( $var_nam, $query_builder ){
        $aql = $query_builder->getAQL();
        $this->appendAQL("LET $var_nam = ( $aql ) ");
        return $this;
    }

    function filter( $filterStatement ){
        $this->appendAQL( "FILTER $filterStatement " );
        return $this;
    }

    function returnObj( $block ){
        $this->appendAQL("RETURN $block ");
        return $this;
    }
}