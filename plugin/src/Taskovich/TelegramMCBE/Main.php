<?php

declare(strict_types=1);

namespace Taskovich\TelegramMCBE;

use Taskovich\TelegramMCBE\commands\LinkToVkCommand;
use Taskovich\TelegramMCBE\commands\SecretCommand;
use Taskovich\TelegramMCBE\managers\LinkedPlayersManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

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
		$this->getServer()->getCommandMap()->registerAll("TelegramMCBE", [
			new SecretCommand($this),
			new LinkCommand($this)
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