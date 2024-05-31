<?php

declare(strict_types=1);

namespace Taskovich\VkConnector\Managers;

use Taskovich\VkConnector\Main;
use Taskovich\VkConnector\utils\Requests;

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
			$logger->error("Не удалось получить данные со стороны бота...");
			return false;
		}

		$this->linked_players = json_decode($response, true)["linked_players"];
		$logger->info("Данные успешно получены и сохранены!");
		return true;
	}

	public function isLinked(string $id): bool {
		return isset($this->linked_players[$id]);
	}

	public function getVkId(string $id): ?int {
		return $this->linked_players[$id] ?? null;
	}

	public function addLink(string $id, int $vk_id): void {
		$this->linked_players[$id] = $vk_id;
	}

	public function removeLink(string $id): void {
		unset($this->linked_players[$id]);
	}

	public function getAll(): array
	{
		return $this->linked_players;
	}
}