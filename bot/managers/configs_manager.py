from os import getcwd
from typing import Union
from yaml import safe_load

path = getcwd() + "/configs"

def get_config(file_name: str) -> Union[dict, list, None]:
	try:
		with open(f"{path}/{file_name}.yml", "r", encoding = "utf-8") as file:
			return safe_load(file)
	except Exception:
		return None