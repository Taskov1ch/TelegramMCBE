from .keyboards import main_keyboard, empty_keyboard
from .rules import IsNotLinked, Action
from dotenv import load_dotenv
from managers import configs_manager, database_manager, rcon_manager
from os import getenv
from typing import Optional
from vkbottle.bot import BotLabeler, Message

load_dotenv()
lb = BotLabeler()
linked_players = database_manager.LinkedPlayers()
not_linked_players = database_manager.NotLinkedPlayers()
not_linked_messages = configs_manager.get_config("messages")["not_linked"]
linked_messages = configs_manager.get_config("messages")["linked"]

async def action(action: str, message: Message) -> Optional[dict]:
	data = await rcon_manager.response(
		action,
		await linked_players.get_player_id(message.from_id),
		message.from_id
	)

	if not data:
		await message.answer(linked_messages["server_error"])
	elif "status" in data and data["status"] == "offline":
		await message.answer(linked_messages["not_session"])
	else:
		return data

	return None

@lb.private_message(IsNotLinked())
async def try_link(message: Message) -> None:
	if len(message.text) == 0:
		return

	player_id = await not_linked_players.get_player_id(message.text)

	if not player_id or not await not_linked_players.link(
		player_id,
		message.from_id,
		message.text
	):
		await message.answer(not_linked_messages["code_not_found"], keyboard = empty_keyboard)
		return

	await message.answer(not_linked_messages["success_link"], keyboard = main_keyboard)
	await action("new_link", message)

@lb.private_message(Action("get_session_info"))
async def session_info(message: Message) -> None:
	data = await action("get_session_info", message)

	if not data:
		return

	played = data["played"];
	await message.answer(linked_messages["session_info"].format(
			ip = data["ip"],
			os = data["os"],
			device = data["model"],
			h = data["played"][0],
			m = data["played"][1],
			s = data["played"][2]
		)
	)


@lb.private_message(Action("close_session"))
async def close_session(message: Message) -> None:
	data = await action("close_session", message)

	if not data:
		return

	await message.answer(linked_messages["close_session"])

@lb.private_message(Action("unlink_account"))
async def unlink(message: Message) -> None:
	await action("unlink", message)
	await linked_players.unlink(await linked_players.get_player_id(message.from_id))
	await message.answer(linked_messages["unlink"], keyboard = empty_keyboard)

@lb.private_message()
async def unknown(message: Message) -> None:
	await message.answer(linked_messages["unknown"], keyboard = main_keyboard)