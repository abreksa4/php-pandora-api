<?php

namespace php_pandora_api;


class PandoraHelper
{
    /**
     * @var Pandora
     */
    protected $_pandora;

    /**
     * PandoraHelper constructor.
     * @param Pandora $_pandora
     */
    public function __construct(Pandora $_pandora)
    {
        $this->_pandora = $_pandora;
    }

    /**
     * @param $pandora Pandora|null
     * @return PandoraHelper
     */
    public static function create($partner_name = 'android', $pandora = null)
    {
        if (is_null($pandora)) {
            $pandora = new Pandora($partner_name);
        }
        return new PandoraHelper($pandora);
    }

    /**
     * @return Pandora
     */
    public function getPandora()
    {
        return $this->_pandora;
    }

    public function getLastRequestData()
    {
        return [
            'error' => $this->_pandora->last_error,
            'request' => $this->_pandora->last_request_data,
            'response' => $this->_pandora->last_response_data
        ];
    }

    public function login($username, $password)
    {
        return $this->_pandora->login($username, $password);
    }

    public function getStations($options = [])
    {
        $response = $this->_pandora->makeRequest(Pandora::user_getStationList, $options);
        if ($response === false) {
            return false;
        }
        return $response['stations'];
    }

    public function getSongs($station_token, $options = [])
    {
        $response = $this->_pandora->makeRequest(Pandora::station_getPlaylist, array_merge([
            'stationToken' => $station_token
        ], $options));
        if ($response === false) {
            return false;
        }
        return $response['items'];
    }


}