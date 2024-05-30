<?php

declare(strict_types=1);

namespace Taskovich\VkConnector\Managers;

use Taskovich\VkConnector\Main;
use Taskovich\VkConnector\utils\Requests;
use Taskovich\VkConnector\utils\Utils;
use pocketmine\player\Player;

class LinkedPlayersManager {

	private array $linked_players = [];

	public function __construct(private readonly Main $main) {}

	public function load(): bool {
		$config = $this->main->getConfig()->getAll();
		$logger = $this->main->getLogger();
		$logger->info("Получение данных...");
		$response = Requests::get(
			$config["bot_endpoint"] . "/vkconnector/linked_players",
			[],
			["Authorization: Bearer " . password_hash($config["bot_secret"], PASSWORD_BCRYPT)]
		);

		if (!$response) {
			$logger->error("Не удалось получить данные со стороны бота... Менеджер привязанных игроков будет не доступен.");
			return false;
		}

		$this->linked_players = json_decode($response, true)["linked_players"];
		$logger->info("Данные успешно получены и сохранены!");
		return true;
	}

	public function isLinked(Player $player): bool {
		$id = Utils::onlineModeEnabled() ? $player->getXuid() : strtolower($player->getName());
		return isset($this->linked_players[$id]);
	}

	public function getVkId(Player $player): ?int {
		$id = Utils::onlineModeEnabled() ? $player->getXuid() : strtolower($player->getName());
		return $this->linked_players[$id] ?? null;
	}
}