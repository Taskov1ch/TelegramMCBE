<?php

namespace Taskovich\VkConnector\Managers;

use Taskovich\VkConnector\Main;
use Taskovich\VkConnector\utils\Requests;

class LinkedPlayersManager {

	private $linked_players = [];
	private $main;

	public function __construct(Main $main) {
		$this->main = $main;
	}

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
			$logger->error("Не удалось получить данные со стороны бота...");
			return false;
		}

		$this->linked_players = json_decode($response, true)["linked_players"];
		$logger->info("Данные успешно получены и сохранены!");
		return true;
	}

	public function isLinked(string $nick): bool {
		return isset($this->linked_players[$nick]);
	}

	public function getVkId(string $nick): ?int {
		return $this->linked_players[$nick] ?? null;
	}

	public function addLink(string $nick, int $vk_id): void {
		$this->linked_players[$nick] = $vk_id;
	}

	public function removeLink(string $nick): void {
		unset($this->linked_players[$nick]);
	}

	public function getAll(): array
	{
		return $this->linked_players;
	}
}