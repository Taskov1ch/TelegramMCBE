<?php

declare(strict_types=1);

namespace Taskovich\VkConnector\commands;

use Taskovich\VkConnector\Main;
use pocketmine\command\Command as PmCommand;
use pocketmine\command\CommandSender;

abstract class Command extends PmCommand {

	public function __construct(
		private readonly Main $main,
		string $command,
		string $description,
		string $permission
	) {
		parent::__construct($command);
		$this->setDescription($description);
		$this->setPermission($permission);
	}

	public function getPlugin(): Main {
		return $this->main;
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
		return $this->do($sender, $args);
	}

	public abstract function do(CommandSender $sender, array $args): bool;

}