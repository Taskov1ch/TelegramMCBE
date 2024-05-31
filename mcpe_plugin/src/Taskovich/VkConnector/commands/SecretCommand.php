<?php

namespace Taskovich\VkConnector\commands;

use Taskovich\VkConnector\Main;
use pocketmine\Player;
use pocketmine\command\CommandSender;
use pocketmine\event\TranslationContainer;
use pocketmine\utils\TextFormat;
use supercrafter333\PlayedTime\PlayedTimeManager;

class SecretCommand extends Command { // Секретная команда специально для бота

	private const COMMAND = "/connector";
	private const DESCRIPTION = "";
	private const PERMISSION = "vkconnector.secret";

	public function __construct(Main $main) {
		parent::__construct($main, self::COMMAND, self::DESCRIPTION, self::PERMISSION);
		$this->secret = $main->getConfig()->get("server_secret");
	}

	public function responseFromBot(string $response): ?array {
		$response = json_decode(base64_decode($response), true);
		$secret = str_replace("\\", "", $response["secret"] ?? "");
		
		if ($response and password_verify($this->secret, $secret)) {
			return $response;
		}

		return null;
	}

	public function do(CommandSender $sender, array $args): bool {
		$args = $this->responseFromBot($args[0] ?? "");
		$messages = $this->getPlugin()->getConfig()->get("messages");

		if ((!$args) or $sender instanceof Player) {
			$sender->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.permission"));
			return false;
		}

		switch ($args["method"]) {
			case "get_session_info":
				$player = $this->getPlugin()->getServer()->getPlayerExact($args["player"]);

				if (!$player) {
					$sender->sendMessage(json_encode(["status" => "offline"]));
					return false;
				}

				$os_names = [
					"Unknown", "Android", "iOS", "macOS", "FireOS",
					"GearVR", "Hololens", "Windows10", "Windows7", "Dedicated",
					"TVOS", "PlayStation", "NintendoSwitch", "Xbox", "WindowsPhone"
				];
				$interval = PlayedTimeManager::getInstance()->getSessionTime($player);
				$total_hours = ($interval->y ?? 0 * 365 * 24) + ($interval->d ?? 0 * 24) + ($interval->h ?? 0);
				$answer = [
					"ip" => $player->getAddress(),
					"os" => $os_names[$player->getDeviceOS()] ?? "Unknown",
					"model" => $player->getDeviceModel(),
					"played" => [$total_hours, $interval->i ?? 0, $interval->s ?? 0]
				];
				$sender->sendMessage(json_encode($answer));
			break;

			case "close_session":
				$player = $this->getPlugin()->getServer()->getPlayerExact($args["player"]);

				if (!$player) {
					$sender->sendMessage(json_encode(["status" => "offline"]));
					return false;
				}

				$player->close("Kicked by VkConnector", $messages["close_session"]["kick_msg"]);
				$sender->sendMessage(json_encode(["status" => "success"]));
			break;

			case "new_link":
				$this->getPlugin()->getLinkedPlayersManager()->addLink(
					$args["player"], $args["vk_id"]);
				$sender->sendMessage(json_encode(["status" => "success"]));
			break;

			case "unlink":
				$this->getPlugin()->getLinkedPlayersManager()->removeLink($args["player"]);
				$sender->sendMessage(json_encode(["status" => "success"]));
			break;
		}

		return true;
	}

}