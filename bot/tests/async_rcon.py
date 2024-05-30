from aiomcrcon import Client
from asyncio import run

async def main():
	async with Client("127.0.0.1", 19132, "mypass") as client:
		while True:
			command = input("> ")

			if command.lower() == "stop":
				break

			response = await client.send_cmd(command)
			print(response[0].replace("\r", ""))

if __name__ == "__main__":
	run(main())
