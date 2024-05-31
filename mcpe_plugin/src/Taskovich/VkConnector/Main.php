<?php

namespace Taskovich\VkConnector;

use pocketmine\plugin\PluginBase;
use Taskovich\VkConnector\commands\SecretCommand;
use Taskovich\VkConnector\commands\LinkToVkCommand;
use Taskovich\VkConnector\managers\LinkedPlayersManager;

class Main extends PluginBase {

	private $linked_players;
	private static $instance;

	public static function getInstance(): Main
	{
		return self::$instance;
	}

	public function onLoad()
	{
		self::$instance = $this;
	}

	public function onEnable(): void {
		$this->getServer()->getCommandMap()->registerAll("VkConnector", [
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