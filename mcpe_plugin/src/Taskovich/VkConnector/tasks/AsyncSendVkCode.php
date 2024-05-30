<?php

declare(strict_types=1);

namespace Taskovich\VkConnector\tasks;

use Taskovich\VkConnector\Main;
use Taskovich\VkConnector\utils\Requests;
use pocketmine\Server;
use pocketmine\scheduler\AsyncTask;

class AsyncSendVkCode extends AsyncTask {

	private $endpoint;
	private $secret;
	private $nick;

	public function __construct(
		string $endpoint,
		string $secret,
		string $nick
	) {
		$this->endpoint = $endpoint;
		$this->secret = $secret;
		$this->nick = $nick;
	}

	public function onRun() {
		$response = Requests::get(
			$this->endpoint . "/vkconnector/try_link",
			["id" => $this->nick],
			["Authorization: Bearer " . password_hash($this->secret, PASSWORD_BCRYPT)]
		);

		if (!$response) {
			return;
		}

		$this->setResult(json_decode($response, true));
	}

	public function onCompletion(Server $server) {
		$player = $server->getPlayerExact($this->nick);

		if (!$player) {
			return;
		}

		$messages = Main::getInstance()->getConfig()->get("messages")["vk_code"];
		$results = $this->getResult();

		if (!$results) {
			$player->sendMessage($messages["error"]);
		} else if ($results["status"] === "linked") {
			$player->sendMessage(str_replace("{full_vk_name}", $results["full_name"], $messages["linked"]));
		} else {
			$player->sendMessage(str_replace("{code}", strval($results["link_code"]), $messages["success"]));
		}
	}

}