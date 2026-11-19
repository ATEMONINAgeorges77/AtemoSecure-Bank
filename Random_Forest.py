# ==============================================================================
# PROJET MY BANK - DÉTECTION DE FRAUDE
# Modèle 3 : Random Forest sur Credit Card Fraud Detection 2023
# Fichier : Sources_final/Random_Forest.py
# ==============================================================================

import os
import pandas as pd
import numpy as np

from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import classification_report, confusion_matrix, roc_auc_score

# 1. Chargement des données
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
DATASET_PATH = os.path.join(BASE_DIR, 'creditcard_2023.csv')

if not os.path.exists(DATASET_PATH):
    DATASET_PATH = os.path.join(BASE_DIR, '..', 'creditcard_2023.csv')

print(" Chargement du dataset Credit Card 2023...")
df = pd.read_csv(DATASET_PATH)
print(f" Dataset chargé avec succès ! Dimensions : {df.shape[0]} lignes, {df.shape[1]} colonnes.\n")

# 2. Séparation des caractéristiques (X) et de la cible (y)
X = df.drop(columns=['id', 'Class'], errors='ignore')
y = df['Class']

# 3. Découpage Entraînement (80%) / Test (20%)
print(" Découpage des données (80% Train / 20% Test)...")
X_train, X_test, y_train, y_test = train_test_split(
    X, y, test_size=0.2, random_state=42, stratify=y
)
print(f"   - Jeu d'entraînement : {X_train.shape[0]} transactions")
print(f"   - Jeu de test         : {X_test.shape[0]} transactions\n")

# 4. Initialisation et Entraînement du modèle
print(" Entraînement de la Forêt Aléatoire (Random Forest)...")
# n_jobs=-1 permet de mobiliser tous les cœurs du processeur pour accélérer le calcul
model = RandomForestClassifier(n_estimators=100, random_state=42, n_jobs=-1)
model.fit(X_train, y_train)
print(" Entraînement terminé avec succès !\n")

# 5. Prédictions
y_pred = model.predict(X_test)
y_proba = model.predict_proba(X_test)[:, 1]

# 6. Évaluation des performances
print("=" * 60)
print(" RÉSULTATS : RANDOM FOREST")
print("=" * 60)

# Matrice de confusion
cm = confusion_matrix(y_test, y_pred)
print("\n--- Matrice de Confusion ---")
print(f"Vrais Négatifs (Légitimes bien classées)   : {cm[0][0]}")
print(f"Faux Positifs (Fausses alertes)            : {cm[0][1]}")
print(f"Faux Négatifs (Fraudes manquées)           : {cm[1][0]}")
print(f"Vrais Positifs (Fraudes bien détectées)    : {cm[1][1]}\n")

# Rapport de classification
print("--- Rapport de Classification ---")
print(classification_report(y_test, y_pred, digits=4))

# Score AUC-ROC
auc_score = roc_auc_score(y_test, y_proba)
print(f"Score AUC-ROC : {auc_score:.4f}")
print("=" * 60)

#Exportation du modèle pour l'API Flask
import joblib

joblib.dump(model,r"C:\DOSSIER GEORGES\Projets\PROJET TUTORE\Sources_final\modele_fraude.pkl")
print("modèle exporté avec succès")