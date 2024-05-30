from aiomcrcon import Client
from base64 import b64encode
from dotenv import load_dotenv
from json import loads, dumps
from managers.configs_manager import get_config
from managers.hash_manager import hash
from os import getenv
from typing import Optional

load_dotenv()

async def response(action: str, player_id: str = "") -> Optional[dict]:
	try:
		command = {
			"method": action,
			"player": player_id,
			"secret": hash(getenv("SERVER_SECRET")).decode()
		}
		async with Client(
			get_config("main_config")["server_host"],
			get_config("main_config")["server_port"],
			getenv("RCON_PASS")
		) as rcon:
			answer = await rcon.send_cmd(
				"/connector " + b64encode(dumps(command).encode()).decode()
			)
		
		return loads(answer[0].replace("\r", ""))
	except Exception as e:
		return None