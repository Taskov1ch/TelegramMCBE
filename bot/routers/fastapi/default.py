from .rules import verify_secret
from dotenv import load_dotenv
from fastapi import APIRouter, Depends
from managers.database_manager import LinkedPlayers, NotLinkedPlayers
from os import getenv

load_dotenv()
rt = APIRouter()
linked_players = LinkedPlayers()
not_linked_players = NotLinkedPlayers()

@rt.get("/tgmc/try_link", dependencies = [Depends(verify_secret)])
async def try_link(id: str) -> dict:
	id = id.lower()
	
	if await linked_players.is_linked_player(id.lower()):
		return {"status": "linked"}

	return {
		"status": "not_linked",
		"link_code": await not_linked_players.get_code(id)
	}

@rt.get("/tgmc/linked_players", dependencies = [Depends(verify_secret)])
async def get_linked_players() -> dict:
	return {
		"status": "success",
		"linked_players": {data[0]: data[1] for data in await linked_players.get_all_players()}
	}

# curl -X GET "http://localhost:8000/tgmc/linked_players" -H "accept: application/json" -H "Authorization: Bearer $2a$12$8DMPnX./VDEhyeIAplo75O0q6maF0bpXUo/eziXvIS73VWbzAIWc."
