<?php 
declare(strict_types=1);

//require_once('src/autoload.php');

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use PHPUnit\Framework\TestCase;

final class ApiTest extends TestCase
{
    public function test_node_id_only_number(): void
    {
        $client = new GuzzleHttp\Client();
        try {      
            $client->request('GET', 'localhost/nestedset/api.php?node_id=dd&language=italian', []);
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $this->assertEquals(400, $response->getStatusCode());
        } 
    }
 
    public function test_node_not_empty(): void
    {
        $client = new GuzzleHttp\Client();
        try {      
            $client->request('GET', 'localhost/nestedset/api.php?node_id=&language=italian', []);
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $this->assertEquals(400, $response->getStatusCode());
        } 
    }

    public function test_node_id_required(): void
    {
        $client = new GuzzleHttp\Client();
        try {      
            $client->request('GET', 'localhost/nestedset/api.php?language=italian', []);
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $this->assertEquals(400, $response->getStatusCode());
        }
    }

    public function test_language_only_italian_english(): void
    {
        $client = new GuzzleHttp\Client();
        try {      
            $client->request('GET', 'localhost/nestedset/api.php?node_id=5&language=asdf', []);
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $this->assertEquals(400, $response->getStatusCode());
        } 
    }

    public function test_language_not_empty(): void
    {
        $client = new GuzzleHttp\Client();
        try {      
            $client->request('GET', 'localhost/nestedset/api.php?node_id=dd&language=', []);
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $this->assertEquals(400, $response->getStatusCode());
        } 
    }

    public function test_language_required(): void
    {
        $client = new GuzzleHttp\Client();
        try {      
            $client->request('GET', 'localhost/nestedset/api.php?node_id=5', []);
        } catch (GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $this->assertEquals(400, $response->getStatusCode());
        }
    }

}