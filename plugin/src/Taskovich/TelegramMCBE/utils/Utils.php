<?php

declare(strict_types=1);

namespace Taskovich\TelegramMCBE\utils;

use pocketmine\Server;
use pocketmine\player\Player;

class Utils {

	public static function onlineModeEnabled(): bool
	{
		return Server::getInstance()->getOnlineMode();
	}

	public static function getPlayerByXuid(string $xuid): ?Player
	{
		foreach (Server::getInstance()->getOnlinePlayers() as $player) {
			if ($player->getXuid() === $xuid) {
				return $player;
			}
		}

		return null;
	}

}