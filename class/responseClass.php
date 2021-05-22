<?php

/**
 * Class Response
 */
class ResponseClass
{
    private $rootNodesNumber;
    private $nodes;
    private $totalPage;
    private $currentPage;
    private $error;

    public function __construct(int $rootNodesNumber, array $nodes, int $totalPage, int $currentPage, string $error)
    {
        $this->rootNodesNumber  = $rootNodesNumber;
        $this->nodes            = $nodes;
        $this->totalPage        = $totalPage;
        $this->error            = $error;
        $this->currentPage      = $currentPage;
    }

    /**
     * @return int
     */
    public function getRootNodesNumber(): int
    {
        return $this->rootNodesNumber;
    }

    /**
     * @return array
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }

    /**
     * @param int $rootNodesNumber
     */
    public function setRootNodesNumber(int $rootNodesNumber)
    {
        $this->rootNodesNumber = $rootNodesNumber;
    }

    /**
     * @param array $nodes
     */
    public function setNodes(array $nodes)
    {
        $this->nodes = $nodes;
    }

    /**
     * @param int $totalPage
     */
    public function setTotalPage(int $totalPage)
    {
        $this->totalPage = $totalPage;
    }

    /**
     * @param int $currentPage
     */
    public function setCurrentPage(int $currentPage)
    {
        $this->currentPage = $currentPage;
    }

    /**
     * @param string $error
     */
    public function setError(string $error)
    {
        $this->error = $error;
    }

    public function getStructure() {
        return array(
            'rootNodesNumber' => $this->rootNodesNumber,
            'nodes' => $this->nodes,
            'totalPage' => $this->totalPage,
            'currentPage' => $this->currentPage,
            'error' => $this->error
        );
    }

    public function toJson($response)
    {
        return json_encode($response);
    }
}