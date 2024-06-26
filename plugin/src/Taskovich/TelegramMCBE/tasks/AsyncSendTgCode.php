<?php

declare(strict_types=1);

namespace Taskovich\TelegramMCBE\tasks;

use Taskovich\TelegramMCBE\Main;
use Taskovich\TelegramMCBE\utils\Requests;
use pocketmine\Server;
use pocketmine\scheduler\AsyncTask;

class AsyncSendTgCode extends AsyncTask
{

	public function __construct(
		private readonly string $endpoint,
		private readonly string $secret,
		private readonly string $player_nick,
		private readonly string $id
	) {}

	public function onRun(): void
	{
		$params = ["id" => $this->id];
		$response = Requests::get(
			$this->endpoint . "/tgmc/try_link",
			["id" => $this->id],
			["Authorization: Bearer " . password_hash($this->secret, PASSWORD_BCRYPT)]
		);

		if (!$response)
		{
			return;
		}

		$this->setResult(json_decode($response, true));
	}

	public function onCompletion(): void
	{
		$player = Server::getInstance()->getPlayerExact($this->player_nick);

		if (!$player) {
			return;
		}

		$messages = Main::getInstance()->getConfig()->get("messages")["tg_code"];
		$results = $this->getResult();

		if (!$results)
		{
			$player->sendMessage($messages["error"]);
		} else if ($results["status"] === "linked")
		{
			$player->sendMessage($messages["linked"]);
		} else
		{
			$player->sendMessage(str_replace("{code}", strval($results["link_code"]), $messages["success"]));
		}
	}

}