from .keyboards import main_keyboard, empty_keyboard
from .filters import IsPrivateMessage, IsNotLinked, Action
from dotenv import load_dotenv
from managers import configs_manager, database_manager, rcon_manager
from os import getenv
from typing import Optional
from aiogram import Router, F
from aiogram.types import Message

load_dotenv()
rt = Router()
linked_players = database_manager.LinkedPlayers()
not_linked_players = database_manager.NotLinkedPlayers()
not_linked_messages = configs_manager.get_config("messages")["not_linked"]
linked_messages = configs_manager.get_config("messages")["linked"]

async def action(action: str, message: Message) -> Optional[dict]:
	player_id = await linked_players.get_player_id(message.from_user.id)
	data = await rcon_manager.response(action, player_id, message.from_user.id)

	if not data:
		await message.answer(linked_messages["server_error"])
	elif "status" in data and data["status"] == "offline":
		await message.answer(linked_messages["not_session"])
	else:
		return data

	return None

@rt.message(IsPrivateMessage(), IsNotLinked())
async def try_link(message: Message) -> None:
	if not(message.text) or len(message.text) == 0:
		return

	player_id = await not_linked_players.get_player_id(message.text)

	if not player_id or not await not_linked_players.link(player_id, message.from_user.id, message.text):
		await message.answer(not_linked_messages["code_not_found"], reply_markup = empty_keyboard)
		return

	await message.answer(not_linked_messages["success_link"], reply_markup = main_keyboard)
	await action("new_link", message)

@rt.message(IsPrivateMessage(), Action("get_session_info"))
async def session_info(message: Message) -> None:
	data = await action("get_session_info", message)

	if not data:
		return

	played = data["played"]
	await message.answer(linked_messages["session_info"].format(
			ip = data["ip"],
			os = data["os"],
			device = data["model"],
			h = played[0],
			m = played[1],
			s = played[2]
		)
	)

@rt.message(IsPrivateMessage(), Action("close_session"))
async def close_session(message: Message) -> None:
	data = await action("close_session", message)

	if not data:
		return

	await message.answer(linked_messages["close_session"])

@rt.message(IsPrivateMessage(), Action("unlink_account"))
async def unlink(message: Message) -> None:
	await action("unlink", message)
	await linked_players.unlink(await linked_players.get_player_id(message.from_user.id))
	await message.answer(linked_messages["unlink"], reply_markup = empty_keyboard)

@rt.message(IsPrivateMessage())
async def unknown(message: Message) -> None:
	await message.answer(linked_messages["unknown"], reply_markup = main_keyboard)
