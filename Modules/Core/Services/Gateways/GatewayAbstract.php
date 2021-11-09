<?php namespace Modules\Core\Services\Gateways;

abstract class GatewayAbstract{

    public $gatewayId;
    public $gatewayResult = [];
    public $exceptions = [];
    public $sendData = [];
    protected $baseUrl;
    protected $endpoints = [];
    public $curlInfo;

    abstract public function getDefaultHeader();

    abstract public function loadEndpoints();

    public function requestHttp(GatewayCurlOptions $option)
    {
        $arrEndpoint = $this->getEndpoint($option);        
           
        $curl = curl_init($option->baseUrl??$this->baseUrl . $arrEndpoint['route']);

        $headers = $option->headers ?? $this->getDefaultHeader();

        curl_setopt($curl, CURLOPT_ENCODING, '');
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $arrEndpoint['method']);

        if (!is_null($option->data)) {

            curl_setopt($curl, CURLOPT_POSTFIELDS, $option->getData());
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        if (!is_null($option->certificate)) {
            curl_setopt($curl, CURLOPT_SSLCERT, $option->certificate);
            curl_setopt($curl, CURLOPT_SSLCERTPASSWD, "");
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($curl);
        
        $this->curlInfo = curl_getinfo($curl);
        
        curl_close($curl);
        
        return $result;
    }

    /**
     * @param GatewayCurlOptions $option
     * endpoint,variables,queryString
     * @return mixed
     */
    public function getEndpoint(GatewayCurlOptions $option)
    {       
        $tempEndpoint = $this->endpoints[$option->endpoint] ?? null; 
        
        if (!empty($tempEndpoint))
        {            
            if (!empty($option->variables))
            {                
                $route = $tempEndpoint['route'];
                preg_match_all('/\:(\w+)/im', $route, $matches);
                $varsRoute = $matches[1];
                $i = 0;
                foreach ($varsRoute as $value) {
                    if (isset($option->variables[$i])) {
                        $route = str_replace(':' . $value, $option->variables[$i], $route);                        
                    }
                    $i++;
                }
                $tempEndpoint['route'] = $route;
            }

            if (!empty($option->queryString)) {
                $route = $tempEndpoint['route'];

                $queryString = http_build_query($option->queryString, '=>');

                $tempEndpoint['route'] = $route . '?' . $queryString;
            }

            return $tempEndpoint;
        }
        return null;
    }

    public function getBaseUrl(){
        return $this->baseUrl;
    }
}