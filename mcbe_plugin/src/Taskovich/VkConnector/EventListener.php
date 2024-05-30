<?php

declare(strict_types=1);

namespace Taskovich\VkConnector;

use Taskovich\VkConnector\tasks\AsyncSavePlayerSkin;
use Taskovich\VkConnector\utils\Utils;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class EventListener implements Listener {

	public function __construct(private readonly Main $main) {}

	public function onJoin(PlayerJoinEvent $event): void {
		$player = $event->getPlayer();
		var_dump($player->getSkin()->getGeometryData());
		$id = Utils::onlineModeEnabled() ? $player->getXuid() : strtolower($player->getName());
		$this->main->getServer()->getAsyncPool()->submitTask(new AsyncSavePlayerSkin(
			$player->getSkin()->getSkinData(),
			$this->main->getDataFolder() . "skins/{$id}.json"
		));
	}

}