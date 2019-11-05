<?php

namespace App\Telegram;

use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
/**
 * Class HelpCommand.
 */
class TestCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = 'test';

    /**
     * @var string Command Description
     */
    protected $description = 'Test command, Get a list of commands';

    /**
     * {@inheritdoc}
     */
    public function handle()
    {
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $this->replyWithMessage(['text' => 'ASDASD ASD ASD ASD ']);

        $telegram_user = \Telegram::geWebhookUpdates()['message'];
        $text = sprintf('%s: %s'.PHP_EOL, 'Ваш номер чата', $telegram_user['from']['id']);
        $text .= sprintf('%s: %s'.PHP_EOL, 'Ваш имя пользователя в телеграм', $telegram_user['from']['username']);

        $this->replyWithMessage(compact('text'));

    }
}
