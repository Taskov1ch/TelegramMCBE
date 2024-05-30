<?php

declare(strict_types=1);

namespace Taskovich\VkConnector\commands;

use Taskovich\VkConnector\Main;
use pocketmine\command\Command as PmCommand;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;

abstract class Command extends PmCommand implements PluginIdentifiableCommand {

	private $main;

	public function __construct(
		Main $main,
		string $command,
		string $description,
		string $permission
	) {
		$this->main = $main;
		parent::__construct($command);
		$this->setDescription($description);
		$this->setPermission($permission);
	}

	public function getPlugin(): Main {
		return $this->main;
	}

	public function execute(CommandSender $sender, $label, array $args): bool {
		return $this->do($sender, $args);
	}

	public abstract function do(CommandSender $sender, array $args): bool;

}