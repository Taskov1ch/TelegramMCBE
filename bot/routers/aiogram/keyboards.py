from aiogram.types import ReplyKeyboardMarkup, KeyboardButton, ReplyKeyboardRemove
from managers.configs_manager import get_config

main_keyboard = ReplyKeyboardMarkup(
	keyboard = [
		[
			KeyboardButton(text = get_config("keyboards")["session_info"])
		],
		[
			KeyboardButton(text = get_config("keyboards")["close_session"])
		],
		[
			KeyboardButton(text = get_config("keyboards")["unlink_account"])
		]
	],
	resize_keyboard = True
)

empty_keyboard = ReplyKeyboardRemove()