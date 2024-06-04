from aiosqlite import connect, Connection
from datetime import datetime, timedelta
from random import choice
from string import ascii_letters, digits
from typing import Optional

class Database:
	async def get_connection(self) -> Connection:
		conn = await connect("database.db")
		async with conn.cursor() as c:
			await c.execute("""
				CREATE TABLE IF NOT EXISTS linked (
					player_id TEXT, 
					tg_id TEXT
				)
			""")
			await c.execute("""
				CREATE TABLE IF NOT EXISTS codes (
					player_id TEXT, 
					code TEXT,
					timestamp TEXT
				)
			""")
			await conn.commit()
		return conn

	async def is_linked_player(self, player_id: str) -> bool:
		conn = await self.get_connection()
		async with conn.cursor() as c:
			await c.execute("SELECT * FROM linked WHERE player_id = ?", (player_id,))
			row = await c.fetchone()
		await conn.close()
		return row is not None

	async def is_linked_tg(self, tg_id: int) -> bool:
		conn = await self.get_connection()
		async with conn.cursor() as c:
			await c.execute("SELECT * FROM linked WHERE tg_id = ?", (tg_id,))
			row = await c.fetchone()
		await conn.close()
		return row is not None

class LinkedPlayers(Database):

	async def unlink(self, player_id: str) -> None:
		conn = await self.get_connection()
		async with conn.cursor() as c:
			await c.execute("DELETE FROM linked WHERE player_id = ?", (player_id,))
			await conn.commit()
		await conn.close()

	async def get_tg_id(self, player_id: str) -> Optional[str]:
		conn = await self.get_connection()
		async with conn.cursor() as c:
			await c.execute("SELECT tg_id FROM linked WHERE player_id = ?", (player_id,))
			row = await c.fetchone()
		await conn.close()
		return row[0] if row else None

	async def get_player_id(self, tg_id: str) -> Optional[str]:
		conn = await self.get_connection()
		async with conn.cursor() as c:
			await c.execute("SELECT player_id FROM linked WHERE tg_id = ?", (tg_id,))
			row = await c.fetchone()
		await conn.close()
		return row[0] if row else None

	async def get_all_players(self) -> list:
		conn = await self.get_connection()
		async with conn.cursor() as c:
			await c.execute("SELECT player_id, tg_id FROM linked")
			rows = await c.fetchall()
		await conn.close()
		return rows

class NotLinkedPlayers(Database):

	def expired(self, timestamp: str) -> bool:
		expiration_time = timedelta(minutes = 30)
		timestamp = datetime.fromisoformat(timestamp)
		return datetime.utcnow() - timestamp > expiration_time

	async def get_code(self, player_id: str) -> str:
		conn = await self.get_connection()
		async with conn.cursor() as c:
			await c.execute("SELECT code, timestamp FROM codes WHERE player_id = ?", (player_id,))
			row = await c.fetchone()

			if row:
				code, timestamp = row
				if not self.expired(timestamp):
					return code
				else:
					await c.execute("DELETE FROM codes WHERE player_id = ?", (player_id,))
					await conn.commit()

			while True: # Вероятность того, что свободный код не будет найден равен 1 к триллиарду
				new_code = "".join(choice(ascii_letters + digits) for _ in range(8))
				await c.execute("SELECT code, timestamp FROM codes WHERE code = ?", (new_code,))
				existing_code_row = await c.fetchone()

				if not existing_code_row:
					break

				existing_code, existing_timestamp = existing_code_row
				if self.expired(existing_timestamp):
					await c.execute("DELETE FROM codes WHERE code = ?", (new_code,))
					await conn.commit()
					break

			await c.execute(
				"INSERT INTO codes (player_id, code, timestamp) VALUES (?, ?, ?)",
				(player_id, new_code, datetime.utcnow().isoformat())
			)
			await conn.commit()
		await conn.close()
		return new_code

	async def get_player_id(self, code: str) -> Optional[str]:
		conn = await self.get_connection()
		async with conn.cursor() as c:
			await c.execute("SELECT player_id, timestamp FROM codes WHERE code = ?", (code,))
			row = await c.fetchone()
			if row:
				player_id, timestamp = row
				if not self.expired(timestamp):
					return player_id
				else:
					await c.execute("DELETE FROM codes WHERE code = ?", (code,))
					await conn.commit()
		await conn.close()
		return None

	async def link(self, player_id: str, tg_id: str, code: str) -> bool:
		conn = await self.get_connection()
		async with conn.cursor() as c:
			await c.execute("SELECT code, timestamp FROM codes WHERE player_id = ?", (player_id,))
			row = await c.fetchone()
			if not row or code != row[0] or self.expired(row[1]):
				return False

			await c.execute("INSERT INTO linked (player_id, tg_id) VALUES (?, ?)", (player_id, tg_id))
			await c.execute("DELETE FROM codes WHERE player_id = ?", (player_id,))
			await conn.commit()
		await conn.close()
		return True
