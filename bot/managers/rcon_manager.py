from aiomcrcon import Client
from base64 import b64encode
from dotenv import load_dotenv
from json import loads, dumps
from utils import (Config, bcrypt_hash as hash)
from os import getenv
from typing import Optional

load_dotenv()

async def send_cmd(command: str) -> Optional[str]:
	try:
		config = Config("main").content
		async with Client(config.server_host, config.server_port, getenv("rcon_pass")) as rcon:
			answer = await rcon.send_cmd(command)
		return loads(answer[0].replace("\r", ""))
	except Exception:
		return None

async def response(action: str, player_id: str = "", tg_id: int = 0) -> Optional[dict]:
	try:
		command = {
			"method": action,
			"player": player_id,
			"tg_id": tg_id,
			"secret": hash(getenv("server_secret")).decode()
		}
		answer = await send_cmd("/connector " + b64encode(dumps(command).encode()).decode())

		return answer
	except Exception:
		return None