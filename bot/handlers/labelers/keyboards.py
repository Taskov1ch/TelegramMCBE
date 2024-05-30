from vkbottle import Keyboard, KeyboardButtonColor, Text

main_keyboard = (
	Keyboard()
	.add(Text("Информация о сессии"), color = KeyboardButtonColor.PRIMARY)
	.row()
	.add(Text("Закрыть сессию"), color = KeyboardButtonColor.NEGATIVE)
	.row()
	.add(Text("Отвязать аккаунт"), color = KeyboardButtonColor.SECONDARY)
).get_json()

empty_keyboard = Keyboard().get_json()