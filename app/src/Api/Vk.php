<?php


namespace Api;


use Exceptions\VkException;

class Vk extends Base
{
    public $version;
    public $endpoint;
    public $chatPeerIdMask = 2000000000;

    public function __construct ($token, $version)
    {
        $this->token = $token;
        $this->version = $version;
        $this->endpoint = 'https://api.vk.com/method/';
    }

    public function call ($method, $params = []) {
        if (!isset($params['access_token'])) {
            $params['access_token'] = $this->token;
        }
        $params['v'] = $this->version;
        $params['token'] = $this->token;
        $query = $params;
        $url = $this->endpoint . $method;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
        $json = curl_exec($curl);
        $error = curl_error($curl);
        if ($error) {
            throw new VkException("Failed {$method} request", 100500);
        }
        curl_close($curl);
        $response = json_decode($json, true);
        if (!isset($response['response'])) {
            throw new VkException($response['error']['error_msg'], $response['error']['error_code']);
        }
        return $response['response'];
    }

    public function getChat ($peerId, $fields = '')
    {
        return $this->call('messages.getConversationsById', [
            'peer_ids' => $peerId,
            'fields' => $fields
        ]);
    }

    public function sendMessage($params) {
        $params['random_id'] = rand(1, 1000000000);
        return $this->call('messages.send', $params);
    }
}
