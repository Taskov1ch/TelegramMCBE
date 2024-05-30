<?php

declare(strict_types=1);

namespace Taskovich\VkConnector\tasks;

use Taskovich\VkConnector\utils\SkinUtils;
use pocketmine\scheduler\AsyncTask;

class AsyncSavePlayerSkin extends AsyncTask {

	public function __construct(
		private readonly string $skin_data,
		private readonly string $save_path
	) {}

	public function onRun(): void {
		$color_map = json_encode(SkinUtils::getColorMap($this->skin_data));
		file_put_contents($this->save_path, $color_map);
	}

}