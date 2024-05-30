from bcrypt import checkpw
from fastapi import Depends, FastAPI, HTTPException
from fastapi.security import OAuth2PasswordBearer
from uvicorn import run

app = FastAPI()
oauth2_scheme = OAuth2PasswordBearer(tokenUrl = "login")

async def verify_token(token: str = Depends(oauth2_scheme)):
	try:
		if not checkpw("Hello, World!".encode(), token.encode()):
			raise HTTPException(status_code = 401, detail = "Invalid token")
	except Exception:
		raise HTTPException(status_code = 401, detail = "Invalid token")
	return token

@app.get("/protected_endpoint", dependencies = [Depends(verify_token)])
async def protected_endpoint() ->dict:
	return {"message": "Welcome to the protected endpoint!"}

run(app, host = "127.0.0.1", port = 8000, access_log = False)
