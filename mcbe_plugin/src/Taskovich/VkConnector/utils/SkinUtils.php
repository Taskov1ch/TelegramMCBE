<?php

declare(strict_types=1);

namespace Taskovich\VkConnector\utils;

use Taskovich\VkConnector\Main;

class SkinUtils {

	const RESOLUTIONS = [
		64 * 32 * 4 => [32, 64],
		64 * 64 * 4 => [64, 64],
		128 * 64 * 4 => [64, 128],
		128 * 128 * 4 => [128, 128]
	];

	public static function getResolution(int $skin_len): ?array {
		return self::RESOLUTIONS[$skin_len] ?? null;
	}

	public static function getColorMap(string $skin_data): ?array {
		$resolution = self::getResolution(strlen($skin_data));

		if (!$resolution) {
			return null;
		}

		$color_map = [];
		$index = 0;

		for ($y = 0; $y < $resolution[0]; $y++) {
			for ($x = 0; $x < $resolution[1]; $x++) {
				$list = substr($skin_data, $index, 4);
				$color_map[] = [
					ord($list[0]),
					ord($list[1]),
					ord($list[2]),
					ord($list[3])
				];
				$index += 4;
			}
		}

		return $color_map;
	}

}