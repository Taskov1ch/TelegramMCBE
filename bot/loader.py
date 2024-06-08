from loguru import logger

def run_fastapi() -> None:
	from contextlib import asynccontextmanager
	from fastapi import FastAPI
	from managers import Config
	from routers.fastapi import default_rt
	from uvicorn import run
	config = Config("main").content
	host, port = config.host, config.port

	@asynccontextmanager
	async def lifespan(app: FastAPI) -> None:
		logger.info(f"FastAPI is working. Endpoint: http://{host}:{port}")
		yield

	app = FastAPI(lifespan = lifespan)
	app.include_router(default_rt)
	run(app, host = host, port = port, access_log = False, log_config = None)

def run_aiogram() -> None:
	from aiogram import Bot, Dispatcher
	from asyncio import run
	from os import getenv
	from routers.aiogram import default_rt
	bot = Bot(token = getenv("bot_token"))
	dp = Dispatcher()
	dp.include_router(default_rt)

	async def on_startup() -> None:
		username = (await bot.get_me()).username
		logger.info(f"Telegram bot @{username} is working.")

	dp.startup.register(on_startup)

	async def async_run() -> None:
		await bot.delete_webhook(drop_pending_updates = True)
		await dp.start_polling(bot)

	run(async_run())

def run() -> None:
	from concurrent.futures import ProcessPoolExecutor
	with ProcessPoolExecutor() as executor:
		executor.submit(run_aiogram)
		executor.submit(run_fastapi)
	# run_aiogram()
	# run_fastapi()

if __name__ == "__main__":
	try:
		run()
	except KeyboardInterrupt:
		logger.info("Shutdown...")
