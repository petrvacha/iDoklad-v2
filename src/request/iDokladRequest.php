<?php
/**
 * Class represents entity for one api request. Collects data and then returns data to provide request.
 *
 * @author Jan Malcánek
 */

namespace petrvacha\iDoklad\request;

include_once __DIR__.'/iDokladFilter.php';
include_once __DIR__.'/iDokladSort.php';

use petrvacha\iDoklad\iDokladException;
use petrvacha\iDoklad\request\iDokladFilter;
use petrvacha\iDoklad\request\iDokladSort;

class iDokladRequest {
    
    /**
     * Holds method (IssuedInvoices)
     * @var string
     */
    private $method;
    
    /**
     * Holds post parameters
     * @var array
     */
    private $postParams = array();
    
    /**
     * Holds get parameters
     * @var array
     */
    private $getParams = array();
    
    /**
     * Holds methodType (e.g. GET, POST)
     * @var string
     */
    private $methodType = 'GET';
    
    /**
     * Holds filters
     * @var array
     */
    private $filters = array();
    
    /**
     * Holds sorts
     * @var array
     */
    private $sorts = array();
    
    /**
     * Holds request lang if set
     * @var string
     */
    private $lang = null;

    /**
     * Optionally initializes request parameters
     * @param string $method
     * @param string $methodType
     * @param array $getParameters
     * @param array $postParameters
     */
    public function __construct($method = null, $methodType = 'GET', $getParameters = array(), $postParameters = array()) {
        $this->method = $method;
        $this->methodType = $methodType;
        $this->getParams = $getParameters;
        $this->postParams = $postParameters;
    }
    
    /**
     * Sets api method (e.g. IssuedInvoices)
     * @param string $method
     * @return \petrvacha\iDoklad\request\iDokladRequest
     */
    public function setMethod($method){
        $this->method = $method;
        return $this;
    }
    
    /**
     * Sets api response language
     * @param string $lang
     * @return \petrvacha\iDoklad\request\iDokladRequest
     */
    public function setLang($lang){
        $this->lang = $lang;
        return $this;
    }

    /**
     * Adds request post parameters from array
     * @param array $params
     * @return \petrvacha\iDoklad\request\iDokladRequest
     */
    public function addPostParameters(array $params){
        $this->postParams = $params;
        return $this;
    }
    
    /**
     * Adds request post parameter by key and value
     * @param string $key
     * @param string $value
     * @return \petrvacha\iDoklad\request\iDokladRequest
     */
    public function addPostParameter($key, $value){
        $this->postParams[$key] = $value;
        return $this;
    }
    
    /**
     * Adds request get parameters
     * @param array $params
     * @return \petrvacha\iDoklad\request\iDokladRequest
     */
    public function addGetParameters(array $params){
        $this->getParams = $params;
        return $this;
    }
    
    /**
     * Adds reuqest get parameters by key and value
     * @param string $key
     * @param string $value
     * @return \petrvacha\iDoklad\request\iDokladRequest
     */
    public function addGetParameter($key, $value){
        $this->getParams[$key] = $value;
        return $this;
    }
    
    /**
     * Sets method type (e.g. GET, POST)
     * @param string $methodType
     * @return \petrvacha\iDoklad\request\iDokladRequest
     */
    public function addMethodType($methodType){
        $this->methodType = $methodType;
        return $this;
    }
    
    /**
     * Adds data filter
     * @param \petrvacha\iDoklad\request\iDokladFilter $filter
     * @return \petrvacha\iDoklad\request\iDokladRequest
     */
    public function addFilter(iDokladFilter $filter){
        $this->filters[] = $filter;
        return $this;
    }
    
    /**
     * Adds data sort
     * @param \petrvacha\iDoklad\request\iDokladSort $sort
     * @return \petrvacha\iDoklad\request\iDokladRequest
     */
    public function addSort(iDokladSort $sort){
        $this->sorts[] = $sort;
        return $this;
    }
    
    /**
     * Declares page from pagination
     * @param int $page
     * @return \petrvacha\iDoklad\request\iDokladRequest
     */
    public function setPage($page){
        $this->getParams['page'] = $page;
        return $this;
    }
    
    /**
     * Declares number of returned items by request
     * @param int $pageSize
     * @return \petrvacha\iDoklad\request\iDokladRequest
     */
    public function setPageSize($pageSize){
        $this->getParams['pagesize'] = $pageSize;
        return $this;
    }
    
    /**
     * Sets filter type allowed and and or
     * @param string $type
     * @return \petrvacha\iDoklad\request\iDokladRequest
     * @throws iDokladException
     */
    public function setFilterType($type){
        if($type != 'and' && $type != 'or'){
            throw new iDokladException('Filter type must be \'and\' or \'or\'');
        }
        $this->getParams['filtertype'] = $type;
        return $this;
    }

    /**
     * Builds http query from get parameters. Auto adds filters and sorts to get query
     * @return string
     * @throws iDokladException
     */
    public function buildGetQuery(){
        $filterString = array();
        foreach($this->filters as $filter){
            $filterString[] = $filter->buildQuery();
        }
        if(!empty($filterString)){
            $this->addGetParameter("filter", implode('|', $filterString));
        }
        $sortString = array();
        foreach($this->sorts as $sort){
            $sortString[] = $sort->buildQuery();
        }
        if(!empty($sortString)){
            $this->addGetParameter("sort", implode('|', $sortString));
        }
        if(is_array($this->getParams)){
            return http_build_query($this->getParams);
        } else {
            throw new iDokladException('Get parameters have to be array');
        }
    }
    
    /**
     * Builds http query from post parameters.
     * @return string
     * @throws iDokladException
     */
    public function buildPostQuery(){
        if(is_array($this->postParams)){
            return json_encode($this->postParams);
        } else {
            throw new iDokladException('Post parameters have to be array');
        }
    }
    
    /**
     * Returns setted method to request.
     * @return string
     */
    public function getMethod(){
        return trim($this->method, '/');
    }
    
    /**
     * Returns setted method type.
     * @return string
     */
    public function getMethodType(){
        return $this->methodType;
    }
    
    /**
     * Returns setted lang.
     * @return string
     */
    public function getLang(){
        return $this->lang;
    }
    
    /**
     * Sets request method type as get
     * @return \petrvacha\iDoklad\request\iDokladRequest
     */
    public function get(){
        $this->methodType = 'GET';
        return $this;
    }
    
    /**
     * Sets request method type as post
     * @return \petrvacha\iDoklad\request\iDokladRequest
     */
    public function post(){
        $this->methodType = 'POST';
        return $this;
    }
    
    /**
     * Sets request method type as put
     * @return \petrvacha\iDoklad\request\iDokladRequest
     */
    public function put(){
        $this->methodType = 'PUT';
        return $this;
    }
    
    /**
     * Sets request method type as delete
     * @return \petrvacha\iDoklad\request\iDokladRequest
     */
    public function delete(){
        $this->methodType = 'DELETE';
        return $this;
    }
    
    /**
     * Sets request method type as patch
     * @return \petrvacha\iDoklad\request\iDokladRequest
     */
    public function patch(){
        $this->methodType = 'PATCH';
        return $this;
    }
    
    /**
     * Returns post params
     * @return array
     */
    public function getPostParams(){
        return $this->postParams;
    }
}
