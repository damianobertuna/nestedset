<?php

class requestException extends Exception {
    public function errorMessage() {
        return json_encode(array(
                'rootNodesNumber' => 0,
                'nodes' => array(),
                'totalPage' => 0,
                'currentPage' => 0,
                'error' => $this->getMessage()
            )
        ); 
    }
  }