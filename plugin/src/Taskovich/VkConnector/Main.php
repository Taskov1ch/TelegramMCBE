<?php

declare(strict_types=1);

namespace Taskovich\VkConnector;

use pocketmine\plugin\PluginBase;
use Taskovich\VkConnector\commands\SecretCommand;
use Taskovich\VkConnector\commands\LinkToVkCommand;
use pocketmine\utils\SingletonTrait;
use Taskovich\VkConnector\managers\LinkedPlayersManager;

class Main extends PluginBase
{
	use SingletonTrait;

	private ?LinkedPlayersManager $linked_players;

	public function onLoad(): void
	{
		self::setInstance($this);
	}

	public function onEnable(): void
	{
		$this->getServer()->getCommandMap()->registerAll("TelegramMC", [
			new SecretCommand($this),
			new LinkToVkCommand($this)
		]);
		$this->saveDefaultConfig();
		$this->linked_players = new LinkedPlayersManager($this);
		$this->linked_players->load();
	}

	public function getLinkedPlayersManager(): ?LinkedPlayersManager
	{
		return $this->linked_players;
	}
}