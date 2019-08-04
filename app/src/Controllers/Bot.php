<?php


namespace Controllers;


use Api\Vk;
use Exceptions\VkException;
use Models\Chat;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use \Exception;

class Bot extends Base
{
    protected $vk;
    protected $secret;
    protected $action;
    protected $chat;
    protected $text;
    protected $peerId;
    protected $fromId;
    protected $isChat;

    protected function isAdmin ($id) {
        $admins = explode(',', env('ADMINS', ''));
        return in_array($id, $admins);
    }

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
                break;
            case 'message_new':
                $this->action = $body['object']['action'];
                $this->text = $body['object']['text'];
                $this->peerId = $body['object']['peer_id'];
                $this->chat = new Chat();
                $this->fromId = $body['object']['from_id'];
                $this->isChat = $this->fromId != $this->peerId;
                if (preg_match("/^\/reg/i", $this->text) && $this->isChat) {
                    try {
                        $chats = $this->vk->getChat($this->peerId);
                        if ($chats['count']) {
                            $currentChat = $chats['items'][0];
                            if ($currentChat['chat_settings']['owner_id'] == $this->fromId) {
                                if (!$this->chat->exists($this->peerId)) {
                                    $this->chat->create($this->peerId);
                                    $this->vk->sendMessage([
                                        'peer_id' => $this->peerId,
                                        'message' => 'Конфа зарегистрирована'
                                    ]);
                                }
                                else {
                                    $this->vk->sendMessage([
                                        'peer_id' => $this->peerId,
                                        'message' => 'Конфа уже зарегистрирована'
                                    ]);
                                }
                            }
                            else {
                                $this->vk->sendMessage([
                                    'peer_id' => $this->peerId,
                                    'message' => 'Вы не администратор конфы'
                                ]);
                            }
                        }
                        else {
                            $this->vk->sendMessage([
                                'peer_id' => $this->peerId,
                                'message' => 'Назначьте бота админом'
                            ]);
                        }
                    } catch (VkException $e) {
                        $this->vk->sendMessage([
                            'peer_id' => $this->peerId,
                            'message' => $e
                        ]);
                    }
                }
                $phpMatch = preg_match_all("/^\/php (.*)/ims", $this->text, $matches);
                if ($phpMatch && $this->isAdmin($this->fromId)) {
                    try {
                        $result = @eval($matches[1][0]);
                    } catch (\Throwable $e) {
                        $result = $e;
                    }
                    $this->vk->sendMessage([
                        'peer_id' => $this->peerId,
                        'message' => "Результат:\n\n" . $result
                    ]);
                }
                $response->withStatus(200)->write('ok');
                break;
            default:
                $response->withStatus(200)->write('ok');
        }

        return $response;
    }
}
