from loguru import logger
from sys import stderr

logger.remove()
logger.add(stderr, level = "INFO")

def run_fastapi() -> None:
	from fastapi import FastAPI
	from managers.configs_manager import config
	from routers.fastapi import default_rt
	from uvicorn import run
	app = FastAPI()
	app.include_router(default_rt)
	host = config("main")
	run(app, host = host["host"], port = host["port"], access_log = False)

def run_aiogram() -> None:
	pass
	
def run() -> None:
	from concurrent.futures import ProcessPoolExecutor
	with ProcessPoolExecutor() as executor:
		executor.submit(run_aiogram)
		executor.submit(run_fastapi)

if __name__ == "__main__":
	run()
