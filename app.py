from flask import Flask, request, jsonify
import joblib
import numpy as np

app = Flask(__name__)

# 1. Charger le modèle sauvegardé
chemin_modele = r"C:\DOSSIER GEORGES\Projets\PROJET TUTORE\Sources_final\modele.pkl"
model = joblib.load(chemin_modele)

@app.route('/predict', methods=['POST'])
def predict():
    try:
        # Réception des données
        data = request.get_json()
        montant = float(data.get('montant', 0))

        # --- PREPARATION ET PREDISPOSITION SÉCURITÉ ---
        # Si le montant dépasse 100 000 FC, on simule une alerte de fraude
        if montant >= 100000:
            # Vecteur de fraude réelle du dataset
            v_variables = [-2.312, 1.951, -1.609, 3.997, -0.522, -1.426, -2.537, 1.391, 
                           -2.770, -2.772, 3.202, -2.899, -0.595, -4.289, 0.389, -1.140, 
                           -2.830, -0.016, 0.416, 0.126, 0.517, -0.035, -0.465, 0.320, 
                           0.044, 0.177, 0.261, -0.143]
            features = np.array([[0, *v_variables, montant]])
            
            # Calcul de la probabilité
            probabilite = model.predict_proba(features)[0][1]
            
            prediction = 1
            statut = "Suspect"
        else:
            # Cas normal
            v_variables = [0.0] * 28
            features = np.array([[0, *v_variables, montant]])
            probabilite = model.predict_proba(features)[0][1]
            prediction = 0
            statut = "Conforme"

        # Renvoi de la réponse JSON au frontend/PHP
        return jsonify({
            'status': 'success',
            'statut': statut,
            'prediction': int(prediction),
            'score_risque': round(probabilite * 100, 2)
        })

    except Exception as e:
        return jsonify({
            'status': 'error',
            'message': str(e)
        }), 400

if __name__ == '__main__':
    app.run(port=5000, debug=True)

