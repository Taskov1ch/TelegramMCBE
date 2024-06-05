<?php

declare(strict_types=1);

namespace Taskovich\TelegramMCBE\commands;

use Taskovich\TelegramMCBE\Main;
use Taskovich\TelegramMCBE\tasks\AsyncSendVkCode;
use Taskovich\TelegramMCBE\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class LinkCommand extends Command
{

	private const COMMAND = "tg";
	private const DESCRIPTION = "Link your account to Telegram";
	private const PERMISSION = "tgmc.link";

	public function __construct(Main $main)
	{
		parent::__construct($main, self::COMMAND, self::DESCRIPTION, self::PERMISSION);
	}

	public function do(CommandSender $sender, array $args): bool
	{
		if (!$sender instanceof Player)
		{
			return false;
		}

		$config = $this->getPlugin()->getConfig();
		$sender->sendMessage($config->get("messages")["vk_code"]["processing"]);
		$this->getPlugin()->getServer()->getAsyncPool()->submitTask(new AsyncSendVkCode(
			$config->get("bot_endpoint"),
			$config->get("bot_secret"),
			strtolower($sender->getName()),
			Utils::onlineModeEnabled() ? $sender->getXuid() : strtolower($sender->getName())
		));
		return true;
	}

}