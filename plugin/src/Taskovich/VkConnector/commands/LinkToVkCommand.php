<?php

declare(strict_types=1);

namespace Taskovich\VkConnector\commands;

use Taskovich\VkConnector\Main;
use Taskovich\VkConnector\tasks\AsyncSendVkCode;
use Taskovich\VkConnector\utils\Utils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class LinkToVkCommand extends Command
{

	private const COMMAND = "vk";
	private const DESCRIPTION = "Привязать аккаунт к ВК";
	private const PERMISSION = "vkconnector.link";

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