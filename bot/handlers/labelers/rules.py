from vkbottle.bot import Message
from vkbottle.dispatch.rules import ABCRule
from managers.database_manager import LinkedPlayers, NotLinkedPlayers

linked_players = LinkedPlayers()
not_linked_players = NotLinkedPlayers()

class IsNotLinked(ABCRule[Message]):
	async def check(self, message: Message) -> bool:
		return not await linked_players.is_linked_vk(message.from_id)

class Action(ABCRule[Message]):
	actions = {
		"get_session_info": "Информация о сессии",
		"close_session": "Закрыть сессию",
		"unlink_account": "Отвязать аккаунт"
	};

	def __init__(self, action: str):
		self.action = action

	async def check(self, message: Message) -> bool:
		if not self.action in self.actions or self.actions[self.action] != message.text:
			return False
		return True