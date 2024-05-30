<?php

namespace supercrafter333\PlayedTime;

use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;

class PlayedTimeLoader extends PluginBase
{

	private static $instance;

	public function onLoad()
	{
		self::$instance = $this;
	}

	public static function getInstance(): PlayedTimeLoader
	{
		return self::$instance;
	}

	public function onEnable()
	{
		$plugin_manager = $this->getServer()->getPluginManager();
		$plugin_manager->registerEvents(new EventListener(), $this);
		$this->saveResource("messages.yml");
		$this->getServer()->getCommandMap()->register("PlayedTime", new PlayedTimeCommand("playedtime", "PlayedTime commands.", null, ["pt"]));
	}

	public function onDisable()
	{
		$this->getPlayedTimeManager()->saveAll();
	}

	public function getPlayedTimeManager(): PlayedTimeManager
	{
		return new PlayedTimeManager;
	}
}