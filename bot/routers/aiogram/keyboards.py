from aiogram.types import ReplyKeyboardMarkup, KeyboardButton, ReplyKeyboardRemove
from utils import Config

keyboards = Config("keyboards").content
main_keyboard = ReplyKeyboardMarkup(
	keyboard = [
		[
			KeyboardButton(text = keyboards.session_info)
		],
		[
			KeyboardButton(text = keyboards.close_session)
		],
		[
			KeyboardButton(text = keyboards.unlink_account)
		]
	],
	resize_keyboard = True
)

empty_keyboard = ReplyKeyboardRemove()