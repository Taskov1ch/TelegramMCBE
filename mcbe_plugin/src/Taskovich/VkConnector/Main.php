<?php

declare(strict_types=1);

namespace Taskovich\VkConnector;

use pocketmine\plugin\PluginBase;
use Taskovich\VkConnector\commands\SecretCommand;
use Taskovich\VkConnector\commands\LinkToVkCommand;
use pocketmine\utils\SingletonTrait;
use Taskovich\VkConnector\utils\Requests;
use Taskovich\VkConnector\utils\Utils;
use pocketmine\player\Player;
use Taskovich\VkConnector\managers\LinkedPlayersManager;

class Main extends PluginBase {
	use SingletonTrait;

	private ?LinkedPlayersManager $linked_players;

	public function onLoad(): void {
		self::setInstance($this);
	}

	public function onEnable(): void {
		$this->getServer()->getCommandMap()->registerAll("VkConnector", [
			new SecretCommand($this),
			new LinkToVkCommand($this)
		]);
		@mkdir($this->getDataFolder() . "skins");
		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
		$this->saveDefaultConfig();
		$this->linked_players = new LinkedPlayersManager($this);

		if (!$this->linked_players->load()) {
			// $this->getServer()->getPluginManager()->disablePlugin($this);
			$this->linked_players = null;
			return;
		}
	}

	public function getLinkedPlayersManager(): ?LinkedPlayersManager
	{
		return $this->linked_players;
	}
}