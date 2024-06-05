<?php

declare(strict_types=1);

namespace Taskovich\TelegramMCBE\Managers;

use Taskovich\TelegramMCBE\Main;
use Taskovich\TelegramMCBE\utils\Requests;

class LinkedPlayersManager
{

	private array $linked_players = [];

	public function __construct(private readonly Main $main) {}

	public function load(): bool
	{
		$config = $this->main->getConfig()->getAll();
		$logger = $this->main->getLogger();
		$logger->info("Getting all data ...");
		$response = Requests::get(
			$config["bot_endpoint"] . "/tgmc/linked_players",
			[],
			["Authorization: Bearer " . password_hash($config["bot_secret"], PASSWORD_BCRYPT)]
		);

		if (!$response)
		{
			$logger->error("Failed to retrieve data from the bot side...");
			return false;
		}

		$this->linked_players = json_decode($response, true)["linked_players"];
		$logger->info("Data successfully received and saved!");
		return true;
	}

	public function isLinked(string $id): bool
	{
		return isset($this->linked_players[$id]);
	}

	public function getVkId(string $id): ?int
	{
		return $this->linked_players[$id] ?? null;
	}

	public function addLink(string $id, int $vk_id): void
	{
		$this->linked_players[$id] = $vk_id;
	}

	public function removeLink(string $id): void
	{
		unset($this->linked_players[$id]);
	}

	public function getAll(): array
	{
		return $this->linked_players;
	}
}