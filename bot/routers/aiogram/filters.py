from aiogram.filters import BaseFilter
from aiogram.types import Message
from managers.database_manager import LinkedPlayers, NotLinkedPlayers
from aiogram.enums import ChatType

linked_players = LinkedPlayers()
not_linked_players = NotLinkedPlayers()

class IsPrivateMessage(BaseFilter):
	async def __call__(self, message: Message) -> bool:
		return message.chat.type == ChatType.PRIVATE

class IsNotLinked(BaseFilter):
	async def __call__(self, message: Message) -> bool:
		return not await linked_players.is_linked_tg(message.from_user.id)

class Action(BaseFilter):
	actions = {
		"get_session_info": "Информация о сессии",
		"close_session": "Закрыть сессию",
		"unlink_account": "Отвязать аккаунт"
	};

	def __init__(self, action: str):
		self.action = action

	async def __call__(self, message: Message) -> bool:
		if not self.action in self.actions or self.actions[self.action] != message.text:
			return False
		return True