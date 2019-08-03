<?php


namespace Controllers;


use Api\Vk;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Bot extends Base
{
    protected $vk;
    protected $secret;

    public function __invoke(Request $request, Response $response)
    {
        $this->vk = new Vk(env('VK_TOKEN'), env('VK_API_VERSION'));
        $this->secret = env('SECRET_KEY');

        $body = $request->getParsedBody();
        switch ($body['type']) {
            case 'confirmation':
                if ($body['secret'] != $this->secret) {
                    $response->withStatus(200)->write('ok');
                }
                else {
                    $response->withStatus(200)->write(env('CONFIRM'));
                }
        }

        return $response;
    }
}
