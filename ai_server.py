from flask import Flask, request, jsonify
import numpy as np
import tensorflow as tf
import cv2

app = Flask(__name__)

# =====================
# LOAD MODELS
# =====================
fire_model = tf.keras.models.load_model(
    r"D:\Xampp\htdocs\neighbourguard\Models\fire_detection_model.h5"
)

animal_model = tf.keras.models.load_model(
    r"D:\Xampp\htdocs\neighbourguard\Models\stray_animal_model.h5"
)

# =====================
# TEXT CLASSIFIER
# =====================
def analyze_text(text):
    text = text.lower()

    fire_words = ["fire", "smoke", "burn", "flame", "blaze"]
    animal_words = ["dog", "cat", "stray", "animal", "barking"]

    fire_score = sum(w in text for w in fire_words)
    animal_score = sum(w in text for w in animal_words)

    if fire_score > animal_score and fire_score > 0:
        return "fire"
    elif animal_score > 0:
        return "stray_animal"
    else:
        return "unknown"

# =====================
# OPENCV PREPROCESS (FIXED)
# =====================
def preprocess_cv2(file, size):
    # read raw bytes
    file_bytes = np.frombuffer(file.read(), np.uint8)

    # decode image (BGR)
    img = cv2.imdecode(file_bytes, cv2.IMREAD_COLOR)

    # resize EXACTLY like training
    img = cv2.resize(img, size)

    # normalize
    img = img / 255.0

    # expand dims
    img = np.expand_dims(img, axis=0)

    return img

# =====================
# API
# =====================
@app.route("/analyze", methods=["POST"])
def analyze():

    text_input = request.form.get("text", "")
    text_detected = analyze_text(text_input)

    file = request.files["image"]

    # 🔥 IMPORTANT: preprocess for fire model
    img_fire = preprocess_cv2(file, fire_model.input_shape[1:3])

    # ⚠️ RESET FILE POINTER (VERY IMPORTANT)
    file.stream.seek(0)

    # 🐕 preprocess for animal model
    img_animal = preprocess_cv2(file, animal_model.input_shape[1:3])

    # =====================
    # PREDICTIONS
    # =====================
    fire_pred = float(fire_model.predict(img_fire)[0][0])
    animal_pred = float(animal_model.predict(img_animal)[0][0])

    print("🔥 FIRE:", fire_pred)
    print("🐕 ANIMAL:", animal_pred)

    # =====================
    # FINAL DECISION (PRIORITY BASED)
    # =====================
    if fire_pred > 0.5:
        image_detected = "fire"
        confidence = fire_pred

    elif animal_pred > 0.5:
        image_detected = "stray_animal"
        confidence = animal_pred

    else:
        image_detected = "unknown"
        confidence = max(fire_pred, animal_pred)

    # =====================
    # RESPONSE
    # =====================
    return jsonify({
        "text_detected": text_detected,
        "image_detected": image_detected,
        "trust_score": round(confidence * 100, 2),
        "status": "SUCCESS"
    })

# =====================
# RUN
# =====================
if __name__ == "__main__":
    app.run(debug=True, port=5000)