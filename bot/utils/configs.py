from os import getcwd
from pathlib import Path
from typing import Any
from yaml import safe_load

class Config:
	configs_path = Path(getcwd(), "configs")

	def __init__(self, file_name: str) -> None:
		self.path = Path(self.configs_path, file_name + ".yml")

		with open(self.path, "r", encoding = "utf-8") as file:
			self.content = self._get_object(safe_load(file))

	def _get_object(self, data: dict) -> Any:
		if not isinstance(data, dict):
			return data

		obj = type("Config", (), {})

		for key, value in data.items():
			setattr(obj, key, self._get_object(value))

		return obj