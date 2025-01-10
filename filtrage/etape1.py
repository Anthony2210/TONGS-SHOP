import os
import csv
import glob

def charger_textes_labels():
    text_dir = "C:/Users/antoc/OneDrive/Bureau/Cours sup/2-MIASHS/L3/semestre 5/Programmation Web/TP/sampled_train/sampled_train"  # chemin vers votre dossier de textes
    annotations_file = "C:/Users/antoc/OneDrive/Bureau/Cours sup/2-MIASHS/L3/semestre 5/Programmation Web/TP/sampled_train/annotations_metadata.csv"  # chemin vers votre fichier CSV

    texts = []
    labels = []

    # Lecture du fichier CSV pour construire le label_map
    label_map = {}
    with open(annotations_file, 'r', encoding='utf-8') as f:
        reader = csv.DictReader(f)
        for row in reader:
            # file_id contient par ex "12834217_1"
            # label contient "hate" ou "noHate"
            file_id = row['file_id'].strip()
            label = row['label'].strip()

            # Le fichier correspondant sera "12834217_1.txt"
            label_map[file_id + ".txt"] = label

    # Maintenant, on parcourt tous les fichiers .txt du dossier all_fines
    file_paths = glob.glob(os.path.join(text_dir, "*.txt"))

    for file_path in file_paths:
        fname = os.path.basename(file_path)  # ex: "12834217_1.txt"

        # Vérifier si ce fichier a un label dans label_map
        if fname in label_map:
            with open(file_path, 'r', encoding='utf-8') as f:
                content = f.read().strip()
                texts.append(content)
                labels.append(label_map[fname])
    print("-------------------------- Étape 1 --------------------------")
    print("Nombre de documents :", len(texts))
    print("Nombre de labels :", len(labels))
    if len(texts) > 0:
        print("Exemple de texte :", texts[0])
        print("Label correspondant :", labels[0])
    return texts, labels