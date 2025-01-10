import math
import string
from etape1 import charger_textes_labels

def calculIDF():
    texts, labels = charger_textes_labels()
    N = len(texts)

    # On va compter le df pour chaque mot
    doc_frequency = {}  # doc_frequency[mot] = nombre de documents contenant ce mot

    for text in texts:
        # Tokenisation simple
        # Convertir en minuscule
        t = text.lower()
        # Retirer la ponctuation (optionnel, exemple simple)
        t = t.translate(str.maketrans('', '', string.punctuation))
        words = t.split()

        # Pour éviter de compter plusieurs fois le même mot dans le même document
        unique_words = set(words)

        for w in unique_words:
            doc_frequency[w] = doc_frequency.get(w, 0) + 1

        # Maintenant, calcul de l'IDF
    idf = {}
    for w, df in doc_frequency.items():
        # IDF = log(N / df)
        # Pour éviter les divisions par zéro, on s'assure que df > 0
        # (df sera toujours > 0, puisque le mot existe dans doc_frequency)
        idf[w] = math.log(N / df)


    # Afficher le nombre de mots et un exemple
    print("-------------------------- Étape 2 --------------------------")
    print("Nombre de mots distincts:", len(idf))
    some_word = list(idf.keys())[0]
    print(f"Exemple: {some_word} => IDF = {idf[some_word]}")
    return idf

