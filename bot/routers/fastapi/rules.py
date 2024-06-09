from bcrypt import checkpw
from dotenv import load_dotenv
from fastapi import Depends, HTTPException
from fastapi.security import OAuth2PasswordBearer
from utils import bcrypt_verify as verify
from os import getenv

load_dotenv()
oauth2_scheme = OAuth2PasswordBearer(tokenUrl = "login")

async def verify_secret(secret: str = Depends(oauth2_scheme)) -> None:
	try:
		if not verify(getenv("bot_secret"), secret):
			raise HTTPException(status_code = 401, detail = "Invalid secret")
	except Exception:
		raise HTTPException(status_code = 401, detail = "Invalid secret")