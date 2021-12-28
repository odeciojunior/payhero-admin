<?php
namespace Modules\Core\Services\Gateways;

use Illuminate\Support\Str;
class GatewayCurlOptions{
    public $baseUrl;
    public $endpoint;
    public $data;
    public $headers;
    public $certificate;
    public $variables;
    public array $queryString;

    public function __construct($dados=null)
    {
        if(!empty($dados)){
            if(!empty($dados['baseUrl'])){
                $this->baseUrl = $dados['baseUrl'];
            }
            if(!empty($dados['endpoint'])){
                $this->endpoint = $dados['endpoint'];
            }
            if(!empty($dados['data'])){
                $this->data = $dados['data'];
            }
            if(!empty($dados['headers'])){
                $this->headers = $dados['headers'];
            }
            if(!empty($dados['certificate'])){
                $this->certificate = $dados['certificate'];
            }
            if(!empty($dados['variables'])){
                $this->variables = $dados['variables'];
            }
            if(!empty($dados['queryString'])){
                $this->queryString = $dados['queryString'];
            }
        }
    }

    public function getData(){
        if(is_array($this->data)){
            return json_encode($this->data);
        }
        return $this->data;
    }
}