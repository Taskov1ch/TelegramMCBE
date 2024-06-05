from bcrypt import checkpw, hashpw, gensalt

def verify(password: str, hash: str) -> bool:
	try:
		return checkpw(password.encode(), hash.encode())
	except Exception:
		return False

def hash(password: str) -> str:
	return hashpw(password.encode(), gensalt())