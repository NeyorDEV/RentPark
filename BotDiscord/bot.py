import discord
from discord.ext import commands
import matplotlib.pyplot as plt
import io
import os
from dotenv import load_dotenv
import requests


load_dotenv()  # charge le contenu du .env
GITLAB_TOKEN = os.getenv("GITLAB_TOKEN")

print("Token chargé :", bool(GITLAB_TOKEN))  # test : doit afficher True

# --- Remplace par ton token personnel (à garder secret !) ---
TOKEN = "MTQyNzU0Mzk3NDcwNDQ0NzQ5OA.GyOEHP.NGMy0sODYyFvsTMIEb0MJTqI28TIzO7Vj2zeMc"

# --- Configuration du bot ---
intents = discord.Intents.default()
intents.message_content = True  # Nécessaire pour lire le contenu des messages
bot = commands.Bot(command_prefix="!", intents=intents)

# --- Événement de démarrage ---
@bot.event
async def on_ready():
    print(f"Connecté en tant que {bot.user}")

# --- Commande !graph ---
@bot.command()

async def graph(ctx):
    # Exemple : générer un graphique simple
    x = [1, 2, 3, 4, 5]
    y = [1, 4, 9, 16, 25]

    plt.figure()
    plt.plot(x, y, marker='o')
    plt.title("Exemple de graphique")
    plt.xlabel("X")
    plt.ylabel("Y")

    # Enregistrer le graphique dans un flux mémoire
    buffer = io.BytesIO()
    plt.savefig(buffer, format='png')
    buffer.seek(0)
    plt.close()

    # Envoyer l'image sur Discord
    await ctx.send(file=discord.File(buffer, filename="graph.png"))

# --- Commande !commit (test) ---
@bot.command()
async def commits(ctx):
    await ctx.send("🔍 Récupération des commits...")

    url = "https://codefirst.iut.uca.fr/gitlab/api/v4/projects/4459/repository/commits?ref_name=main"

    headers = {"PRIVATE-TOKEN": GITLAB_TOKEN}

    response = requests.get(url, headers=headers)
    print("Code retour GitLab :", response.status_code)

    if response.status_code == 200:
        data = response.json()
        msg = "**Derniers commits :**\n"
        for commit in data[:5]:
            msg += f"- {commit['title']} ({commit['author_name']})\n"
        await ctx.send(msg)
    else:
        await ctx.send(f"Erreur {response.status_code} lors de la récupération des commits.")

# --- Lancer le bot ---
print("Commandes chargées :", bot.all_commands.keys())
bot.run(TOKEN)
