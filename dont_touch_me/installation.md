## Requirements 🔌
- A server with internet access (VPS or PC).
- A **Minecraft PE/BE** game server running on the **PocketMine-MP 5** core, also with internet access.
- **Python 3.10** or higher.

## Installation ⚡

#### Plugin Installation 📦
1. Download the archive with plugins for your game version [here](https://github.com/Taskov1ch/TelegramMC/releases).
2. Unpack the archive with plugins and install all the plugins from the archive on your game server.
3. Start and stop the server to create the configs.
4. Configure the plugin configs for your server.

#### Bot Installation 🤖
1. Download the archive with the bot [here](https://github.com/Taskov1ch/TelegramMC/releases).
2. Unpack the archive with the bot and move all the contents to the desired directory on your server.
3. Configure the configs in the **configs** directory for your server.
4. Create a `.env` file and fill it with [this](env_template.md) template.
5. Change the values of the elements for your server.

## Correct Launch 🚀

#### Bot 🤖
1. Install **Poetry** on your server.
2. Run the command `poetry install` in the bot directory to install all necessary dependencies.
3. Run the command `poetry run python loader.py`.

#### Game Server 🔑
Simply start your game server with the plugins installed, and that's it.
