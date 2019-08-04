<?php

namespace Models;

use RedBeanPHP\R;

class Chat extends Base
{
    public function create ($peerId) {
        $chat = R::dispense('chats');
        $chat->peerId = $peerId;
        $chat->createDate = time();
        return R::store($chat);
    }

    public function exists ($peerId) {
        $chat = R::findOne('chats', 'where peer_id = ?', [$peerId]);
        return $chat;
    }
}
