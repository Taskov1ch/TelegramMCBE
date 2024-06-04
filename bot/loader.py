from loguru import logger
from sys import stderr

logger.remove()
logger.add(stderr, level = "INFO")

def run_fastapi() -> None:
	from fastapi import FastAPI
	from handlers.routers import default_rt
	from managers.configs_manager import config
	from uvicorn import run
	app = FastAPI()
	app.include_router(default_rt)
	host = config("main_config")
	run(app, host = host["host"], port = host["port"], access_log = False)

def run_vkbottle() -> None:
	from asyncio import run
	from dotenv import load_dotenv
	from handlers.labelers import default_lb
	from os import getenv
	from vkbottle import API
	from vkbottle.bot import Bot
	load_dotenv()
	bot = Bot(token = getenv("VK_TOKEN"))
	bot.labeler.load(default_lb)
	run(bot.run_polling())

def run() -> None:
	from concurrent.futures import ProcessPoolExecutor
	with ProcessPoolExecutor() as executor:
		executor.submit(run_vkbottle)
		executor.submit(run_fastapi)

if __name__ == "__main__":
	run()
