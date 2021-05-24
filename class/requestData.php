<?php

class requestData
{
    private $idNode;
    private $language;
    private $searchKeyword;
    private $pageNum;
    private $pageSize;
    private $responseObj;

    /**
     * configData constructor.
     */
    public function __construct(array $params, responseClass $responseObj)
    {
        $this->responseObj = $responseObj;
        try {
            $this->validateParams($params);
            $this->setParams($params);
        } catch (requestException $e) {
            throw new requestException($e->getMessage());
        }
    }

    /** metodo usato per validare i parametri forniti tramite GET
     * @param array $params
     * @return bool
     */
    private function validateParams(array $params)
    {
        $mandatoryParams = array('node_id', 'language');

        /*
         * verifico se qualche parametro required è mancante (node_id e language)
         */
        foreach ($mandatoryParams as $mandatory) {
            if (!array_key_exists($mandatory, $params)) {
                throw new requestException($this->getErrorMessage('missing_params'));
            }
        }

        /*
         * node_id non può essere vuoto
         */
        if ((array_key_exists('node_id', $params) && $params['node_id'] == '') || !is_numeric($params['node_id'])) {
            throw new requestException($this->getErrorMessage('invalid_node_id'));
        }

        /*
         * language deve essere o italian o english
         */
        $allowedLanguages = array('italian', 'english');
        if (!in_array($params['language'], $allowedLanguages)) {
            throw new requestException($this->getErrorMessage('invalid_language'));
        }

        /*
         * page_num può solo essere un numero, uso una regex per verificarlo
         */
        if (array_key_exists('page_num', $params) && $params['page_num'] != '' &&
            !preg_replace( '/[^0-9]/', '', $params['page_num'])) {
            throw new requestException($this->getErrorMessage('invalid_page_num'));
        }

        /*
         * page_size può solo essere un numero, uso una regex per verificarlo
         */
        if (array_key_exists('page_size', $params) && $params['page_size'] != '' &&
            !preg_replace( '/[^0-9]/', '', $params['page_size'])) {
            throw new requestException($this->getErrorMessage('invalid_page_size'));
        }

        return true;
    }

    /** Metodo usato per settare le proprietà che serviranno per cercare i nodi e filtrarli
     * @param array $params
     */
    public function setParams(array $params) {
        $this->idNode           = $params['node_id'];
        $this->language         = $params['language'];
        $this->searchKeyword    = "";
        
        if (array_key_exists('search_keyword', $params) && $params['search_keyword']) {
            $this->searchKeyword = $params['search_keyword'];
        }

        /*
         * valore di default
         */
        $this->pageNum          = 0;
        if (array_key_exists('page_num', $params) && $params['page_num'] != "" && preg_replace( '/[^0-9]/', '', $params['page_num'])) {
            $this->pageNum      = intval($params['page_num']);
        }

        /*
         * valore di default
         */
        $this->pageSize         = 100;
        if (array_key_exists('page_size', $params) && $params['page_size'] != "" && preg_replace( '/[^0-9]/', '', $params['page_size'])) {
            $this->pageSize     = intval($params['page_size']);
        }
    }

    /**
     * @param string $errorName
     */
    public function getErrorMessage(string $errorName) {
        $errorDictionary = array(
            'invalid_node_id'       => 'Invalid node id',
            'missing_params'        => 'Missing mandatory params',
            'invalid_page_num'      => 'Invalid page number request',
            'invalid_page_size'     => 'Invalid page size requested',
            'database_error'        => 'Database connection error',
            'invalid_language'      => 'Invalid language string'
        );
        return $errorDictionary[$errorName];
    }

    /**
     * @return mixed
     */
    public function getSearchKeyword()
    {
        return $this->searchKeyword;
    }

    /**
     * @return mixed
     */
    public function getPageNum()
    {
        return $this->pageNum;
    }

    /**
     * @return mixed
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * @return mixed
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return mixed
     */
    public function getIdNode()
    {
        return $this->idNode;
    }
}