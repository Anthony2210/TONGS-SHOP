import json
import string

def load_scoremap(file_path="scoremap.json"):
    with open(file_path, "r", encoding="utf-8") as f:
        scoremap = json.load(f)
    return scoremap

def classify_message(message, scoremap):
    # Prétraitement du message
    m = message.lower()
    m = m.translate(str.maketrans('', '', string.punctuation))
    words = m.split()

    # Calcul de la somme des scores
    total_score = 0.0
    for w in words:
        if w in scoremap:
            total_score += scoremap[w]

    # Décision
    if total_score > 0:
        return "noHate"
    else:
        return "hate"

# Exemple d'utilisation
if __name__ == "__main__":
    scoremap = load_scoremap()

    # Testez avec quelques messages
    msg1 = "I love all people"
    msg2 = "I hate these brown people"
    msg3 = "Hello the world"

    print("Message :", msg1)
    print("Classification :", classify_message(msg1, scoremap))

    print("Message :", msg2)
    print("Classification :", classify_message(msg2, scoremap))

    print("Message :", msg3)
    print("Classification :", classify_message(msg3, scoremap))

