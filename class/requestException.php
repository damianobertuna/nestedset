<?php

class requestException extends Exception {
    public function errorMessage() {
        return $this->getMessage();
    }
}