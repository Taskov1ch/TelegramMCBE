from aiogram.types import ReplyKeyboardMarkup, KeyboardButtonб ReplyKeyboardRemove

main_keyboard = ReplyKeyboardMarkup(
	keyboard = [
		[
			KeyboardButton(text = "Информация о сессии")
		],
		[
			KeyboardButton(text = "Закрыть сессию")
		],
		[
			KeyboardButton(text = "Отвязать аккаунт")
		]
	],
	resize_keyboard = True
)

empty_keyboard = ReplyKeyboardRemove()