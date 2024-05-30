<?php

namespace supercrafter333\PlayedTime;

use DateInterval;
use DateTime;
use Exception;
use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\event\TranslationContainer;
use pocketmine\plugin\Plugin;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use function array_keys;
use function array_shift;
use function explode;
use function implode;
use function is_numeric;
use function str_replace;

class PlayedTimeCommand extends Command implements PluginIdentifiableCommand 
{

	/**
	 * @param string $name
	 * @param string $description
	 * @param string|null $usageMessage
	 * @param array $aliases
	 */
	public function __construct(string $name, string $description = "", ?string $usageMessage = null, array $aliases = [])
	{
		parent::__construct($name, $description, $usageMessage, $aliases);
		$this->setPermission("playedtime.cmd");
	}

	/**
	 * @return PlayedTimeLoader
	 */
	public function getPlugin(): Plugin
	{
		return PlayedTimeLoader::getInstance();
	}

	/**
	 * @param CommandSender|Player $s
	 * @param string $permission
	 * @return bool
	 */
	private function checkPermission($s, string $permission): bool
	{
		if (!$s->hasPermission($permission)) {
			$s->sendMessage(new TranslationContainer(TextFormat::RED . "%commands.generic.permission"));
			return false;
		}
		return true;
	}

	/**
	 * @param string $msgPrefix
	 * @param array|null $replace
	 * @param bool $allowNewLines
	 * @return string
	 */
	public function getMsg(string $msgPrefix, ?array $replace = null, bool $allowNewLines = true): string
	{
		$msgCfg = new Config($this->getPlugin()->getDataFolder() . "messages.yml", Config::YAML);

		if (!strpos($msgPrefix, ':')) $message = $msgCfg->get($msgPrefix);
		else {
			$newPref = explode(':', $msgPrefix);
			$message = $msgCfg->getNested($newPref[0] . "." . $newPref[1]);
		}
		if (!$message || $message === null) return "Message not found!";
		if ($replace !== null)
			foreach (array_keys($replace) as $key) {
				$message = str_replace($key, $replace[$key], $message);
			}

		return $allowNewLines ? str_replace("{line}", "\n", $message) : $message;
	}

	/**
	 * @param CommandSender $s
	 * @param string $label
	 * @param array $args
	 * @return void
	 * @throws Exception
	 */
	public function execute(CommandSender $s, $label, array $args): void
	{
		if (!$this->checkPermission($s, "playedtime.cmd")) return;

		if (!isset($args[0])) {
			$cmdArr = ["mytime", "time", "top"];
			$cmds = "";
			foreach ($cmdArr as $cmd) {
				$cmds .= "\n§6/playedtime " . $cmd . " " . ($this->getMsg($cmd . "-command:args") !== "Message not found!" ? $this->getMsg($cmd . "-command:args") : "") . " §r - §b" . $this->getMsg($cmd . "-command:description");
			}
			$s->sendMessage($this->getMsg("help-command:onSuccess", ["{commands}" => $cmds]));
			return;
		}

		$notAPlayerMsg = function() use ($s) {
			return $s->sendMessage($this->getMsg("only-in-game"));
		};

		$timeMsg = function(DateInterval $time) {
			return $this->getMsg("time-format", [
				"{y}" => $time->y,
				"{m}" => $time->m,
				"{d}" => $time->d,
				"{h}" => $time->h,
				"{i}" => $time->i,
				"{s}" => $time->s
			], false);
		};

		$subCmd = array_shift($args);
		switch ($subCmd) {
			case "help":
				$cmdArr = ["mytime", "time", "top"];
				$cmds = "";
				foreach ($cmdArr as $cmd) {
					$cmds .= "\n§6/playedtime " . $cmd . " " . ($this->getMsg($cmd . "-command:args") !== "Message not found!" ? $this->getMsg($cmd . "-command:args") : "") . " §r - §b" . $this->getMsg($cmd . "-command:description");
				}
				$s->sendMessage($this->getMsg("help-command:onSuccess", ["{commands}" => $cmds]));

				break;

			case "mytime":
			case "mine":
				if (!$s instanceof Player) {
					$notAPlayerMsg();
					return;
				}

				if (!$this->checkPermission($s, "playedtime.cmd.mytime")) return;

				if (($mt = $this->getPlugin()->getPlayedTimeManager()->getTotalTime($s)) === null) return;
				if (($mst = $this->getPlugin()->getPlayedTimeManager()->getSessionTime($s)) === null) return;
				$s->sendMessage($this->getMsg("mytime-command:onSuccess", ["{total_time}" => $timeMsg($mt), "{session_time}" => $timeMsg($mst)]));

				break;

			case "time":
				if (!$this->checkPermission($s, "playedtime.cmd.time")) return;

				if (!isset($args[0])) {
					if (!$s instanceof Player) {
						$notAPlayerMsg();
						return;
					}

					if (!$this->checkPermission($s, "playedtime.cmd.mytime")) return;

					if (($mt = $this->getPlugin()->getPlayedTimeManager()->getTotalTime($s)) === null) return;
					if (($mst = $this->getPlugin()->getPlayedTimeManager()->getSessionTime($s)) === null) return;
					$s->sendMessage($this->getMsg("mytime-command:onSuccess", ["{total_time}" => $timeMsg($mt), "{session_time}" => $timeMsg($mst)]));
					return;
				}

				$name = implode(" ", $args);
				if (($tPlayer = $this->getPlugin()->getServer()->getPlayer($name))) $name = $tPlayer->getName();

				if (!($totalTime = $this->getPlugin()->getPlayedTimeManager()->getTotalTime($name)) instanceof DateInterval) {
					$s->sendMessage($this->getMsg("time-command:onFail", ["{player}" => $name]));
					return;
				}

				if ($tPlayer instanceof Player) $s->sendMessage($this->getMsg("time-command:onSuccess2",
					["{player}" => $name, "{time}" => $timeMsg($this->getPlugin()->getPlayedTimeManager()->getSessionTime($tPlayer))]));

				$s->sendMessage($this->getMsg("time-command:onSuccess",
					["{player}" => $name, "{time}" => $timeMsg($this->getPlugin()->getPlayedTimeManager()->getTotalTime($name))]));

				break;

			case "top":
				if (!$this->checkPermission($s, "playedtime.cmd.top")) return;

				$top = null;
				$all = $this->getPlugin()->getPlayedTimeManager()->getConfig()->getAll();
				$allTimes = [];
				foreach ($all as $playerName => $intervalStr)
					if (($tt = PlayedTimeManager::getInstance()->getTotalTime($playerName)) instanceof DateInterval)
						$allTimes[$playerName] = (new DateTime('now'))->add($tt)->getTimestamp();

				PlayedTimeLoader::getInstance()->getServer()->getScheduler()->scheduleAsyncTask(
					new TopSortAsyncTask($s->getName(), $allTimes, (isset($args[0]) && is_numeric($args[0]) && $args[0] > 0 ? $args[0] : 1))
				);
				break;

			default:
				$cmdArr = ["mytime", "time", "top"];
				$cmds = "";
				foreach ($cmdArr as $cmd) {
					$cmds .= "\n§6/playedtime " . $cmd . " " . ($this->getMsg($cmd . "-command:args") !== "Message not found!" ? $this->getMsg($cmd . "-command:args") : "") . " §r - §b" . $this->getMsg($cmd . "-command:description");
				}
				$s->sendMessage($this->getMsg("help-command:onSuccess", ["{commands}" => $cmds]));

				break;
		}
	}
}