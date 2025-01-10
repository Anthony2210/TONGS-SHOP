import json
from etape2 import*

texts, labels = charger_textes_labels()
idf = calculIDF()
# Initialiser le ScoreMap à 0 pour tous les mots
# Une approche simple : initialiser uniquement quand on en a besoin.
ScoreMap = {}

for i, text in enumerate(texts):
    label = labels[i]  # "hate" ou "noHate"

    # Pré-traitement du texte
    t = text.lower()
    t = t.translate(str.maketrans('', '', string.punctuation))
    words = t.split()

    total_words = len(words)
    if total_words == 0:
        # Éviter la division par zéro si un texte est vide
        continue

    # Compter la fréquence brute de chaque mot dans ce document
    freq = {}
    for w in words:
        freq[w] = freq.get(w, 0) + 1

    # Calcul du TF-IDF pour chaque mot du document
    for w, count in freq.items():
        if w in idf:  # Le mot existe dans le corpus IDF
            tf = count / total_words
            tf_idf = tf * idf[w]

            # Mise à jour de ScoreMap
            # Si hate, on décrémente
            # Si noHate, on incrémente
            if label == "hate":
                ScoreMap[w] = ScoreMap.get(w, 0) - tf_idf
            else:  # label == "noHate"
                ScoreMap[w] = ScoreMap.get(w, 0) + tf_idf

print("-------------------------- Étape 3 --------------------------")
print("Nombre de mots dans ScoreMap :", len(ScoreMap))

# Afficher quelques mots exemples
some_words = list(ScoreMap.keys())[:10]
for sw in some_words:
    print(f"{sw} => {ScoreMap[sw]}")

# Sauvegarde de ScoreMap dans un fichier JSON
with open("scoremap.json", "w", encoding="utf-8") as f:
    json.dump(ScoreMap, f, ensure_ascii=False)